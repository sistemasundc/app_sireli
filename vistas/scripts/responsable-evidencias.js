$(document).ready(function () {
    const oficina_id = $("#oficina-id").data("oficina-id");

    listarPorOficinaPendientes(oficina_id);

    listarPorOficina(oficina_id, "");

    $('#myTab .nav-link').on('click', function () {
        const estado = $(this).data('estado');
        listarPorOficina(oficina_id, estado);
    });

    // Obtener evidencia_id de la URL y llamar listarEvidenciaId si existe
    const evidenciaId = getQueryParam("evidencia_id");
    if (evidenciaId) {
        listarEvidenciaIdPorOficina(evidenciaId);
        mostrarHistorialEvidencia(evidenciaId);
    }

    $(document).on("click", "#btnSubirEvidencia", function () {
        const evidenciaId = $(this).data("id");
        window.location.href = `subirevidencia.php?evidencia_id=${evidenciaId}`;
    });

    $(document).on("click", "#btnVerEvidencia", function () {
        const evidenciaId = $(this).data("id");
        window.location.href = `verevidencia.php?evidencia_id=${evidenciaId}`;
    });

    $("#formSubirEvidencia").on("submit", function (e) {
        e.preventDefault();
        console.log("Form submit initiated");
        subirEvidencia(e);
    });

});


function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function subirEvidencia(e) {
    const evidencia_id = document.getElementById("evidencia_id").value;
    const archivo = pond.getFile(); // Suponiendo que pond es tu instancia de la librería para gestionar los archivos

    // Verificar si un archivo ha sido seleccionado
    if (!archivo) {
        mostrarToastAdvertencia("Debes seleccionar un archivo antes de enviar.");
        return;
    }

    // Validar el tipo de archivo
    const archivoExtension = archivo.file.name.split('.').pop().toLowerCase();
    const tiposPermitidos = ['docx', 'pdf', 'zip', 'rar'];

    if (!tiposPermitidos.includes(archivoExtension)) {
        // Mostrar alerta y eliminar el archivo
        mostrarToastAdvertencia("El archivo debe ser de tipo DOCX, PDF, RAR o ZIP.");
        pond.removeFile();
        return;
    }

    // Validar el tamaño del archivo (200 MB como máximo)
    if (archivo.file.size > 200 * 1024 * 1024) {  // 20 MB
        mostrarToastAdvertencia("El archivo no debe superar los 200 MB.");
        pond.removeFile();
        return;
    }

    // Si el archivo es válido, proceder con la subida
    const formData = new FormData();
    formData.append("evidencia_id", evidencia_id);
    formData.append("archivo_presentacion", archivo.file);

    const modalSpinner = new bootstrap.Modal(document.getElementById('modalSpinner'));
    modalSpinner.show();

    $.ajax({
        url: "../controladores/evidencias.php?op=subirEvidenciaPresentacion",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                /* console.log("Response received: ", response); */
                const data = JSON.parse(response);

                if (data.success) {
                    // Mostrar toast de éxito después de subir la evidencia correctamente
                    mostrarToastExito("Evidencia registrada con éxito.");
                    pond.removeFile();  // Eliminar el archivo después de una carga exitosa
                    document.getElementById("formSubirEvidencia").reset();
                    vistaPrevia.style.display = 'none';

                    setTimeout(() => {
                        window.location.href = "./evidencias_pendientes.php";
                    }, 1200);

                } else {
                    mostrarToastAdvertencia(data.msg || "Hubo un problema al registrar la evidencia.");
                }

            } catch (error) {
                /* console.error("Error al procesar respuesta:", error); */
                mostrarToastAdvertencia("No se pudo interpretar la respuesta del servidor.");
            }
        },
        error: function (xhr, status, error) {
            modalSpinner.hide();
            mostrarToastAdvertencia("Error de conexión con el servidor.");
        }

    });
}



/* Listar evidencias Por Oficina */

