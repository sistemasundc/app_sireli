let tabla;
let choicesOficinas;
let choicesCoordinadores;
let choicesMedios;
let choicesOficinasFiltro;
let choicesOficinasEditar = null;
let choicesCoordinadoresEditar = null;

$(document).ready(function () {


    listar();
    // Botón Filtrar
    $("#btnFiltrarOficina").on("click", function () {
        listar();
        limpiar_filtro()
    });

    // Enter en el campo de búsqueda
    $("#inputBusqueda").on("keypress", function (e) {
        if (e.which === 13) {
            listar();
            limpiar_filtro()
        }
    });

    // Botón ver todos
    $("#btnVerTodos").on("click", function () {

        $("#oficina_id").val("");
        $("#inputBusqueda").val("");
        listar();
    });

    $(document).on("click", "#btnEliminarEvidencia", function () {
        const evidencia_id = $(this).data("id");
        desactivarEvidencia(evidencia_id);
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
    });

    $.post("../controladores/verificaciones.php?op=seleccionarVerificacion", function (r) {
        // Llena el select con las opciones devueltas
        $("#medio_id").html(r);

        // Destruye la instancia anterior si ya estaba inicializada
        if (choicesMedios) {
            choicesMedios.destroy();
        }

        // Inicializa Choices.js después de cargar las opciones
        choicesMedios = new Choices('#medio_id', {
            searchEnabled: true,
            itemSelectText: 'Presionar',
            searchResultLimit: -1,
            renderChoiceLimit: -1,
            shouldSort: false

        });
    });


    $.post("../controladores/oficinas.php?op=seleccionarOficinas", function (r) {
        $("#oficina_id_evidencia").html(r);

        // Destruye instancia anterior si ya estaba inicializada
        if (choicesOficinas) {
            choicesOficinas.destroy();
        }

        choicesOficinas = new Choices('#oficina_id_evidencia', {
            removeItemButton: true,
            searchEnabled: true,
            itemSelectText: '',
            placeholderValue: 'Selecciona una o más oficinas',
        });
    });

    $.post("../controladores/oficinas.php?op=seleccionarCoordinadores", function (r) {
        $("#coordinador_id_evidencia").html(r);

        // Destruye instancia anterior si ya estaba inicializada
        if (choicesCoordinadores) {
            choicesCoordinadores.destroy();
        }

        choicesCoordinadores = new Choices('#coordinador_id_evidencia', {
            removeItemButton: true,
            searchEnabled: true,
            itemSelectText: '',
            placeholderValue: 'Selecciona una o más oficinas',
        });
    });

    $("#formEvidencia").on("submit", function (e) {
        e.preventDefault();
        registrarEvidencia(e);
    });

    /*     $(document).on('click', '#evidencia-list #btnEditarEvidencia', function () {
    
            var evidencia_id = $(this).data('id');
    
            $.ajax({
                url: '../controladores/evidencias.php?op=obtenerEvidencia',
                type: 'POST',
                data: {
                    'evidencia_id': evidencia_id,
                },
                success: function (response) {
                   
                    var data = JSON.parse(response);
    
                    $('#evidencia_id').val(data.evidencia_id);
    
                    $('#evidencia_nombre_actual').val(data.evidencia_nombre);
                    $('#fecha_plazo_inicio_actual').val(data.fecha_plazo_inicio);
                    $('#fecha_plazo_fin_actual').val(data.fecha_plazo_fin);
                    let html = $('<div>').html(data.evidencia_consideraciones);
                    let textoPlano = '';
    
                  
                    if (html.find('li').length > 0) {
                        html.find('li').each(function () {
                            textoPlano += '• ' + $(this).text().trim() + '\n';
                        });
                    }
                    
                    else if (html.find('p').length > 0) {
                        html.find('p').each(function () {
                            let texto = $(this).text().trim();
                            if (texto !== '') {
                                textoPlano += '• ' + texto + '\n';
                            }
                        });
                    }
                    
                    else {
                        textoPlano = html.text().trim();
                    }
    
                    $('#evidencia_consideraciones_actual').val(textoPlano);
    
                    var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasFileDesc'));
                    offcanvas.hide();
                    $('#EditarEvidenciaModal').modal('show');
    
                },
                error: function () {
                    alert('Error al obtener los datos de la Evidencia');
                }
            });
        }); */
    /*     $(document).on('click', '#evidencia-list #btnEditarEvidencia', function () {
    
            var evidencia_id = $(this).data('id');
    
            $.ajax({
                url: '../controladores/evidencias.php?op=obtenerEvidencia',
                type: 'POST',
                data: {
                    'evidencia_id': evidencia_id,
                },
                success: function (response) {
                    console.log(response);
                    var data = JSON.parse(response);
    
                    $('#evidencia_id').val(data.evidencia_id);
    
                    $('#evidencia_nombre_actual').val(data.evidencia_nombre);
                    $('#fecha_plazo_inicio_actual').val(data.fecha_plazo_inicio);
                    $('#fecha_plazo_fin_actual').val(data.fecha_plazo_fin);
    
                    quill.setContents(
                        quill.clipboard.convert(data.evidencia_consideraciones)
                    );
    
                    var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasFileDesc'));
                    offcanvas.hide();
                    $('#EditarEvidenciaModal').modal('show');
    
                },
                error: function () {
                    alert('Error al obtener los datos de la Evidencia');
                }
            });
        }); */

    $(document).on('click', '#evidencia-list #btnEditarEvidencia', function () {
        var evidencia_id = $(this).data('id');

        $.ajax({
            url: '../controladores/evidencias.php?op=obtenerEvidencia',
            type: 'POST',
            data: { 'evidencia_id': evidencia_id },
            success: function (response) {
                var data = JSON.parse(response);

                $('#evidencia_id').val(data.evidencia_id);
                $('#evidencia_nombre_actual').val(data.evidencia_nombre);
                $('#fecha_plazo_inicio_actual').val(data.fecha_plazo_inicio);
                $('#fecha_plazo_fin_actual').val(data.fecha_plazo_fin);
                quill.setContents(quill.clipboard.convert(data.evidencia_consideraciones));

                // Cargar las oficinas
                $.post("../controladores/oficinas.php?op=seleccionarOficinas", function (r) {
                    if (choicesOficinasEditar && typeof choicesOficinasEditar.destroy === 'function') {
                        choicesOficinasEditar.destroy();
                        choicesOficinasEditar = null;
                    }

                    $("#oficina_id_evidencia_actual").html(r);


                    $("#oficina_id_evidencia_actual option").each(function () {

                    });

                    let oficinasSeleccionadas = data.oficinas.map(String);

                    // Inicializar Choices.js
                    choicesOficinasEditar = new Choices('#oficina_id_evidencia_actual', {
                        removeItemButton: true,
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholderValue: 'Selecciona una o más oficinas',
                    });

                    // Seleccionar las oficinas después de inicializar Choices
                    choicesOficinasEditar.setChoiceByValue(oficinasSeleccionadas);

                });

                // Cargar los coordinadores
                $.post("../controladores/oficinas.php?op=seleccionarCoordinadores", function (r) {
                    if (choicesCoordinadoresEditar && typeof choicesCoordinadoresEditar.destroy === 'function') {
                        choicesCoordinadoresEditar.destroy();
                        choicesCoordinadoresEditar = null;
                    }

                    $("#coordinador_id_evidencia_actual").html(r);


                    $("#coordinador_id_evidencia_actual option").each(function () {

                    });

                    let oficinasSeleccionadas = data.coordinadores.map(String);

                    // Inicializar Choices.js
                    choicesCoordinadoresEditar = new Choices('#coordinador_id_evidencia_actual', {
                        removeItemButton: true,
                        searchEnabled: true,
                        itemSelectText: '',
                        placeholderValue: 'Selecciona una o más oficinas',
                    });

                    // Seleccionar las oficinas después de inicializar Choices
                    choicesCoordinadoresEditar.setChoiceByValue(oficinasSeleccionadas);

                });


                // Mostrar el modal
                $('#EditarEvidenciaModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos de la Evidencia');
            }
        });
    });

});

