let choicesOficinasFiltro;
$(document).ready(function () {

    listarEvidenciasPorRecibir();
    listarEvidenciasPorRevisar();
    listarTodasEvidencias('', '');


    const evidenciaId = getQueryParam("evidencia_id");

    if (evidenciaId) {
        listarEvidenciaId(evidenciaId);
        mostrarHistorialEvidencia(evidenciaId);
    }

    $(document).on("click", "#btnVerEvidencia", function () {
        const evidenciaId = $(this).data("id");
        window.location.href = `verevidencia.php?evidencia_id=${evidenciaId}`;
    });


    $(document).on("click", ".btnRecibirEvidencia", function () {
        const historialId = $(this).data("id");
        recibirEvidencia(historialId);
    });

    $(document).on("click", ".btnRevisarEvidencia", function () {
        const evidenciaId = $(this).data("id");
        window.location.href = `verevidencia.php?evidencia_id=${evidenciaId}`;
    });

    $.post("../controladores/oficinas.php?op=seleccionarOficinaFiltro", function (r) {
        $("#oficina_id").html(r);

        if (choicesOficinasFiltro) {
            choicesOficinasFiltro.destroy();
        }

        choicesOficinasFiltro = new Choices('#oficina_id', {
            searchEnabled: true,
            itemSelectText: 'Presionar',
            searchResultLimit: -1,
            renderChoiceLimit: -1,
            shouldSort: false

        });
    });;

    $(document).on("click", ".btnCalificarEvidencia", function () {
        const historialId = $(this).data("id");
        $("#historial_id").val(historialId); // establece en el input oculto

        $('#RegistrarCalificacionModal').modal('show');

        // Cargar grados
        $.post("../controladores/evidencias.php?op=seleccionarGrado", function (r) {
            $("#grado_id").html(r);
        });
    });

    $(document).on("click", "#applyFilters", function () {

        var oficina_id = $('#oficina_id').val();
        var estado = $('#estadoFilter').val();

        listarTodasEvidencias(oficina_id, estado);
        limpiar_filtro()

    });

    $("#btnVerTodos").on("click", function () {

        $("#oficina_id").val("");
        $("#estadoFilter").val("");
        listarTodasEvidencias('', '');
    });

    $(document).on("click", "#applyFiltersPorRecibir", function () {
        var oficina_id = $('#oficina_id').val();

        listarEvidenciasPorRecibir(oficina_id);
        limpiar_filtro();

    });

    $("#btnVerTodosPorRecibir").on("click", function () {

        $("#oficina_id").val("");
        listarEvidenciasPorRecibir('');
    });

    document.getElementById('archivo_observacion').addEventListener('change', function () {
        const file = this.files[0];

        const extensionesPermitidas = ['pdf', 'doc', 'docx', 'rar', 'zip'];
        const extension = file?.name.split('.').pop().toLowerCase();

        if (!file) return;

        // Validar tipo de archivo
        if (!extensionesPermitidas.includes(extension)) {
            mostrarToastAdvertencia('Tipo de archivo no permitido. Solo PDF, Word, RAR o ZIP.');
            this.value = '';
            return;
        }

        // Validar tamaño máximo (20 MB)
        if (file.size > 100 * 1024 * 1024) {
            mostrarToastAdvertencia('Tamaño excedido. El archivo no debe superar los 100 MB.');
            this.value = '';
            return;
        }

        mostrarToastExito('Archivo cargado correctamente.');
    });



});

/* document.getElementById('applyFilters').addEventListener('click', function (e) {
    var oficina_id = document.getElementById('oficina_id').value;
    var estado = document.getElementById('estadoFilter').value;

    // Llamar a la función que lista todas las evidencias, pasando los filtros
    listarTodasEvidencias(oficina_id, estado);

    // Prevenir la propagación del evento para que no interfiera con otros botones
    e.stopPropagation();  // Esto asegura que el evento no afecte a otros elementos
}); */


function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function limpiar_filtro() {

    if (choicesOficinasFiltro) {
        choicesOficinasFiltro.destroy();
    }

    $("#oficina_id").val("").trigger('change');

    choicesOficinasFiltro = new Choices('#oficina_id', {
        searchEnabled: true,
        itemSelectText: 'Presionar',
        searchResultLimit: 5,
        renderChoiceLimit: 5,
        shouldSort: false
    });

    $("#estadoFilter").val("").trigger('change');

}