function listarPorOficina(oficina_id, estado) {
    $.ajax({
        url: "../controladores/evidencias.php?op=listarPorOficina",
        type: "POST",
        dataType: "json",
        data: {
            oficina_id: oficina_id,
            estado: estado
        },
        success: function (data) {
            let evidenciaList = $("#mis-evidencias-list");
            evidenciaList.empty();

            if (data.aaData.length === 0) {
                evidenciaList.append('<p class="text-muted">No se encontraron evidencias para este filtro.</p>');
            } else {
                data.aaData.forEach(function (row) {
                    let fechaInicioTiempo = human_time_diff(row.fecha_registro);
                    let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                    let fechaFin = formatearFecha(row.fecha_plazo_fin);
                    let fechaSubsanacion = formatearFecha(row.fecha_subsanacion);


                    // Si existe subsanación, agregar línea adicional
                    let lineaSubsanacion = row.fecha_subsanacion
                        ? `<small class="badge ms-2 ${getBadgeClass(row.estado_revision)}"> <b>Fecha de Subsanación: </b> ${fechaSubsanacion} </small><br>`
                        : '';


                    let hoy = new Date();
                    let fechaFinDate = parseFechaFinal(row.fecha_plazo_fin);
                    let fechaSubsanacionDate = row.fecha_subsanacion ? parseFechaFinal(row.fecha_subsanacion) : null;

                    let botonAccion = '';
                    const estado = row.estado_revision;

                    // Solo si estado es Pendiente u Observado
                    const esCoordinador = (row.rol_oficina === "coordinador");

                    if (esCoordinador) {
                        // Coordinador SOLO seguimiento
                        botonAccion = `
        <a href="#" class="btn btn-sm btn-primary w-100" id="btnVerEvidencia" data-id="${row.evidencia_id}">
            <i class="feather icon-eye mx-1"></i> Seguimiento
        </a>
    `;
                    } else {
                        if (estado === 'Pendiente' || estado === 'Observado') {
                            let limiteFecha = new Date(Math.max(
                                fechaSubsanacionDate?.getTime?.() || 0,
                                fechaFinDate?.getTime?.() || 0
                            ));

                            if (hoy <= limiteFecha) {
                                // Mostrar botón para subir (y seguimiento si Observado)
                                botonAccion = `
            <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                <a href="#" class="btn btn-sm btn-dark w-100 mb-2" id="btnSubirEvidencia" data-id="${row.evidencia_id}">
                    <i class="ti ti-arrow-up-circle mx-1"></i> Subir Evidencia
                </a>
                ${estado === 'Observado' ? `
                <a href="#" class="btn btn-sm btn-primary w-100" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                    <i class="feather icon-eye mx-1"></i> Seguimiento
                </a>` : ''}
            </div>`;
                            } else {
                                // Plazo vencido
                                botonAccion = `
            <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                ${estado === 'Observado' ? `
                    <a href="#" class="btn btn-sm btn-primary w-100 mb-2" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                        <i class="feather icon-eye mx-1"></i> Seguimiento
                    </a>` : ''}
                <div class="col-auto d-flex align-items-center justify-content-center ps-4 text-center">
                    <span class="badge bg-danger">Plazo Vencido</span>
                </div>
            </div>`;
                            }
                        } else {
                            // Estados diferentes a Pendiente u Observado: solo seguimiento
                            botonAccion = `
        <div class="col-auto d-flex align-items-center justify-content-center ps-4">
            <a href="#" class="btn btn-sm btn-primary mb-2 w-100" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                <i class="feather icon-eye mx-1"></i> Seguimiento
            </a>
        </div>`;
                        }
                    }
                    let bloqueCoordinadores = '';

                    if (row.coordinadores_vinculados && row.coordinadores_vinculados.trim() !== '') {
                        bloqueCoordinadores = `
                    <p>
                        <b>Coordinadores:</b> <br> ${row.coordinadores_vinculados} <br>
                    </p>
                `;
                    } else {
                        bloqueCoordinadores = `
                    <p>
                        <b>Coordinadores:</b> <br> <span class="badge text-bg-light text-dark" >Sin Coordinadores</span> <br>
                    </p>
                `;
                    }
                    let cardHTML = `
                    <div class="card ticket-card w-100 h-100">
                        <div class="card-body h-100">
                            <div class="row h-100">
                                <div class="col d-flex flex-column justify-content-between border-end pe-4">
                                    <div class="popup-trigger">
                                        <div class="h5 font-weight-bold">
                                            ${row.evidencia_nombre}
                                            <small class="badge ms-2 ${getBadgeClass(row.estado_revision)}">${row.estado_revision}</small> ${lineaSubsanacion}
                                        </div>
                                        <div class="help-sm-hidden">
                                            <ul class="list-unstyled mt-2 mb-0 text-muted">
                                                <li class="d-sm-inline-block d-block mt-1">
                                                    <i class="ti ti-user"></i>
                                                    Creado por <b>${row.nomcompleto}</b> ${fechaInicioTiempo}.
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="mt-3">
                                            <p>
                                                <b>Responsables:</b> <br> ${row.oficinas_vinculadas} <br>
                                            </p>
                                            ${bloqueCoordinadores}
                                        </div>
                                     </div>
                                </div>
                                <div class="col-auto d-flex align-items-center justify-content-center ps-4">
                                    <div class="mt-2">
                                        <b>Fecha de Plazo Inicial:</b> ${fechaInicio} <br>
                                        <b>Fecha de Plazo Final:</b> ${fechaFin}
                                    </div>
                                </div>
                                <div class="col-auto d-flex flex-column align-items-center justify-content-center ps-4">
                                ${botonAccion}
                                </div>
                            </div>
                        </div>
                    </div>`;

                    evidenciaList.append(cardHTML);
                });
            }
        },
        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
}

/* Listar evidencias Por Oficina Pendientes */

function listarPorOficinaPendientes(oficina_id) {

    $.ajax({
        url: "../controladores/evidencias.php?op=listarPorOficinaPendientes",
        type: "POST",
        dataType: "json",
        data: { oficina_id: oficina_id },
        success: function (data) {

            if (!data || !data.aaData || data.aaData.length === 0) {
                $("#mis-evidencias-list-pendientes").html(`
                   <div class='alert alert-warning w-100
                   ' role='alert'>
                        <svg class='pc-icon'>
                            <use xlink:href='#custom-notification-status'></use>
                        </svg>
                        <strong>¡Atención!</strong> No tienes evidencias pendientes.
                    </div>
                `);
                return;
            }

            let evidenciaList = $("#mis-evidencias-list-pendientes");
            evidenciaList.empty();

            data.aaData.forEach(function (row) {
                let fechaInicioTiempo = human_time_diff(row.fecha_registro);
                let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                let fechaFin = formatearFecha(row.fecha_plazo_fin);
                let fechaSubsanacion = formatearFecha(row.fecha_subsanacion);
                // Si existe subsanación, agregar línea adicional
                let lineaSubsanacion = row.fecha_subsanacion
                    ? `<small class="badge ms-2 ${getBadgeClass(row.estado_revision)}"> <b>Fecha de Subsanación: </b> ${fechaSubsanacion} </small><br>`
                    : '';



                // Calcular la hora actual en UTC y ajustarla a la hora de Perú (UTC-5)
                let hoy = new Date();

                let fechaFinDate = parseFechaFinal(row.fecha_plazo_fin);
                let fechaSubsanacionDate = row.fecha_subsanacion ? parseFechaFinal(row.fecha_subsanacion) : null;

                // Determinar cuál fecha usar para el vencimiento (fecha de subsanación o fecha final)
                /* let limiteFecha = fechaSubsanacionDate || fechaFinDate; */
                let limiteFecha = new Date(Math.max(
                    fechaSubsanacionDate?.getTime?.() || 0,
                    fechaFinDate?.getTime?.() || 0
                ));
                // Calcular la diferencia en milisegundos
                let diffMilisegundos = limiteFecha - hoy;

                // Convertir la diferencia a horas y minutos exactos
                let diffHoras = diffMilisegundos / (1000 * 60 * 60); // Diferencia en horas exactas
                let diffHorasEnteras = Math.floor(diffHoras); // Obtener horas completas
                let diffMinutos = Math.round((diffHoras - diffHorasEnteras) * 60); // Obtener los minutos restantes

                /*                 console.log("Diferencia exacta en horas:", diffHoras);
                                 console.log("Diferencia en horas completas:", diffHorasEnteras);
                                 console.log("Diferencia en minutos:", diffMinutos); 
                 */
                // Si está a menos de 48 horas de vencer o ya venció, mostrar alerta
                if (diffHoras <= 48 && diffHoras > 0) {
                    // Mostrar el toast
                    mostrarToast(row, diffHorasEnteras, diffMinutos);
                }



                let botonAccion = '';



                const esCoordinador = (row.rol_oficina === "coordinador");

                if (esCoordinador) {
                    // Coordinador SOLO seguimiento
                    botonAccion = `
        <a href="#" class="btn btn-sm btn-primary w-100" id="btnVerEvidencia" data-id="${row.evidencia_id}">
            <i class="feather icon-eye mx-1"></i> Seguimiento
        </a>
    `;
                } else {
                    // Lógica actual para responsables
                    if (row.estado_revision === 'Pendiente') {
                        if (hoy <= limiteFecha) {
                            botonAccion = `
                <a href="#" class="btn btn-sm btn-dark w-100" id="btnSubirEvidencia" data-id="${row.evidencia_id}">
                    <i class="ti ti-arrow-up-circle mx-1"></i> Subir Evidencia
                </a>
            `;
                        } else {
                            botonAccion = `
                <div class="d-flex justify-content-center">
                    <span class="badge bg-danger">Plazo Vencido</span>
                </div>
            `;
                        }
                    } else if (row.estado_revision === 'Observado') {
                        if (hoy <= limiteFecha) {
                            botonAccion = `
                <a href="#" class="btn btn-sm btn-dark w-100 mb-2" id="btnSubirEvidencia" data-id="${row.evidencia_id}">
                    <i class="ti ti-arrow-up-circle mx-1"></i> Subir Evidencia
                </a>
                <a href="#" class="btn btn-sm btn-primary w-100" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                    <i class="feather icon-eye mx-1"></i> Seguimiento
                </a>
            `;
                        } else {
                            botonAccion = `
                <a href="#" class="btn btn-sm btn-primary w-100 mb-2" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                    <i class="feather icon-eye mx-1"></i> Seguimiento
                </a>
                <div class="w-100 text-center">
                    <span class="badge bg-danger">Plazo Vencido</span>
                </div>
            `;
                        }
                    } else {
                        botonAccion = `
            <a href="#" class="btn btn-sm btn-primary w-100" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                <i class="feather icon-eye mx-1"></i> Seguimiento
            </a>
        `;
                    }
                }

                let bloqueCoordinadores = '';

                if (row.coordinadores_vinculados && row.coordinadores_vinculados.trim() !== '') {
                    bloqueCoordinadores = `
                    <p>
                        <b>Coordinadores:</b> <br> ${row.coordinadores_vinculados} <br>
                    </p>
                `;
                } else {
                    bloqueCoordinadores = `
                    <p>
                        <b>Coordinadores:</b> <br> <span class="badge text-bg-light text-dark" >Sin Coordinadores</span> <br>
                    </p>
                `;
                }

                let cardHTML = `
                <div class="card ticket-card w-100 h-100">
                    <div class="card-body h-100">
                        <div class="row h-100">
                            <!-- Contenido de la izquierda -->
                            <div class="col d-flex flex-column justify-content-between border-end pe-4">
                                <div class="popup-trigger">
                                    <div class="h5 font-weight-bold">
                                        ${row.evidencia_nombre}
                                        <small class="badge ms-2 ${getBadgeClass(row.estado_revision)}">${row.estado_revision}</small>${lineaSubsanacion}
                                    </div>
                                    <div class="help-sm-hidden">
                                        <ul class="list-unstyled mt-2 mb-0 text-muted">
                                            <li class="d-sm-inline-block d-block mt-1">
                                                <i class="ti ti-user"></i>
                                                Creado por <b>${row.nomcompleto}</b> ${fechaInicioTiempo}.
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="mt-3">
                                        <p>
                                            <b>Responsables:</b> <br> ${row.oficinas_vinculadas} <br>
                                        </p>
                                         ${bloqueCoordinadores}
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-auto d-flex align-items-center justify-content-center ps-4">
                                    <div class="mt-2">
                                
                                        <b>Fecha de Plazo Inicial:</b> ${fechaInicio} <br>
                                        <b>Fecha de Plazo Final:</b> ${fechaFin}
                                    </div>
                            </div>
                            <div class="col-auto d-flex flex-column align-items-center justify-content-center ps-4">
                            
                                ${botonAccion}
                               
                            </div>
                        </div>
                    </div>
                </div>`;



                evidenciaList.append(cardHTML);

            });

        },
        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
}
/* Listar datos por evidencia */

function listarEvidenciaIdPorOficina(evidenciaId) {
    $.ajax({
        url: "../controladores/evidencias.php?op=listarEvidenciaIdPorOficina",
        type: "POST",
        dataType: "json",
        data: { evidencia_id: evidenciaId },
        success: function (response) {
            if (response.aaData.length > 0) {
                const cardData = response.aaData[0];
                let fechaInicio = formatearFecha(cardData.fecha_plazo_inicio);
                let fechaFin = formatearFecha(cardData.fecha_plazo_fin);

                $("#evidencia_id").val(cardData.evidencia_id);
                $("#evidencia-estado").html(`
                    <span class="badge ms-2 ${getBadgeClass(cardData.estado_revision)}">
                        ${cardData.estado_revision}
                    </span>
                `);

                $("#evidencia-nombre").text(cardData.evidencia_nombre);
                $("#usuario-evidencia").text(`Creado por: ${cardData.nomcompleto}`);
                $("#fecha-registro").text(`Registrado: ${cardData.fecha_registro}`);
                $("#indicador-nombre").text(cardData.indicador_nombre);
                $("#medio-nombre").text(cardData.medio_nombre);
                $("#evidencia-consideraciones").html(cardData.evidencia_consideraciones);
                $("#responsables").html(cardData.oficinas_vinculadas);
                let EvidenciaCoordinadores = '';

                if (cardData.coordinadores_vinculados && cardData.coordinadores_vinculados.trim() !== '') {
                    EvidenciaCoordinadores = `
                                ${cardData.coordinadores_vinculados} 
                                
                            `;
                } else {
                    EvidenciaCoordinadores = `
                               <span class="badge text-bg-light text-dark" >Sin Coordinadores</span>
                              
                            `;
                }
                $("#coordinadores").html(EvidenciaCoordinadores);
                $("#estadoRevision").text(cardData.estado_revision);
                $("#evidencia-usuario").text(cardData.nomcompleto);
                $("#fecha-creacion").text(cardData.fecha_registro);
                if (cardData.fecha_subsanacion) {
                    $("#fecha-subsanacion")
                        .html(` - <span class="badge ms-2 ${getBadgeClass(cardData.estado_revision)}"><b>Fecha de Subsanación: </b> ${formatearFecha(cardData.fecha_subsanacion)}</span>`)
                        .show();
                } else {
                    $("#fecha-subsanacion").hide();
                }
                let gradoNombre = cardData.grado_nombre;

                // Verificar si se debe mostrar "Por revisar"
                if (
                    !gradoNombre ||
                    cardData.estado_revision === 'Enviado' ||
                    cardData.estado_revision === 'En Revision'
                ) {
                    gradoNombre = 'Por Revisar';
                }

                const badgeClass = getGradoBadgeClass(gradoNombre);

                $("#evidencia-cumplimiento").html(`<span class="badge ${badgeClass}">${gradoNombre}</span>`);

                $("#evidencia-plazo").text(`${fechaInicio} - ${fechaFin}`);
                $("#evidencia-nombre").text(cardData.evidencia_nombre);
            } else {
                /* alert("Evidencia no encontrada o no tienes acceso."); */
                window.location.href = "evidencias_pendientes.php";
            }
        },
        error: function () {
            alert("Error al obtener la evidencia.");
        }
    });
}

function mostrarHistorialEvidencia(evidenciaId) {
    $.ajax({
        url: "../controladores/evidencias.php?op=mostrarHistorialEvidenciaId",
        type: "POST",
        dataType: "json",
        data: { evidencia_id: evidenciaId },
        success: function (response) {
            if (response.aaData.length > 0) {
                const historialData = response.aaData;
                let rows = "";
                let archivosAdjuntos = "";

                historialData.forEach((data, index) => {
                    // Crear las filas para la tabla de flujo de envío
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${data.historial_id}</td>
                            <td>${data.emisor_oficina}</td>
                            <td>${data.fecha_emision}</td>
                            <td>${data.receptor_oficina}</td>
                            <td>${data.fecha_recepcion ?? ''}</td>
                            <td class="text-center">
                                <div class="mb-1 d-flex align-items-center justify-content-center">
                                    <span class="badge ${getBadgeClassEstado(data.estado2)} me-1">
                                        ${data.estado2 === 'Finalizado' ? 'Revisado' : data.estado2}
                                    </span>

                                     ${data.archivo_observacion != null ? (() => {
                            const ext = data.archivo_observacion.split('.').pop().toLowerCase();
                            if (ext === 'pdf') {
                                return `
                                <a href="#" class="ms-1" title="Ver PDF" onclick="verPDF('${data.archivo_observacion}')">
                                    <i class="ti ti-eye f-18 text-danger"></i>
                                </a>
                            `;
                            } else {
                                return `
                                <a href="descargar.php?archivo=${data.archivo_observacion}" class="ms-1" title="Descargar Observación" download>
                                    <i class="ti ti-download f-18"></i>
                                </a>
                            `;
                            }
                        })() : ''}
                                </div>
                            </td>
                            <td>
                                ${(data.observaciones && data.observaciones.trim()) ? data.observaciones : '<small>Sin comentarios</small>'}
                            </td>




                        </tr>
                    `;

                    // Validar que el archivo exista
                    if (data.archivo_presentacion && data.archivo_presentacion.trim() !== '') {
                        const archivo = data.archivo_presentacion.trim();
                        const extension = archivo.split('.').pop().toLowerCase();
                        const archivoUrl = `descargar.php?archivo=${archivo}`;

                        let botonAccion = '';

                        if (extension === 'pdf') {
                            // Botón para ver en modal
                            botonAccion = `
                            <a href="#" class="btn-link-success" onclick="verPDF('${archivo}')">
                                <i class="ti ti-eye f-18"></i> Visualizar PDF
                            </a>`;
                        } else {
                            // Botón de descarga
                            botonAccion = `
                            <a href="${archivoUrl}" class="btn-link-success" download>
                                <i class="ti ti-download f-18"></i> Descargar
                            </a>`;
                        }

                        const ext = archivo.split('.').pop().toLowerCase();
                        let icono = '';

                        switch (ext) {
                            case 'pdf':
                                icono = `<i class="fas fa-file-pdf" style="color: #d9534f;"></i>`; // rojo
                                break;
                            case 'docx':
                                icono = `<i class="fas fa-file-word" style="color: #0d6efd;"></i>`; // azul
                                break;
                            case 'zip':
                            case 'rar':
                                icono = `<i class="fas fa-file-archive" style="color: #6c757d;"></i>`; // gris
                                break;
                            default:
                                icono = `<i class="fas fa-file" style="color: #6c757d;"></i>`; // archivo genérico
                        }

                        archivosAdjuntos += `
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-6 d-flex align-items-center mb-2 mb-md-0">
                                        <span class="fw-bold me-4 text-nowrap">N° Historial: ${data.historial_id}</span>
                                        ${icono}
                                        <span class="text-truncate d-inline-block ms-2" style="max-width: 300px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;" title="${archivo}">
                                            ${archivo}
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-6 text-md-end text-start">
                                        ${botonAccion}
                                    </div>
                                </div>
                            </li>
                        `;

                    }
                });

                // Insertar las filas generadas en la tabla de flujo de envío
                $("#tabla-historial tbody").html(rows);

                // Insertar los archivos adjuntos en la lista
                if (archivosAdjuntos === "") {
                    $("#archivos-adjuntos").html('<li class="list-group-item">No se encontraron archivos.</li>');
                } else {
                    $("#archivos-adjuntos").html(archivosAdjuntos);
                }
            } else {
                // Si no se encuentran registros en la tabla
                $("#tabla-historial tbody").html('<tr><td colspan="7" class="text-center">No se encontraron registros.</td></tr>');
                // Si no hay archivos adjuntos
                $("#archivos-adjuntos").html('<li class="list-group-item">No se encontraron archivos.</li>');
            }
        },
        error: function () {
            alert("Error al obtener el historial de evidencia.");
        }
    });
}


function verPDF(nombreArchivo) {
    const url = `descargar.php?archivo=${nombreArchivo}`;
    document.getElementById('iframePDF').src = url;
    const modal = new bootstrap.Modal(document.getElementById('modalPDF'));
    modal.show();
}


/* Mostrar Toast de Éxito */

function mostrarToastExito(message) {
    $("#mensajefloat").html(message);
    const toastLiveExample = document.getElementById('liveToast');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
    toastBootstrap.show();
}

/* Mostrar Toast de Error */

function mostrarToastAdvertencia(message) {
    $("#mensajefloatwarning").html(message);
    const toastLiveExample = document.getElementById('liveToastwarning');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
    toastBootstrap.show();
}

/* Nuevo Foramto de Fecha */
function formatearFecha(fechaStr) {
    if (!fechaStr) return '';

    const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    const [anio, mes, dia] = fechaStr.split('-'); // evita problemas de zona horaria

    return `${dia} ${meses[parseInt(mes, 10) - 1]} ${anio}`;
}

/* Calcular el tiempo desde el registro de la Evidencia */

function human_time_diff(fecha) {
    const ahora = new Date();

    const opciones = { timeZone: "America/Lima" };

    const ahoraPeru = new Date(ahora.toLocaleString("en-US", opciones));

    const fechaPlazo = new Date(new Date(fecha).toLocaleString("en-US", opciones));

    if (fechaPlazo > ahoraPeru) {
        return `hace 0 segundos`;
    }

    const diferencia = ahoraPeru - fechaPlazo;

    const segundos = Math.floor(diferencia / 1000);
    const minutos = Math.floor(segundos / 60);
    const horas = Math.floor(minutos / 60);
    const dias = Math.floor(horas / 24);
    const meses = Math.floor(dias / 30);
    const años = Math.floor(meses / 12);

    if (años > 0) {
        return `hace ${años} año${años > 1 ? 's' : ''}`;
    } else if (meses > 0) {
        return `hace ${meses} mes${meses > 1 ? 'es' : ''}`;
    } else if (dias > 0) {
        return `hace ${dias} día${dias > 1 ? 's' : ''}`;
    } else if (horas > 0) {
        return `hace ${horas} hora${horas > 1 ? 's' : ''}`;
    } else if (minutos > 0) {
        return `hace ${minutos} minuto${minutos > 1 ? 's' : ''}`;
    } else {
        return `hace ${segundos} segundo${segundos > 1 ? 's' : ''}`;
    }
}

/* Clase por estado */

function getBadgeClass(estado) {
    switch (estado) {
        case 'Pendiente':
            return 'bg-light-success';
        case 'Enviado':
            return 'bg-light-warning';
        case 'En Revision':
            return 'bg-light-warning';
        case 'Observado':
            return 'bg-light-danger';
        case 'Finalizado':
            return 'bg-light-primary';
        default:
            return 'bg-light-info';
    }
}

function getBadgeClassEstado(estado) {
    switch (estado) {
        case 'Recibido':
            return 'bg-light-success';
        case 'Sin Recibir':
            return 'bg-light-warning';
        case 'Derivado':
            return 'bg-light-warning';
        case 'Finalizado':
            return 'bg-light-primary';
        default:
            return 'bg-light-info';
    }
}

function getGradoBadgeClass(gradoNombre) {
    switch (gradoNombre) {
        case 'Si Cumple':
            return 'bg-success';
        case 'No Cumple':
            return 'bg-danger';
        case 'Cumple Parcialmente':
            return 'bg-warning text-dark';
        case 'Por Revisar':
            return 'bg-secondary';
        default:
            return 'bg-info';
    }
}

function parseFechaFinal(diaStr) {
    // Retorna una fecha en formato YYYY-MM-DDT23:59:59-05:00 (hora peruana)
    return new Date(`${diaStr}T23:59:59-05:00`);
}

function mostrarToast(row, diffHorasEnteras, diffMinutos) {
    // Verificar si el contenedor existe
    if (!$("#toast-container").length) {
        console.log("Contenedor de Toast no encontrado");
        return;
    }

    // Crear el contenido del toast
    let toastHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <b>¡Alerta de Vencimiento!</b> <br> La evidencia <b>"${row.evidencia_nombre}"</b> está por vencer en ${diffHorasEnteras} hora(s) y ${diffMinutos} minuto(s).
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    // Agregar el toast al contenedor
    $("#toast-container").append(`
        <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" style="margin-bottom: 10px;">
            ${toastHTML}
        </div>
    `);

    // Inicializar y mostrar el toast
    let toastElement = $(".toast").last()[0];  // Seleccionar el último toast agregado
    if (toastElement) {
        let toast = new bootstrap.Toast(toastElement);
        toast.show(); // Mostrar el toast
        console.log("Toast mostrado:", row.evidencia_nombre);

    } else {
        console.log("Error al inicializar el Toast");
    }
}