function registrarEvidencia() {
    const medio_id = $("#medio_id").val();
    const evidencia_nombre = $("#evidencia_nombre").val();
    const evidencia_consideraciones = quillRegistrar.root.innerHTML;
    const fecha_plazo_inicio = $("#fecha_plazo_inicio").val();
    const fecha_plazo_fin = $("#fecha_plazo_fin").val();

    // Obtener las oficinas seleccionadas
    const oficinas = $("#oficina_id_evidencia").val();
    if (!oficinas) {
        oficinas = [];
        $("input[name='oficina_id_evidencia[]']:checked").each(function () {
            oficinas.push($(this).val());
        });
    }

    if (oficinas.length === 0) {
        mostrarToastAdvertencia("Debe seleccionar al menos una oficina.");
        return;
    }

    // Obtener coordinadores seleccionados
    const coordinadores = $("#coordinador_id_evidencia").val();
    if (!coordinadores) {
        coordinadores = [];
        $("input[name='coordinador_id_evidencia[]']:checked").each(function () {
            coordinadores.push($(this).val());
        });
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=guardar",
        type: "POST",
        traditional: true,
        data: {
            medio_id: medio_id,
            evidencia_nombre: evidencia_nombre,
            evidencia_consideraciones: evidencia_consideraciones,
            fecha_plazo_inicio: fecha_plazo_inicio,
            fecha_plazo_fin: fecha_plazo_fin,
            "oficina_id_evidencia[]": oficinas,
            "coordinador_id_evidencia[]": coordinadores
        },
        success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Evidencia registrada con éxito.");
                limpiar();
                listar();
            } else {
                mostrarToastAdvertencia("Error al registrar la evidencia: " + data.message);
            }
        },
        error: function (xhr, status, error) {
            mostrarToastAdvertencia("Error en la solicitud: " + error);
        }
    });
}