function listarEvidenciasPorRecibir(oficina_id) {
    const filtrosAplicados = [];

    if (oficina_id) {
        filtrosAplicados.push(`Oficina: ${$("#oficina_id option:selected").text()}`);
    }


    if (filtrosAplicados.length > 0) {
        $("#filtros-aplicados").show();
        $("#filtros-mostrados").html(filtrosAplicados.map(filtro => `<span class="badge text-bg-primary mt-0">${filtro}</span>`).join(" "));
    } else {
        $("#filtros-aplicados").hide();
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=listarEvidenciasPorRecibir", // URL del controlador
        type: "POST",
        dataType: "json",
        data: {
            oficina_id: oficina_id // Enviar el parámetro oficina_id
        },
        success: function (data) {
            console.log("Respuesta del servidor:", data); // Mostrar la respuesta en consola para depuración

            // Verificar si no hay datos
            if (!data || !data.aaData || data.aaData.length === 0) {
                $("#mis-evidencias-list-recibir").html(` 
                   <div class='alert alert-info w-100' role='alert'>
                        <svg class='pc-icon'>
                            <use xlink:href='#custom-message-2'></use>
                        </svg>
                        <strong>¡Atención!</strong> No hay evidencias por recibir.
                    </div>
                `);

                // Ocultar la paginación y el mensaje si no hay datos
                $("#paginacion-container").hide();
                $("#mensaje-registros-2").hide();
                return;
            }


            $("#paginacion-container").show();
            $("#mensaje-registros-2").show();

            let evidenciaList = $("#mis-evidencias-list-recibir");
            evidenciaList.empty();

            let evidencias = data.aaData;
            const itemsPorPagina = 20;
            const totalItems = data.iTotalRecords;
            let paginaActual = 1;

            // Función para mostrar los registros de una página
            function mostrarPaginaPR(pagina) {
                evidenciaList.empty();
                const inicio = (pagina - 1) * itemsPorPagina;
                const fin = inicio + itemsPorPagina;
                const evidenciasPagina = evidencias.slice(inicio, fin);

                if (evidenciasPagina.length === 0) {
                    evidenciaList.append('<p class="text-muted" style="text-align: center; width: 100%;">No se encontraron evidencias para este filtro.</p>');
                } else {
                    evidenciasPagina.forEach(function (row) {
                        let fechaEnvioTiempo = human_time_diff(row.fecha_emision);
                        let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                        let fechaFin = formatearFecha(row.fecha_plazo_fin);
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
                                                <small class="badge ms-2 ${getBadgeClass(row.estado_revision)}">${row.estado_revision}</small>
                                            </div>
                                            <div class="help-sm-hidden">
                                                <ul class="list-unstyled mt-2 mb-0 text-muted">
                                                    <li class="d-sm-inline-block d-block mt-1">
                                                        <i class="ti ti-user"></i>
                                                        Enviado por <b>${row.emisor_nombre}</b> de <b>${row.oficina_origen}</b> ${fechaEnvioTiempo}.
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
                                    <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                                        <a href="#" class="btn btn-success d-inline-flex btnRecibirEvidencia" data-id="${row.historial_id}">
                                            <i class="ti ti-file-check mx-1"></i>Recepcionar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        evidenciaList.append(cardHTML);
                    });
                }

                // Mostrar mensaje de los registros actuales
                const desde = (pagina - 1) * itemsPorPagina + 1;
                const hasta = Math.min(pagina * itemsPorPagina, totalItems);
                const mensaje = `Mostrando ${desde} - ${hasta} de ${totalItems} registros`;
                $("#mensaje-registros-2").text(mensaje);
            }


            function crearPaginacionPR() {
                const totalPaginas = Math.ceil(totalItems / itemsPorPagina);

                if (totalPaginas > 1) {
                    const paginacion = $("#paginacion-evidencias-por-recibir");
                    paginacion.empty();

                    for (let i = 1; i <= totalPaginas; i++) {
                        let active = i === paginaActual ? "active" : "";
                        paginacion.append(`<li class="page-item ${active} mt-2"><a class="page-link" href="#">${i}</a></li>`);
                    }

                    $(".page-link").click(function (e) {
                        e.preventDefault();
                        paginaActual = parseInt($(this).text());
                        mostrarPaginaPR(paginaActual);
                    });

                    $("#paginacion-container").show();
                } else {
                    $("#paginacion-container").hide();
                }
            }

            mostrarPaginaPR(paginaActual);
            crearPaginacionPR();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert("Error al cargar las evidencias");
        }
    });
}

function listarTodasEvidencias(oficina_id, estado) {
    const filtrosAplicados = [];

    if (oficina_id) {
        filtrosAplicados.push(`Oficina: ${$("#oficina_id option:selected").text()}`);
    }

    if (estado) {
        filtrosAplicados.push(`Estado: ${estado}`);
    }

    if (filtrosAplicados.length > 0) {
        $("#filtros-aplicados").show();
        $("#filtros-mostrados").html(filtrosAplicados.map(filtro => `<span class="badge text-bg-primary mt-0">${filtro}</span>`).join(" "));
    } else {
        $("#filtros-aplicados").hide();
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=listarTodasEvidencias",
        type: "POST",
        dataType: "json",
        data: {
            oficina_id: oficina_id,
            estado: estado
        },
        success: function (data) {
            let evidenciaList = $("#mis-evidencias-list-todas");
            evidenciaList.empty();

            let evidencias = data.aaData;
            const itemsPorPagina = 20;
            const totalItems = evidencias.length;
            let paginaActual = 1;


            function mostrarPagina(pagina) {
                evidenciaList.empty();
                const inicio = (pagina - 1) * itemsPorPagina;
                const fin = inicio + itemsPorPagina;
                const evidenciasPagina = evidencias.slice(inicio, fin);

                // Si no hay resultados
                if (evidenciasPagina.length === 0) {
                    evidenciaList.append('<p class="text-muted" style="text-align: center; width: 100%;">No se encontraron evidencias para este filtro.</p>');

                } else {
                    evidenciasPagina.forEach(function (row) {
                        let fechaInicioTiempo = human_time_diff(row.fecha_registro);
                        let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                        let fechaFin = formatearFecha(row.fecha_plazo_fin);
                        let fechaSubsanacion = formatearFecha(row.fecha_subsanacion);

                        let lineaSubsanacion = row.fecha_subsanacion
                            ? `<small class="badge ms-2 ${getBadgeClass(row.estado_revision)}"> <b>Fecha de Subsanación: </b> ${fechaSubsanacion} </small><br>`
                            : '';

                        let hoy = new Date();
                        let fechaFinDate = parseFechaFinal(row.fecha_plazo_fin);
                        let fechaSubsanacionDate = row.fecha_subsanacion ? parseFechaFinal(row.fecha_subsanacion) : null;

                        let botonAccion = '';
                        const estado = row.estado_revision;
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

                        // Solo si estado es Pendiente u Observado
                        if (estado === 'Pendiente' || estado === 'Observado') {
                            let limiteFecha = new Date(Math.max(
                                fechaSubsanacionDate?.getTime?.() || 0,
                                fechaFinDate?.getTime?.() || 0
                            ));


                            if (hoy <= limiteFecha) {
                                botonAccion = `
                                    <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                                        <a href="#" class="btn btn-sm btn-primary w-100 mb-2" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                                            <i class="feather icon-eye mx-1"></i> Seguimiento
                                        </a>
                                    </div>`;
                            } else {
                                // Plazo vencido
                                botonAccion = `
                                    <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                                        <a href="#" class="btn btn-sm btn-primary w-100 mb-2" id="btnVerEvidencia" data-id="${row.evidencia_id}">
                                            <i class="feather icon-eye mx-1"></i> Seguimiento
                                        </a>
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

                // Mostrar mensaje de los registros actuales
                const desde = (pagina - 1) * itemsPorPagina + 1;
                const hasta = Math.min(pagina * itemsPorPagina, totalItems);
                const mensaje = `Mostrando ${desde} - ${hasta} de ${totalItems} registros`;
                $("#mensaje-registros").text(mensaje);  // Mostrar el mensaje en un contenedor
            }

            // Paginación
            function crearPaginacion(totalItems, itemsPorPagina) {
                const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
                const paginacion = $("#paginacion-evidencias");
                paginacion.empty();

                for (let i = 1; i <= totalPaginas; i++) {
                    let active = i === paginaActual ? "active" : "";
                    paginacion.append(`<li class="page-item ${active}"><a class="page-link" href="#">${i}</a></li>`);
                }

                // Agregar evento de clic a las páginas
                $(".page-link").on("click", function (e) {
                    e.preventDefault();
                    paginaActual = parseInt($(this).text());
                    mostrarPagina(paginaActual);  // Mostrar la página seleccionada
                });
            }

            // Inicializar la paginación
            mostrarPagina(paginaActual);
            crearPaginacion(totalItems, itemsPorPagina);
        },
        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
}





function recibirEvidencia(historial_id) {
    console.log(historial_id);
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Estás seguro?</span>' +
            '</div>',
        text: "Vas a marcar esta evidencia como recibida.",
        showCancelButton: true,
        confirmButtonColor: "#4680ff",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, recibir",
        cancelButtonText: "No",
        customClass: {
            popup: 'rounded-4 shadow p-3 custom-swal-width',
            confirmButton: 'btn btn-primary btn-sm px-3 fs-8',
            cancelButton: 'btn btn-secondary btn-sm px-3 fs-8',
            icon: 'm-1'
        },
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../controladores/evidencias.php?op=recibirEvidencia", { historial_id: historial_id }, function (response) {
                const data = JSON.parse(response);
                if (data.success) {
                    mostrarToastExito("Evidencia recibida correctamente.");
                    listarEvidenciasPorRecibir();
                    setTimeout(() => {
                        window.location.href = 'evidencias_revisar.php';
                    }, 300);
                } else {
                    mostrarToastAdvertencia("No se pudo recibir la evidencia.");
                }
            });
        }
    });
}
function listarEvidenciasPorRevisar() {
    $.ajax({
        url: "../controladores/evidencias.php?op=listarEvidenciasPorRevisar",
        type: "POST",
        dataType: "json",
        success: function (data) {

            if (!data || !data.aaData || data.aaData.length === 0) {
                $("#mis-evidencias-list-revisar").html(`
                   <div class='alert alert-info w-100' role='alert'>
                        <svg class='pc-icon'>
                            <use xlink:href='#custom-message-2'></use>
                        </svg>
                        <strong>¡Atención!</strong> No hay evidencias por revisar.
                    </div>
                `);
                $("#paginacion-container").hide();
                return;
            }

            let evidenciaList = $("#mis-evidencias-list-revisar");
            evidenciaList.empty();

            let evidencias = data.aaData;
            const itemsPorPagina = 10;
            const totalItems = data.iTotalRecords;
            let paginaActual = 1;

            // Función para mostrar los registros de una página
            function mostrarPaginaPR(pagina) {
                evidenciaList.empty();
                const inicio = (pagina - 1) * itemsPorPagina;
                const fin = inicio + itemsPorPagina;
                const evidenciasPagina = evidencias.slice(inicio, fin);

                if (evidenciasPagina.length === 0) {
                    evidenciaList.append('<p class="text-muted" style="text-align: center; width: 100%;">No se encontraron evidencias para este filtro.</p>');
                } else {
                    evidenciasPagina.forEach(function (row) {
                        let fechaEnvioTiempo = human_time_diff(row.fecha_emision);
                        let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                        let fechaFin = formatearFecha(row.fecha_plazo_fin);
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
                                                <small class="badge ms-2 ${getBadgeClass(row.estado_revision)}">${row.estado_revision}</small>
                                            </div>
                                            <div class="help-sm-hidden">
                                                <ul class="list-unstyled mt-2 mb-0 text-muted">
                                                    <li class="d-sm-inline-block d-block mt-1">
                                                        <i class="ti ti-user"></i>
                                                        Enviado por <b>${row.emisor_nombre}</b> de <b>${row.oficina_origen}</b> ${fechaEnvioTiempo}.
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
                                    <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                                        <a href="#" class="btn btn-primary d-inline-flex btnRevisarEvidencia" data-id="${row.evidencia_id}">
                                            <i class="ti ti-circle-check mx-1"></i>Revisar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        evidenciaList.append(cardHTML);
                    });
                }

                // Mostrar mensaje de los registros actuales
                const desde = (pagina - 1) * itemsPorPagina + 1;
                const hasta = Math.min(pagina * itemsPorPagina, totalItems);
                const mensaje = `Mostrando ${desde} - ${hasta} de ${totalItems} registros`;
                $("#mensaje-registros-revisar").text(mensaje);
            }

            function crearPaginacionPR() {
                const totalPaginas = Math.ceil(totalItems / itemsPorPagina);

                if (totalPaginas > 1) {
                    const paginacion = $("#paginacion-evidencias-por-revisar");
                    paginacion.empty();

                    for (let i = 1; i <= totalPaginas; i++) {
                        let active = i === paginaActual ? "active" : "";
                        paginacion.append(`<li class="page-item ${active} mt-3"><a class="page-link" href="#">${i}</a></li>`);
                    }

                    $(".page-link").click(function (e) {
                        e.preventDefault();
                        paginaActual = parseInt($(this).text());
                        mostrarPaginaPR(paginaActual);
                    });

                    $("#paginacion-container").show();
                } else {
                    $("#paginacion-container").hide();
                }
            }

            mostrarPaginaPR(paginaActual);
            crearPaginacionPR();

            $("#mensaje-registros-revisar").show();
        },
        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
}

/* function listarEvidenciasPorRevisar() {
    $.ajax({
        url: "../controladores/evidencias.php?op=listarEvidenciasPorRevisar",
        type: "POST",
        dataType: "json",
        success: function (data) {
            console.log("Respuesta del servidor:", data); // Muestra toda la respuesta del servidor

            if (!data || !data.aaData || data.aaData.length === 0) {

                $("#mis-evidencias-list-revisar").html(`
                   <div class='alert alert-info w-100
                   ' role='alert'>
                        <svg class='pc-icon'>
                            <use xlink:href='#custom-message-2'></use>
                        </svg>
                        <strong>¡Atención!</strong> No hay evidencias por revisar.
                    </div>
                `);
                return;
            }

            let evidenciaList = $("#mis-evidencias-list-revisar");
            evidenciaList.empty();

            data.aaData.forEach(function (row) {
                let fechaEnvioTiempo = human_time_diff(row.fecha_emision);
                let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                let fechaFin = formatearFecha(row.fecha_plazo_fin);
                let cardHTML = `
                <div class="card ticket-card w-100 h-100">
                    <div class="card-body h-100">
                        <div class="row h-100">
                            <div class="col d-flex flex-column justify-content-between border-end pe-4">
                                <div class="popup-trigger">
                                    <div class="h5 font-weight-bold">
                                        ${row.evidencia_nombre}
                                        <small class="badge ms-2 ${getBadgeClass(row.estado_revision)}">${row.estado_revision}</small>
                                    </div>
                                    <div class="help-sm-hidden">
                                        <ul class="list-unstyled mt-2 mb-0 text-muted">
                                            <li class="d-sm-inline-block d-block mt-1">
                                                <i class="ti ti-user"></i>
                                                Enviado por <b>${row.emisor_nombre}</b> de <b>${row.oficina_origen}</b> ${fechaEnvioTiempo}.
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="mt-3">
                                        <p>
                                            <b>Responsables:</b> <br> ${row.oficinas_vinculadas} <br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto d-flex align-items-center justify-content-center ps-4">
                                <div class="mt-2">
                                    <b>Fecha de Plazo Inicial:</b> ${fechaInicio} <br>
                                    <b>Fecha de Plazo Final:</b> ${fechaFin}
                                </div>
                            </div>
                            <div class="col-auto d-flex flex-column align-items-start justify-content-center ps-4">
                                <a href="#" class="btn btn-primary d-inline-flex btnRevisarEvidencia" data-id="${row.evidencia_id}">
                                    <i class="ti ti-circle-check mx-1"></i>Revisar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>`;

                evidenciaList.append(cardHTML);
            });
        },
        error: function () {
            console.log("Error en la llamada AJAX");
            alert("Error al cargar las evidencias");
        }
    });

} */

/* Listar datos por evidencia */
function listarEvidenciaId(evidenciaId) {
    $.ajax({
        url: "../controladores/evidencias.php?op=listarEvidenciaId",
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
                $("#fecha-registro").text(`Creado: ${cardData.fecha_registro}`);
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

                $("#evidencia-plazo").text(`${fechaInicio} - ${fechaFin}`);
                $("#evidencia-nombre").text(cardData.evidencia_nombre);
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
            if (response && response.aaData && response.aaData.length > 0) {
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
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-success btn-sm btnCalificarEvidencia" data-id="${data.historial_id}" 
                                ${data.estado2 !== 'Recibido' ? 'disabled' : ''}>
                                <i class="ti ti-file-like fs-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;

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


$("#btnGuardarCalificacion").on("click", function (e) {
    e.preventDefault();

    let historial_id = $("#historial_id").val();
    let grado_id = $("#grado_id").val();
    let observaciones = $("#observaciones").val().trim();
    let fecha_reprogramacion = $("#fecha_reprogramacion").val();
    let archivo = $("#archivo_observacion")[0].files[0];

    if (!grado_id) {
        mostrarToastAdvertencia("Seleccione un grado de cumplimiento.");
        return;
    }

    let formData = new FormData();
    formData.append("historial_id", historial_id);
    formData.append("grado_id", grado_id);
    formData.append("observaciones", observaciones);
    formData.append("fecha_reprogramacion", fecha_reprogramacion);

    if (archivo) {
        formData.append("archivo_observacion", archivo);
    }

    // Mostrar modal spinner
    const modalSpinner = new bootstrap.Modal(document.getElementById('modalSpinner'));
    $("#btnGuardarCalificacion").prop("disabled", true);
    modalSpinner.show();

    $.ajax({
        url: "../controladores/evidencias.php?op=registrarCalificacion",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            let data = JSON.parse(response);

            if (data.success) {
                mostrarToastExito("Calificación registrada correctamente.");
                $('#RegistrarCalificacionModal').modal('hide');
                setTimeout(function () {
                    const evidenciaId = getQueryParam("evidencia_id");
                    if (evidenciaId) {
                        listarEvidenciaId(evidenciaId);
                        mostrarHistorialEvidencia(evidenciaId);
                    }
                }, 1000);
            } else if (data.mensaje) {
                mostrarToastAdvertencia(data.mensaje);
            } else {
                mostrarToastAdvertencia("No se pudo registrar la calificación.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud.");
        },
        complete: function () {
            // Ocultar el modal spinner
            modalSpinner.hide();
        }
    });
});



/* $("#btnGuardarCalificacion").on("click", function (e) {
    e.preventDefault();

    let historial_id = $("#historial_id").val();
    let grado_id = $("#grado_id").val();
    let observaciones = $("#observaciones").val().trim();
    let fecha_reprogramacion = $("#fecha_reprogramacion").val();

    if (!grado_id) {
        mostrarToastAdvertencia("Seleccione un grado de cumplimiento.");
        return;
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=registrarCalificacion",
        type: "POST",
        data: {
            historial_id: historial_id,
            grado_id: grado_id,
            observaciones: observaciones,
            fecha_reprogramacion: fecha_reprogramacion
        },
        success: function (response) {
            let data = JSON.parse(response);

            if (data.success) {
                mostrarToastExito("Calificación registrada correctamente.");
                $('#RegistrarCalificacionModal').modal('hide');
                if (botonCalificarActual) {
                    botonCalificarActual.prop("disabled", true);
                }


                setTimeout(function () {
                    location.reload();
                }, 1000);

            } else {
                mostrarToastAdvertencia("No se pudo registrar la calificación.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud.");
        }
    });
}); */


$('#grado_id').on('change', function () {
    const gradoSeleccionado = $(this).val();

    if (gradoSeleccionado === '3') {
        $('#fechaReprogramacionContainer').show();
    } else {
        $('#fechaReprogramacionContainer').hide();
        $('#fecha_reprogramacion').val(''); // Limpiar si no aplica
    }
});


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