function limpiar() {
    $("#medio_id").val("");
    $("#evidencia_nombre").val("");
    $("#fecha_plazo_inicio").val("");
    $("#fecha_plazo_fin").val("");

    quillRegistrar.root.innerHTML = "";

    $("#oficina_id_evidencia").val([]);
    $("input[name='oficina_id_evidencia[]']").prop("checked", false);
    $("#coordinador_id_evidencia").val([]);
    $("input[name='coordinador_id_evidencia[]']").prop("checked", false);
}

function limpiar_filtro() {

    if (choicesOficinasFiltro) {
        choicesOficinasFiltro.destroy();
    }

    $("#oficina_id").val("").trigger('change');

    // Reinicializar Choices.js con las configuraciones necesarias
    choicesOficinasFiltro = new Choices('#oficina_id', {
        searchEnabled: true,
        itemSelectText: 'Presionar',
        searchResultLimit: 5,
        renderChoiceLimit: 5,
        shouldSort: false
    });

    $("#inputBusqueda").val("");
}


function listar() {
    const oficinaId = $("#oficina_id").val();
    const textoBusqueda = $("#inputBusqueda").val().trim();

    // Mostrar filtros aplicados
    const filtrosAplicados = [];
    if (oficinaId) filtrosAplicados.push(`Oficina: ${$("#oficina_id option:selected").text()}`);
    if (textoBusqueda) filtrosAplicados.push(`Búsqueda: "${textoBusqueda}"`);

    if (filtrosAplicados.length > 0) {
        $("#filtros-aplicados").show();
        $("#filtros-mostrados").html(filtrosAplicados.map(filtro => `<span class="badge text-bg-primary">${filtro}</span>`).join(" "));
    } else {
        $("#filtros-aplicados").hide();
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=listar",
        type: "POST",
        dataType: "json",
        data: { oficina_id: oficinaId, busqueda: textoBusqueda },
        success: function (data) {
            const evidencias = data.aaData;
            const evidenciasPorPagina = 8;
            let paginaActual = 1;


            function mostrarPagina(pagina) {
                const evidenciaList = $("#evidencia-list");
                evidenciaList.empty();

                const inicio = (pagina - 1) * evidenciasPorPagina;
                const fin = inicio + evidenciasPorPagina;
                const evidenciasPagina = evidencias.slice(inicio, fin);

                if (evidenciasPagina.length === 0) {
                    evidenciaList.append(`
                        <div class="col-12 text-center text-muted mt-4">
                            <p>No se encontraron evidencias con los criterios seleccionados.</p>
                        </div>
                    `);
                    return;
                }

                evidenciasPagina.forEach(row => {
                    let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                    let fechaFin = formatearFecha(row.fecha_plazo_fin);
                    let claseVencido = estaVencido(row.fecha_plazo_fin) ? "card-vencida" : "";

                    const cardHTML = `
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card file-card h-100 ${claseVencido}" 
                                data-id="${row.evidencia_id}" 
                                data-nombre="${row.evidencia_nombre}"
                                data-fecha-inicio="${row.fecha_plazo_inicio}" 
                                data-fecha-fin="${row.fecha_plazo_fin}"
                                data-oficinas="${row.oficinas_vinculadas}" 
                                data-coordinadores="${row.coordinadores_vinculados}" 
                                data-medio="${row.medio_nombre}" 
                                data-indicador="${row.indicador_nombre}"
                                data-consideracion="${escapeHTMLAttr(row.evidencia_consideraciones)}" 
                                data-estado="${row.estado}">
                                <div class="card-body p-1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                class="form-check-input input-primary" 
                                                id="file-check-${row.evidencia_id}" 
                                                value="${row.evidencia_id}">
                                            <label class="form-check-label d-block" for="file-check-${row.evidencia_id}"></label>
                                        </div>
                                        <div class="dropdown">
                                            <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="material-icons-two-tone f-18">more_vert</i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" id="btnVerEvidencia" data-id="${row.evidencia_id}">Ver Detalle</a>
                                                <a class="dropdown-item" id="btnEditarEvidencia" data-id="${row.evidencia_id}">Editar</a>
                                                <a class="dropdown-item" id="btnEliminarEvidencia" data-id="${row.evidencia_id}">Eliminar</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start px-3 mb-1 mt-1">
                                        <div class="me-3 mt-1">
                                            <svg class="pc-icon wid-40 hei-40 text-warning">
                                                <use xlink:href="#custom-folder-open"></use>
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><b>${row.evidencia_nombre}</b></h6>
                                            <p class="mb-0"><small><b>Responsables:</b><br>${row.oficinas_vinculadas}</small></p>
                                            <p class="mb-0 text-muted"><small>Plazo: ${fechaInicio} - ${fechaFin}</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    evidenciaList.append(cardHTML);
                });

                // --- Click en "Ver Detalle" ---
                $(".dropdown-item#btnVerEvidencia").on("click", function () {
                    const evidenciaId = $(this).data("id");
                    const $card = $(`.card.file-card[data-id="${evidenciaId}"]`);

                    const cardData = {
                        nombre: $card.attr("data-nombre"),
                        fechaInicio: $card.attr("data-fecha-inicio"),
                        fechaFin: $card.attr("data-fecha-fin"),
                        indicador: $card.attr("data-indicador"),
                        medio: $card.attr("data-medio"),
                        consideracion: $card.attr("data-consideracion"),
                        oficinas: $card.attr("data-oficinas"),
                        coordinadores: $card.attr("data-coordinadores"),
                        estado: parseInt($card.attr("data-estado"), 10)
                    };

                    $("#offcanvasFileDesc").addClass("show");
                    $("#offcanvasFileDesc .offcanvas-body h5").text(cardData.nombre);
                    $("#evidenciaPlazo").text(`Plazo: ${formatearFecha(cardData.fechaInicio)} - ${formatearFecha(cardData.fechaFin)}`);
                    $("#indicadorNombre").text(cardData.indicador);
                    $("#medioNombre").text(cardData.medio);
                    $("#evidenciaConsideraciones").html(cardData.consideracion);
                    $("#evidenciaOficinas").html(cardData.oficinas);
                    let coordinadores = cardData.coordinadores;

                    if (
                        !coordinadores ||                      
                        coordinadores.trim() === "" ||        
                        coordinadores === "null" ||          
                        coordinadores === "undefined" 
                    ) {
                        coordinadores = "<span class='text-muted'>Sin coordinadores</span>";
                    }

                    $("#evidenciaCoordinadores").html(coordinadores);


                    $("#evidenciaEstado").removeClass("text-bg-success text-bg-danger text-bg-warning");
                    if (cardData.estado === 1) {
                        if (estaVencido(cardData.fechaFin)) {
                            $("#evidenciaEstado").text("Vencido").addClass("text-bg-danger");
                        } else {
                            $("#evidenciaEstado").text("Activo").addClass("text-bg-success");
                        }
                    } else {
                        $("#evidenciaEstado").text("Inactivo").addClass("text-bg-danger");
                    }
                });
            }

            marcarCheckboxSeleccionados();

            // --- Paginación ---
            function crearPaginacion(totalItems, itemsPorPagina) {
                const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
                const paginacion = $("#paginacion-evidencias");
                paginacion.empty();

                const pages = [];
                if (totalPaginas <= 5) {
                    for (let i = 1; i <= totalPaginas; i++) pages.push(i);
                } else {
                    pages.push(1);
                    if (paginaActual > 3) pages.push("...");
                    for (let i = Math.max(2, paginaActual - 1); i <= Math.min(paginaActual + 1, totalPaginas - 1); i++) pages.push(i);
                    if (paginaActual < totalPaginas - 2) pages.push("...");
                    if (totalPaginas > 1) pages.push(totalPaginas);
                }

                pages.forEach(page => {
                    if (page === "...") {
                        paginacion.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                    } else {
                        const active = page === paginaActual ? "active" : "";
                        paginacion.append(`<li class="page-item ${active}"><a class="page-link" href="#">${page}</a></li>`);
                    }
                });

                $(".page-link").on("click", function (e) {
                    e.preventDefault();
                    let page = $(this).text();
                    if (page === "...") return;
                    paginaActual = parseInt(page);
                    mostrarPagina(paginaActual);
                    crearPaginacion(totalItems, itemsPorPagina);
                });
            }

            mostrarPagina(paginaActual);
            crearPaginacion(evidencias.length, evidenciasPorPagina);
        },
        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
}


let evidenciasSeleccionadas = [];
let allSelected = false;

// -------------------------
// Checkbox individual
// -------------------------
$(document).on("change", ".card.file-card .form-check-input", function () {
    let id = $(this).val();

    if ($(this).is(":checked")) {
        if (!evidenciasSeleccionadas.includes(id)) {
            evidenciasSeleccionadas.push(id);
        }
    } else {
        evidenciasSeleccionadas = evidenciasSeleccionadas.filter(eid => eid !== id);
        allSelected = false;
        $("#btnSelectAll").text("Seleccionar todo");
    }
});

// -------------------------
// Botón Seleccionar/Deseleccionar todo
// -------------------------
$("#btnSelectAll").on("click", function () {
    if (!allSelected) {

        $.ajax({
            url: "../controladores/evidencias.php?op=listarTodosIds",
            type: "POST",
            data: {
                oficina_id: $("#oficina_id").val(),
                busqueda: $("#inputBusqueda").val().trim()
            },
            dataType: "json",
            success: function (resp) {
                if (resp.success) {
                    evidenciasSeleccionadas = resp.ids;
                    allSelected = true;

                    $(".card.file-card .form-check-input").prop("checked", true);

                    $("#btnSelectAll").text("Deseleccionar todo");
                    mostrarToastExito("✅ Todas las evidencias fueron seleccionadas");
                }
            }
        });
    } else {
        // Deseleccionar todo
        evidenciasSeleccionadas = [];
        allSelected = false;

        $(".card.file-card .form-check-input").prop("checked", false);

        $("#btnSelectAll").text("Seleccionar todo");
        mostrarToastAdvertencia("Se desmarcaron todas las evidencias");
    }
});

// -------------------------
// Función para recordar checks al paginar
// -------------------------
function marcarCheckboxSeleccionados() {
    $(".card.file-card .form-check-input").each(function () {
        if (evidenciasSeleccionadas.includes($(this).val())) {
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", false);
        }
    });
}

// -------------------------
// Botón Modificar plazos
// -------------------------
$("#btnModificarPlazos").on("click", function () {
    // Marcar los checkboxes que realmente están seleccionados
    marcarCheckboxSeleccionados(); // opcional si quieres sincronizar

    // Verificar selección real
    if (evidenciasSeleccionadas.length === 0) {
        mostrarToastAdvertencia("Por favor, seleccione al menos una evidencia.");
        return; // NO abrir modal
    }

    $("#EditarPlazosEvidenciaModal input[name='evidenciasSeleccionadas']")
        .val(evidenciasSeleccionadas.join(","));

    $("#EditarPlazosEvidenciaModal").modal("show");
});


// -------------------------
// Submit del formulario del modal
// -------------------------
$(document).on("submit", ".formEditarPlazosEvidencia", function (e) {
    e.preventDefault();

    let evidencias = $("#evidenciasSeleccionadas").val();
    let fecha_inicio = $("#fecha_plazo_inicio_nuevo").val();
    let fecha_fin = $("#fecha_plazo_fin_nuevo").val();

    if (!evidencias) {
        mostrarToastAdvertencia("No hay evidencias seleccionadas.");
        return;
    }

    if (!fecha_inicio && !fecha_fin) {
        mostrarToastAdvertencia("Debe seleccionar al menos una fecha (inicio o fin).");
        return;
    }

    let datos = {
        evidencias: evidencias,
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin
    };

    console.log("Datos a enviar:", datos);

    $.ajax({
        url: "../controladores/evidencias.php?op=actualizarPlazosGrupo",
        type: "POST",
        data: datos,
        dataType: "json",
        success: function (resp) {
            if (resp.success) {
                mostrarToastExito("Plazos actualizados correctamente");

                // Cerrar modal
                $("#EditarPlazosEvidenciaModal").modal("hide");

                // Limpiar selección
                evidenciasSeleccionadas = [];
                allSelected = false;

                // Desmarcar todos los checkboxes visibles
                $(".card.file-card .form-check-input").prop("checked", false);

                // Resetear el botón de seleccionar todo
                $("#btnSelectAll").text("Seleccionar todo");

                // Limpiar inputs del modal
                $("#fecha_plazo_inicio_nuevo").val("");
                $("#fecha_plazo_fin_nuevo").val("");

                // Volver a cargar la lista
                listar();
            } else {
                mostrarToastAdvertencia(resp.msg || "No se pudo actualizar los plazos");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", error);
            mostrarToastAdvertencia("❌ Error en la petición");
        }
    });
});


/* function listar() {
    $.ajax({
        url: "../controladores/evidencias.php?op=listar",
        type: "POST",
        dataType: "json",
        success: function (data) {
            const evidencias = data.aaData;
            const evidenciasPorPagina = 4;
            let paginaActual = 1;

            function mostrarPagina(pagina) {
                const evidenciaList = $("#evidencia-list");
                evidenciaList.empty();

                const inicio = (pagina - 1) * evidenciasPorPagina;
                const fin = inicio + evidenciasPorPagina;
                const evidenciasPagina = evidencias.slice(inicio, fin);

                evidenciasPagina.forEach(function (row) {
                    let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                    let fechaFin = formatearFecha(row.fecha_plazo_fin);

                    const cardHTML = `
                           <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card file-card h-100"  data-id="${row.evidencia_id}" data-nombre="${row.evidencia_nombre}" data-fecha-inicio="${row.fecha_plazo_inicio}" data-fecha-fin="${row.fecha_plazo_fin}" data-oficinas="${row.oficinas_vinculadas}" data-medio="${row.medio_nombre}" data-consideracion="${escapeHTMLAttr(row.evidencia_consideraciones)}"
                                data-estado="${row.estado}">
                                    <div class="card-body p-1">
                                            <!-- Parte superior: check a la izquierda y menú a la derecha -->
                                            <div class="d-flex align-items-center justify-content-between">
                                                <!-- Check a la izquierda -->
                                                <div class="form-check">
                                                    <input type="radio" name="file-radio" class="form-check-input input-primary" id="file-check-${row.evidencia_id}">
                                                    <label class="form-check-label d-block" for="file-check-${row.evidencia_id}"></label>
                                                </div>

                                                <!-- Menú de opciones a la derecha -->
                                                <div class="dropdown">
                                                    <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="material-icons-two-tone f-18">more_vert</i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" id="btnEditarEvidencia" data-id="${row.evidencia_id}">Editar</a>
                                                        <a class="dropdown-item" href="#">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contenido central: ícono de carpeta a la izquierda, texto a la derecha -->
                                            <div class="d-flex align-items-start px-3 mb-1 mt-1">
                                                <!-- Ícono de carpeta -->
                                                <div class="me-3 mt-1">
                                                    <svg class="pc-icon wid-40 hei-40 text-warning">
                                                        <use xlink:href="#custom-folder-open"></use>
                                                    </svg>
                                                </div>

                                                <!-- Datos del archivo -->
                                                <div>
                                                    <h6 class="mb-1"><b>${row.evidencia_nombre}</b></h6>
                                                    <p class="mb-0"><small><b>Responsables:</b><br>${row.oficinas_vinculadas}</small></p>
                                                    <p class="mb-0 text-muted"><small>Plazo: ${fechaInicio} - ${fechaFin}</small></p>
                                                </div>
                                            </div>
                                    </div>


                            </div>`;

                    evidenciaList.append(cardHTML);
                });

                // Agregar eventos a las tarjetas
                $(".card.file-card").on("click", function () {
                    let cardData = $(this).data();

                    $("input[name='file-radio']").prop("checked", false);
                    let evidenciaId = cardData.id;
                    $(`#file-check-${evidenciaId}`).prop("checked", true);

                    $(".card.file-card").removeClass("border-primary shadow-lg");
                    $(this).addClass("border-primary shadow-lg");

                    $("#offcanvasFileDesc").addClass("show");
                    $("#offcanvasFileDesc .offcanvas-body h5").text(cardData.nombre);
                    $("#evidenciaPlazo").text(`Plazo: ${formatearFecha(cardData.fechaInicio)} - ${formatearFecha(cardData.fechaFin)}`);

                    if (cardData.estado == 1) {
                        $("#evidenciaEstado").text("Activo").removeClass("text-bg-danger").addClass("text-bg-success");
                    } else {
                        $("#evidenciaEstado").text("Inactivo").removeClass("text-bg-success").addClass("text-bg-danger");
                    }

                    $("#medioNombre").text(cardData.medio);
                    $("#evidenciaConsideraciones").html(cardData.consideracion);
                    $("#evidenciaOficinas").html(cardData.oficinas);
                });
            }

            function crearPaginacion(totalItems, itemsPorPagina) {
                const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
                const paginacion = $("#paginacion-evidencias");
                paginacion.empty();

                for (let i = 1; i <= totalPaginas; i++) {
                    let active = i === paginaActual ? "active" : "";
                    paginacion.append(`<li class="page-item ${active}"><a class="page-link" href="#">${i}</a></li>`);
                }

                $(".page-link").on("click", function (e) {
                    e.preventDefault();
                    paginaActual = parseInt($(this).text());
                    mostrarPagina(paginaActual);
                    crearPaginacion(evidencias.length, evidenciasPorPagina);
                });
            }

            // Mostrar la primera página
            mostrarPagina(paginaActual);
            crearPaginacion(evidencias.length, evidenciasPorPagina);
        },
        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
} */
/* function listarold() {

    $.ajax({
        url: "../controladores/evidencias.php?op=listar",
        type: "POST",
        dataType: "json",
        success: function (data) {
            let evidenciaList = $("#evidencia-list");
            evidenciaList.empty();

            data.aaData.forEach(function (row) {
                let fechaInicio = formatearFecha(row.fecha_plazo_inicio);
                let fechaFin = formatearFecha(row.fecha_plazo_fin);

                let cardHTML = `
                    <div class="col-md-6 col-lg-4 col-xxl-3">
                        <div class="card file-card  h-100" data-id="${row.evidencia_id}" data-nombre="${row.evidencia_nombre}" data-fecha-inicio="${row.fecha_plazo_inicio}" data-fecha-fin="${row.fecha_plazo_fin}" data-oficinas="${row.oficinas_vinculadas}" data-medio="${row.medio_nombre}" data-consideracion="${escapeHTMLAttr(row.evidencia_consideraciones)}"
                    data-estado="${row.estado}">
                        <div class="card-body p-2">
                            <!-- Parte superior: check a la izquierda y menú a la derecha -->
                            <div class="d-flex align-items-center justify-content-between">
                                <!-- Check a la izquierda -->
                                <div class="form-check">
                                    <input type="radio" name="file-radio" class="form-check-input input-primary" id="file-check-${row.evidencia_id}">
                                    <label class="form-check-label d-block" for="file-check-${row.evidencia_id}"></label>
                                </div>

                                <!-- Menú de opciones a la derecha -->
                                <div class="dropdown">
                                    <a class="avtar avtar-xs btn-link-secondary dropdown-toggle arrow-none" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons-two-tone f-18">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" id="btnEditarEvidencia" data-id="${row.evidencia_id}">Editar</a>
                                        <a class="dropdown-item" href="#">Eliminar</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido central: ícono de carpeta a la izquierda, texto a la derecha -->
                            <div class="d-flex align-items-start px-3 mb-1 mt-2">
                                <!-- Ícono de carpeta -->
                                <div class="me-3 mt-1">
                                    <svg class="pc-icon wid-40 hei-40 text-warning">
                                        <use xlink:href="#custom-folder-open"></use>
                                    </svg>
                                </div>

                                <!-- Datos del archivo -->
                                <div>
                                    <h6 class="mb-1"><b>${row.evidencia_nombre}</b></h6>
                                    <p class="mb-0"><small><b>Responsables:</b><br>${row.oficinas_vinculadas}</small></p>
                                    <p class="mb-0 text-muted"><small>Plazo: ${fechaInicio} - ${fechaFin}</small></p>
                                </div>
                            </div>
                        </div>


                    </div>`;

                evidenciaList.append(cardHTML);
            });

            $(".card.file-card").on("click", function () {
                let cardData = $(this).data();

                $("input[name='file-radio']").prop("checked", false);

                let evidenciaId = cardData.id;
                let checkbox = $(`#file-check-${evidenciaId}`);
                checkbox.prop("checked", true);

                $(".card.file-card").removeClass("border-primary shadow-lg");
                $(this).addClass("border-primary shadow-lg");

                $("#offcanvasFileDesc").addClass("show");

                // Mostrar la información en el offcanvas

                $("#offcanvasFileDesc .offcanvas-body h5").text(cardData.nombre);
                $("#evidenciaPlazo").text(`Plazo: ${formatearFecha(cardData.fechaInicio)} - ${formatearFecha(cardData.fechaFin)}`);
                if (cardData.estado == 1) {
                    $("#evidenciaEstado")
                        .text("Activo")
                        .removeClass("text-bg-danger")
                        .addClass("text-bg-success");
                } else {
                    $("#evidenciaEstado")
                        .text("Inactivo")
                        .removeClass("text-bg-success")
                        .addClass("text-bg-danger");
                }

                $("#medioNombre").text(cardData.medio);
                $("#evidenciaConsideraciones").html(cardData.consideracion);
                $("#evidenciaOficinas").html(cardData.oficinas);

            });

        },

        error: function () {
            alert("Error al cargar las evidencias");
        }
    });
} */

/* $("#btnActualizarEvidencia").on("click", function (e) {
    e.preventDefault();

    var evidencia_id = $("#evidencia_id").val();
    var evidencia_nombre_actual = $("#evidencia_nombre_actual").val();
    var evidencia_consideraciones_actual = $("#evidencia_consideraciones_actual").val();
    var fecha_plazo_inicio_actual = $("#fecha_plazo_inicio_actual").val();
    var fecha_plazo_fin_actual = $("#fecha_plazo_fin_actual").val();


    if (evidencia_nombre_actual === "" || fecha_plazo_inicio_actual === "" || fecha_plazo_fin_actual === "") {
        mostrarToastAdvertencia("Existen campos vacíos.");
        return;
    }

  
    let lineas = evidencia_consideraciones_actual.split('\n').filter(linea => linea.trim() !== '');
    let html_consideraciones = '<ul>';
    lineas.forEach(function (linea) {
        let texto = linea.replace(/^•\s?/, '').trim(); 
        if (texto !== '') {
            html_consideraciones += `<li>${texto}</li>`;
        }
    });
    html_consideraciones += '</ul>';

    
    $.ajax({
        url: "../controladores/evidencias.php?op=actualizarEvidencia",
        type: "POST",
        data: {
            evidencia_id: evidencia_id,
            evidencia_nombre: evidencia_nombre_actual,
            evidencia_consideraciones: html_consideraciones,
            fecha_plazo_inicio: fecha_plazo_inicio_actual,
            fecha_plazo_fin: fecha_plazo_fin_actual
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Evidencia actualizada correctamente.");
                $('#EditarEvidenciaModal').modal('hide');
                listar();

            } else {
                mostrarToastAdvertencia("La Evidencia ya está registrada. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
}); */

/* $("#btnActualizarEvidencia").on("click", function (e) {
    e.preventDefault();

    var evidencia_id = $("#evidencia_id").val();
    var evidencia_nombre_actual = $("#evidencia_nombre_actual").val();
    var evidencia_consideraciones_actual = quill.root.innerHTML;
    var fecha_plazo_inicio_actual = $("#fecha_plazo_inicio_actual").val();
    var fecha_plazo_fin_actual = $("#fecha_plazo_fin_actual").val();

    if (evidencia_nombre_actual === "" || fecha_plazo_inicio_actual === "" || fecha_plazo_fin_actual === "") {
        mostrarToastAdvertencia("Existen campos vacíos.");
        return;
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=actualizarEvidencia",
        type: "POST",
        data: {
            evidencia_id: evidencia_id,
            evidencia_nombre: evidencia_nombre_actual,
            evidencia_consideraciones: evidencia_consideraciones_actual,
            fecha_plazo_inicio: fecha_plazo_inicio_actual,
            fecha_plazo_fin: fecha_plazo_fin_actual
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Evidencia actualizada correctamente.");
                $('#EditarEvidenciaModal').modal('hide');
                listar();

            } else {
                mostrarToastAdvertencia("La Evidencia ya está registrada. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
}); */

$("#btnActualizarEvidencia").on("click", function (e) {
    e.preventDefault();

    const evidencia_id = $("#evidencia_id").val();
    const evidencia_nombre_actual = $("#evidencia_nombre_actual").val();
    const evidencia_consideraciones_actual = quill.root.innerHTML;
    const fecha_plazo_inicio_actual = $("#fecha_plazo_inicio_actual").val();
    const fecha_plazo_fin_actual = $("#fecha_plazo_fin_actual").val();

    const oficinas = $("#oficina_id_evidencia_actual").val();   // p. ej. ["1","10","3"]
    const coordinadores = $("#coordinador_id_evidencia_actual").val();

    if (
        evidencia_nombre_actual === "" ||
        fecha_plazo_inicio_actual === "" ||
        fecha_plazo_fin_actual === "" ||
        !oficinas || oficinas.length === 0
    ) {
        mostrarToastAdvertencia("Existen campos vacíos o no se ha seleccionado ninguna oficina.");
        return;
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=actualizarEvidencia",
        type: "POST",


        data: {
            evidencia_id: evidencia_id,
            evidencia_nombre: evidencia_nombre_actual,
            evidencia_consideraciones: evidencia_consideraciones_actual,
            fecha_plazo_inicio: fecha_plazo_inicio_actual,
            fecha_plazo_fin: fecha_plazo_fin_actual,
            "oficina_id_evidencia_actual[]": oficinas,     // ← aquí el []
            "coordinador_id_evidencia_actual[]": coordinadores
        },

        traditional: true,   // hace que jQuery envíe oficina_id_evidencia_actual[]=1&... (no '0:', '1:' …)
        success: function (response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    mostrarToastExito("Evidencia actualizada correctamente.");
                    $('#EditarEvidenciaModal').modal('hide');
                    listar();
                } else {
                    mostrarToastAdvertencia(data.message || "Problema al actualizar.");
                }
            } catch (err) {
                mostrarToastAdvertencia("Error inesperado.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error en la solicitud AJAX.");
        }
    });
});

$("#btnActualizarEvidencia2").on("click", function (e) {
    e.preventDefault();

    var evidencia_id = $("#evidencia_id").val();
    var evidencia_nombre_actual = $("#evidencia_nombre_actual").val();
    var evidencia_consideraciones_actual = quill.root.innerHTML;
    var fecha_plazo_inicio_actual = $("#fecha_plazo_inicio_actual").val();
    var fecha_plazo_fin_actual = $("#fecha_plazo_fin_actual").val();
    var oficinas = $("#oficina_id_evidencia_actual").val(); // Múltiples oficinas


    if (
        evidencia_nombre_actual === "" ||
        fecha_plazo_inicio_actual === "" ||
        fecha_plazo_fin_actual === "" ||
        !oficinas || oficinas.length === 0
    ) {
        mostrarToastAdvertencia("Existen campos vacíos o no se ha seleccionado ninguna oficina.");
        return;
    }

    $.ajax({
        url: "../controladores/evidencias.php?op=actualizarEvidencia",
        type: "POST",
        data: {
            evidencia_id: evidencia_id,
            evidencia_nombre: evidencia_nombre_actual,
            evidencia_consideraciones: evidencia_consideraciones_actual,
            fecha_plazo_inicio: fecha_plazo_inicio_actual,
            fecha_plazo_fin: fecha_plazo_fin_actual,
            oficina_id_evidencia_actual: oficinas
        },
        traditional: true,
        success: function (response) {
            try {
                console.log("🔍 Respuesta cruda:", response);
                var data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Evidencia actualizada correctamente.");
                    $('#EditarEvidenciaModal').modal('hide');
                    listar();
                }

            } catch (e) {
                console.error("Error al interpretar JSON:");

            }
        },
        error: function () {
            mostrarToastAdvertencia("Error en la solicitud AJAX.");
        }
    });
});

function desactivarEvidencia(evidencia_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Eliminar Evidencia?</span>' +
            '</div>',
        showCancelButton: true,
        confirmButtonColor: "#4680ff",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No",
        customClass: {
            popup: 'rounded-4 shadow p-3 custom-swal-width',
            confirmButton: 'btn btn-primary btn-sm px-3 fs-8',
            cancelButton: 'btn btn-secondary btn-sm px-3 fs-8',
            icon: 'm-1'
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {

            $.post("../controladores/evidencias.php?op=desactivarEvidencia", {
                evidencia_id: evidencia_id
            }, function (response) {

                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Evidencia eliminada con éxito.");
                    listar();
                } else {

                    mostrarToastAdvertencia("hubo un error.");
                }
            }).fail(function (xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            });
        }
    });
}

function mostrarToastExito(message) {
    $("#mensajefloat").html(message);
    const toastLiveExample = document.getElementById('liveToast');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
    toastBootstrap.show();
}


function mostrarToastAdvertencia(message) {
    $("#mensajefloatwarning").html(message);
    const toastLiveExample = document.getElementById('liveToastwarning');
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
    toastBootstrap.show();
}

function formatearFecha(fechaStr) {
    if (!fechaStr) return '';

    const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    const [anio, mes, dia] = fechaStr.split('-'); // evita problemas de zona horaria

    return `${dia} ${meses[parseInt(mes, 10) - 1]} ${anio}`;
}

function estaVencido(fechaFinStr) {
    const [anio, mes, dia] = fechaFinStr.split('-');
    const fechaFinDate = new Date(anio, mes - 1, dia, 23, 59, 59, 999); // fin del día
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // inicio del día
    return hoy > fechaFinDate;
}

function escapeHTMLAttr(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}


document.querySelector('#closeOffcanvas').addEventListener('click', function () {

    document.querySelector('#offcanvasFileDesc').classList.remove('show');
});
