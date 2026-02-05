let choicesCbc;
let choicesComponentes;
let choicesIndicadores;
let choicesOficinas;

$(document).ready(function () {

    if (document.querySelector("#tblReporte")) {
        listar();
    }

    if (document.querySelector("#chartMediosCBC")) {
        graficoMediosCBC();
    }

    if (document.querySelector("#chartEvidencias")) {
        graficoEvidencias();
    }

    if (document.querySelector("#TotalComponentes") || document.querySelector("#TotalIndicadores") || document.querySelector("#TotalMedios") || document.querySelector("#TotalEvidencias") || document.querySelector("#TotalCondiciones")) {
        actualizarReportesFiltrados();
    }

    if (document.querySelector("#chartCumplimientoMV")) {
        graficoResumen();
    }

    if (document.querySelector("#chartOficinaCumplimientoMV")) {
        graficoResumenPorOficina();
    }

    if (document.querySelector("#tblMediosCumplimiento")) {
        listarMediosCumplimiento();
    }

    if (document.querySelector("#tblOficinaMediosCumplimiento")) {
        listarMediosCumplimientoPorOficina();
    }


    $('#btnFiltrarOficina').on('click', function () {
        listar();
        limpiar_filtro();
    });

    $('#btnVerTodos').on('click', function () {
        $('#cbc_id').val('');
        $('#oficina_id').val('');
        listar();
    });

    $("#cbc_id").on("change", function () {
        const condicion = $(this).val();
        const cbcNombre = $("#cbc_id option:selected").text();

        if (condicion === "") {
            $("#tituloMedios").addClass("d-none").text("");
            $("#tituloCondiciones").addClass("d-none").text("");
        } else {
            $("#tituloMedios").removeClass("d-none").text(cbcNombre);
            $("#tituloCondiciones").removeClass("d-none").text(cbcNombre);
        }

        graficoResumen(condicion);
        listarMediosCumplimiento(condicion);
        actualizarReportesFiltrados(condicion);
    });


    $("#componente_id, #indicador_id, #oficina_id").on("change", function () {
        actualizarReportesFiltrados();
    });
    /*     $("#cbc_id, #componente_id, #indicador_id, #oficina_id").on("change", function () {
            actualizarReportesFiltrados();
        });
     */
    if (document.querySelector("#cbc_id")) {
        $.post("../controladores/condiciones.php?op=seleccionarCondicionFiltro", function (r) {
            $("#cbc_id").html(r);

            if (choicesCbc) {
                choicesCbc.destroy();
            }

            choicesCbc = new Choices('#cbc_id', {
                searchEnabled: true,
                itemSelectText: 'Presionar',
                searchResultLimit: -1,
                renderChoiceLimit: -1,
                shouldSort: false
            });
        });
    }

    if (document.querySelector("#componente_id")) {
        $.post("../controladores/componentes.php?op=seleccionarComponenteFiltro", function (r) {
            $("#componente_id").html(r);

            if (choicesComponentes) {
                choicesComponentes.destroy();
            }

            choicesComponentes = new Choices('#componente_id', {
                searchEnabled: true,
                itemSelectText: 'Presionar',
                searchResultLimit: -1,
                renderChoiceLimit: -1,
                shouldSort: false
            });
        });
    }


    if (document.querySelector("#indicador_id")) {
        $.post("../controladores/indicadores.php?op=seleccionarIndicadorFiltro", function (r) {
            $("#indicador_id").html(r);

            if (choicesIndicadores) {
                choicesIndicadores.destroy();
            }

            choicesIndicadores = new Choices('#indicador_id', {
                searchEnabled: true,
                itemSelectText: 'Presionar',
                searchResultLimit: -1,
                renderChoiceLimit: -1,
                shouldSort: false
            });
        });
    }

    if (document.querySelector("#oficina_id")) {
        $.post("../controladores/oficinas.php?op=seleccionarOficinaFiltro", function (r) {
            $("#oficina_id").html(r);

            if (choicesOficinas) {
                choicesOficinas.destroy();
            }

            choicesOficinas = new Choices('#oficina_id', {
                searchEnabled: true,
                itemSelectText: 'Presionar',
                searchResultLimit: -1,
                renderChoiceLimit: -1,
                shouldSort: false

            });
        });
    }

});

/* function reporte() {
    $.ajax({
        url: '../controladores/estadisticas.php',
        type: 'GET',
        data: { op: 'reportes' },
        dataType: 'json',
        success: function (response) {

            var totalCondiciones = response.aaData.length > 0 ? response.aaData[0].TotalCondiciones : 0;
            var totalComponentes = response.aaData.length > 0 ? response.aaData[0].TotalComponentes : 0;
            var totalIndicadores = response.aaData.length > 0 ? response.aaData[0].TotalIndicadores : 0;
            var totalMedios = response.aaData.length > 0 ? response.aaData[0].TotalMedios : 0;
            var totalEvidencias = response.aaData.length > 0 ? response.aaData[0].TotalEvidencias : 0;

            $('#TotalCondiciones').text(totalCondiciones);
            $('#TotalComponentes').text(totalComponentes);
            $('#TotalIndicadores').text(totalIndicadores);
            $('#TotalMedios').text(totalMedios);
            $('#TotalEvidencias').text(totalEvidencias);
        },
        error: function (xhr, status, error) {
            console.error('Error fetching report data:', error);
        }
    });
} */

function actualizarReportesFiltrados() {
    const cbc_id = $("#cbc_id").val();
    const componente_id = $("#componente_id").val();
    const indicador_id = $("#indicador_id").val();
    const oficina_id = $("#oficina_id").val();

    $.post("../controladores/estadisticas.php?op=reportes", {
        cbc_id: cbc_id,
        componente_id: componente_id,
        indicador_id: indicador_id,
        oficina_id: oficina_id
    }, function (data) {
        const json = JSON.parse(data);
        const valores = json.aaData[0];

        $("#TotalComponentes").text(valores.TotalComponentes);
        $("#TotalIndicadores").text(valores.TotalIndicadores);
        $("#TotalMedios").text(valores.TotalMedios);
        $("#TotalEvidencias").text(valores.TotalEvidencias);
        $("#TotalCondiciones").text(valores.TotalCondiciones);
    });
}


function graficoMediosCBC() {
    $.ajax({
        url: '../controladores/estadisticas.php',
        type: 'GET',
        data: { op: 'mediosPorCondicion' },
        dataType: 'json'
    })
        .done(function (json) {
            const datos = json.aaData || [];

            const etiquetas = datos.map(d => d.cbc_nombre);
            const valores = datos.map(d => Number(d.total_medios));

            const contenedor = document.querySelector('#chartMediosCBC');
            if (!contenedor) return; // Si no existe el div, salimos

            const options = {
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                series: [{ name: 'Medios de verificación', data: valores }],
                xaxis: { categories: etiquetas, labels: { rotate: -45 } },
                plotOptions: { bar: { borderRadius: 4 } },
                dataLabels: { enabled: true }
            };

            new ApexCharts(contenedor, options).render();
        })
        .fail(function (xhr, status, error) {
            console.error('Error cargando datos del gráfico:', error);
        });
}


function graficoEvidencias() {
    $.ajax({
        url: '../controladores/estadisticas.php',
        type: 'GET',
        data: { op: 'gradoPorEvidencias' },
        dataType: 'json'
    })
        .done(function (json) {
            const datos = json.aaData || [];

            const etiquetas = datos.map(d => d.grado_nombre);
            const valores = datos.map(d => Number(d.total_evidencias));

            const contenedor = document.querySelector('#chartEvidencias');
            if (!contenedor) return;

            const options = {
                chart: { type: 'bar', height: 350, toolbar: { show: false } },
                series: [{ name: 'Evidencias', data: valores }],
                xaxis: { categories: etiquetas, labels: { rotate: -45 } },
                plotOptions: { bar: { borderRadius: 4 } },
                dataLabels: { enabled: true },
                colors: ['#fda722']
            };

            new ApexCharts(contenedor, options).render();
        })
        .fail(function (xhr, status, error) {
            console.error('Error cargando datos del gráfico:', error);
        });
}


$('#tblReporte').on('init.dt', function () {
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});

function listarok1() {
    const condicion = $('#cbc_id').val();
    const oficina = $('#oficina_id').val();

    tabla = $('#tblReporte').DataTable({
        destroy: true,
        ordering: true,
        pageLength: 10,

        ajax: {
            url: "../controladores/estadisticas.php?op=listarReporte",
            type: "POST",
            dataType: "json",
            data: {
                cbc_id: condicion,      // Enviamos el valor al backend con el mismo nombre
                oficina_id: oficina     // Enviamos el valor al backend con el mismo nombre
            },
            dataSrc: "aaData"
        },

        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'CUMPLIMIENTO DE LA MATRIZ DE MANTENIMIENTO DE LAS CBC 2025',
                text: '<i class="ti ti-file-export"></i> Exportar Excel',
                className: 'btn btn-primary',
                attr: {
                    style: 'font-size: 0.7rem; margin-top: 8px;margin-left: 15px;'
                }
            }
        ],

        columns: [
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.cbc_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.componente_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.indicador_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.medio_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const evidencia = row.evidencia_nombre;
                    if (!evidencia || evidencia.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f; font-weight:bold;">No aplica</span>`;
                    }
                    return `<p class="mb-0">${evidencia}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const responsables = row.oficinas_vinculadas;

                    if (!responsables || responsables.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f;">No aplica</span>`;
                    }

                    return `<small class="mb-0">${responsables}</small>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const evidencia = row.evidencia_nombre;
                    if (!evidencia || evidencia.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f;">No aplica</span>`;
                    }

                    const valor = row.grado_cumplimiento ?? "Pendiente";
                    let claseColor = "text-bg-secondary";

                    if (valor === "No Cumple") {
                        claseColor = "text-bg-danger";
                    } else if (valor === "Si Cumple") {
                        claseColor = "text-bg-success";
                    } else if (valor === "Cumple Parcialmente") {
                        claseColor = "text-bg-warning";
                    }

                    return `<span class="badge ${claseColor}">${valor}</span>`;
                }
            }
        ],

        language: {
            url: "../json/es-ES.json"
        }
    });
}
/* function listar() {
    tabla = $('#tblReporte').DataTable({
        destroy: true,
        ordering: true,
        pageLength: 10,

        ajax: {
            url: "../controladores/estadisticas.php?op=listarReporte",
            type: "POST",
            dataType: "json",
            dataSrc: "aaData"
        },

        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'CUMPLIMIENTO DE LA MATRIZ DE MANTENIMIENTO DE LAS CBC 2025',
                text: '<i class="ti ti-file-export"></i> Exportar Excel',
                className: 'btn btn-primary',
                attr: {
                    style: 'font-size: 0.7rem; margin-top: 8px;margin-left: 15px;'
                }
            }
        ],

        columns: [
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.cbc_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.componente_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.indicador_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.medio_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const evidencia = row.evidencia_nombre;

                    if (!evidencia || evidencia.trim() === "") {
                        // "No aplica" con clase visual de 'text-bg-danger' y estilo lila
                        return `<span class="badge" style="background-color: #783c8f; font-weight:bold;">No aplica</span>`;
                    }

                    return `<p class="mb-0">${evidencia}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    if (!row.oficinas_vinculadas || row.oficinas_vinculadas.trim() === '') {
                        return `<span class="badge" style="background-color: #783c8f;">No aplica</span>`;
                    }
                    return `<small class="mb-0">${row.oficinas_vinculadas}</small>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const evidencia = row.evidencia_nombre;

                    // Si no hay evidencia, mostrar "No aplica"
                    if (!evidencia || evidencia.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f;">No aplica</span>`;
                    }

                    const valor = row.grado_cumplimiento ?? "Pendiente";
                    let claseColor = "text-bg-secondary"; // gris por defecto

                    if (valor === "No Cumple") {
                        claseColor = "text-bg-danger"; // rojo
                    } else if (valor === "Si Cumple") {
                        claseColor = "text-bg-success"; // verde
                    } else if (valor === "Cumple Parcialmente") {
                        claseColor = "text-bg-warning"; // amarillo
                    }

                    return `<span class="badge ${claseColor}">${valor}</span>`;
                }
            }


        ],

        language: {
            url: "../json/es-ES.json"
        }
    });
} */
function listar() {
    const condicion = $('#cbc_id').val();
    const oficina = $('#oficina_id').val();


    const filtrosAplicados = [];

    if (oficina) {
        filtrosAplicados.push(`Oficina: ${$("#oficina_id option:selected").text()}`);
    }
    if (condicion) {
        filtrosAplicados.push(`Condición: ${$("#cbc_id option:selected").text()}`);
    }


    if (filtrosAplicados.length > 0) {
        $("#filtros-aplicados").show();
        $("#filtros-mostrados").html(
            filtrosAplicados.map(filtro => `<span class="badge text-bg-primary">${filtro}</span>`).join(" ")
        );
    } else {
        $("#filtros-aplicados").hide();
    }

    tabla = $('#tblReporte').DataTable({
        destroy: true,
        ordering: true,
        pageLength: 25,
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "Todos"]
        ],

        ajax: {
            url: "../controladores/estadisticas.php?op=listarReporte",
            type: "POST",
            dataType: "json",
            data: {
                cbc_id: condicion,
                oficina_id: oficina
            },
            dataSrc: "aaData"
        },
        dom: '<"d-flex justify-content-between align-items-center mt-2"lBf>rtip',

        buttons: [
            {
                extend: 'excelHtml5',
                title: 'CUMPLIMIENTO DE LA MATRIZ DE MANTENIMIENTO DE LAS CBC 2025',
                text: '<i class="ti ti-file-export"></i> Exportar Excel',
                className: 'btn btn-success',
                attr: {
                    style: 'font-size: 0.7rem; margin-top: 8px;margin-left: 15px;'
                }
            },
            {
                text: '<i class="ti ti-printer"></i> Imprimir Tabla',
                className: 'btn btn-primary',
                attr: {
                    style: 'font-size: 0.7rem; margin-top: 8px; margin-left: 15px;'
                },
                action: function () {
                    imprimirTodosCombinados(); // Llama tu función personalizada para imprimir
                }
            }
        ],

        rowGroup: {
            dataSrc: ['cbc_nombre', 'componente_nombre', 'indicador_nombre', 'medio_nombre']
        },

        columns: [
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.cbc_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.componente_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0">${row.indicador_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<p class="mb-0" data-id="${row.medio_id}">${row.medio_nombre}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const evidencia = row.evidencia_nombre;
                    if (!evidencia || evidencia.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f; font-weight:bold;">No aplica</span>`;
                    }
                    return `<p class="mb-0">${evidencia}</p>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const responsables = row.oficinas_vinculadas;
                    if (!responsables || responsables.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f;">No aplica</span>`;
                    }
                    return `<small class="mb-0">${responsables}</small>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const responsables = row.coordinadores_vinculados;
                    if (!responsables || responsables.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f;">Sin coordinadores</span>`;
                    }
                    return `<small class="mb-0">${responsables}</small>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const archivo = row.archivo_presentacion;

                    if (!archivo || archivo === "No disponible") {
                        return `<span class="badge class="badge" style="background:#efefef; color:#000;><i class="bi bi-file-earmark-x-fill"></i> No disponible</span>`;
                    }

                    const ext = archivo.split('.').pop().toLowerCase();
                    let icono = '';
                    let contenido = '';

                    switch (ext) {
                        case 'pdf':
                            icono = `<i class="fas fa-file-pdf" style="color: #d9534f;"></i>`; // rojo
                            contenido = `
                            <a href="#" class="text-decoration-none" onclick="verPDF('${archivo}')">
                                <span class="badge" style="background:#efefef; color:#000; max-width: 400px; word-wrap: break-word; display: inline-block; white-space: normal;">
                                    ${icono} ${archivo}
                                </span>

                            </a>`;
                            break;
                        case 'docx':
                            icono = `<i class="fas fa-file-word" style="color: #0d6efd;"></i>`; // azul
                            break;
                        case 'zip':
                        case 'rar':
                            icono = 'fas fa-file-archive';
                            break;
                        default:
                            icono = 'fas fa-file-archive';
                    }

                    // Si no es PDF (porque PDF ya tiene contenido)
                    if (!contenido) {
                        contenido = `
                <a href="descargar.php?archivo=${archivo}" class="text-decoration-none" download>
                    <span class="badge" style="background:#efefef; color:#000;">
                        <i class="${icono}"></i> ${archivo}
                    </span>
                </a>`;
                    }

                    return contenido;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    const evidencia = row.evidencia_nombre;
                    if (!evidencia || evidencia.trim() === "") {
                        return `<span class="badge" style="background-color: #783c8f;">No aplica</span>`;
                    }

                    // Primero validar si grado_cumplimiento es nulo, vacío o undefined
                    let valor = row.grado_cumplimiento;
                    if (valor === null || valor === undefined || valor.trim() === "") {
                        valor = "Pendiente";
                    }

                    // Si el estado_revision es 'En Revisión' o 'Enviado', siempre forzar 'Pendiente'
                    if (row.estado_revision === "En Revision" || row.estado_revision === "Enviado") {
                        valor = "Pendiente";
                    }

                    let claseColor = "text-bg-secondary";

                    if (valor === "No Cumple") {
                        claseColor = "text-bg-danger";
                    } else if (valor === "Si Cumple") {
                        claseColor = "text-bg-success";
                    } else if (valor === "Cumple Parcialmente") {
                        claseColor = "text-bg-warning";
                    }

                    return `<span class="badge ${claseColor}">${valor}</span>`;
                }
            },
            {
                data: "cumplimiento_medio",
                render: function (data, type, row) {
                    let claseColor = "text-bg-secondary";

                    if (data === "No Cumple") claseColor = "text-bg-danger";
                    else if (data === "Cumple Parcialmente") claseColor = "text-bg-warning";
                    else if (data === "Si Cumple") claseColor = "text-bg-success";
                    else if (data === "No aplica") claseColor = "";

                    if (data === "No aplica") {
                        return `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; width: 100%;">
                    <span class="badge" style="background-color: #783c8f;">No aplica</span>
                </div>`;
                    }

                    return `
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; width: 100%;">
                <span class="badge ${claseColor}">${data}</span>
            </div>`;
                }
            }


        ],
        language: {
            url: "../json/es-ES.json"
        },

        drawCallback: function () {
            combinarCeldas(0);
            combinarCeldas(1);
            combinarCeldas(2);
            combinarCeldasPorId(3, 'medio_id');
            combinarCeldasPorOtraColumna(9, 3);
        }

    });

}

function verPDF(nombreArchivo) {
    const url = `descargar.php?archivo=${nombreArchivo}`;
    document.getElementById('iframePDF').src = url;
    const modal = new bootstrap.Modal(document.getElementById('modalPDF'));
    modal.show();
}

function combinarCeldas(columna) {
    let api = $('#tblReporte').DataTable();
    let rows = api.rows({ page: 'current' }).nodes();
    let last = null;
    let rowspan = 1;

    api.column(columna, { page: 'current' }).nodes().each(function (cell, i) {
        const currentText = $(cell).text().trim();

        if (last === currentText && currentText !== "") {
            $(cell).hide();
            rowspan++;
            $(rows).eq(i - rowspan + 1).find('td').eq(columna).attr('rowspan', rowspan);
        } else {
            last = currentText;
            rowspan = 1;
        }
    });
}

function combinarCeldasPorId(columna) {
    let api = $('#tblReporte').DataTable();
    let rows = api.rows({ page: 'current' }).nodes();
    let lastId = null;
    let rowspan = 1;
    let lastCell = null;

    api.column(columna, { page: 'current' }).nodes().each(function (cell, i) {
        // Obtener el ID único desde el <p> dentro de la celda
        const currentId = $(cell).find('p').data('id'); // CORRECTO: data-id en HTML -> data('id')

        if (lastId === currentId && currentId !== undefined && currentId !== null) {
            $(cell).hide();
            rowspan++;
            $(lastCell).attr('rowspan', rowspan);
        } else {
            lastId = currentId;
            rowspan = 1;
            lastCell = cell;
        }
    });
}


function combinarCeldasPorOtraColumna(columnaObjetivo, columnaComparacion) {
    let api = $('#tblReporte').DataTable();
    let rows = api.rows({ page: 'current' }).nodes();
    let lastId = null;
    let rowspan = 1;
    let lastCell = null;

    api.column(columnaComparacion, { page: 'current' }).nodes().each(function (cell, i) {
        // Tomamos el ID desde el <p data-id> dentro de la celda
        const currentId = $(cell).find('p').data('id');

        const celdaObjetivo = $(rows).eq(i).find('td').eq(columnaObjetivo);

        if (lastId === currentId && currentId !== undefined && currentId !== null) {
            celdaObjetivo.hide();
            rowspan++;
            $(lastCell).attr('rowspan', rowspan);
        } else {
            lastId = currentId;
            rowspan = 1;
            lastCell = celdaObjetivo;
        }
    });
}


function imprimirTodosCombinados() {
    const tabla = $('#tblReporte').DataTable();

    tabla.page.len(-1).draw(); // Muestra todos los registros

    setTimeout(() => {
        combinarCeldas(0);
        combinarCeldas(1);
        combinarCeldas(2);
        combinarCeldas(3);
        combinarCeldasPorOtraColumna(9, 3);

        // Clonamos y limpiamos el contenido
        const tablaClon = document.querySelector("#tblReporte").cloneNode(true);
        tablaClon.querySelectorAll('span').forEach(el => {
            const textoPlano = el.textContent;
            el.replaceWith(textoPlano);
        });

        const tablaHTML = tablaClon.outerHTML;
        const ventana = window.open('', '_blank');

        ventana.document.write(`
            <html>
                <head>
                    <title>REPORTE CBC 2025</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            font-size: 12px; 
                            margin: 20px; 
                            text-align: center;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            font-size: 12px;
                            margin: 0 auto;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 5px;
                            text-align: center;
                            vertical-align: middle;
                        }
                        th {
                            background-color: #0e5fbb;
                            color: #fff;
                        }
                        thead { display: table-header-group; }
                    </style>
                </head>
                <body>
                    <h3>CUMPLIMIENTO DE LA MATRIZ DE MANTENIMIENTO DE LAS CBC 2025</h3>
                    ${tablaHTML}
                </body>
            </html>
        `);

        ventana.document.close();
        ventana.print();

        tabla.page.len(10).draw(); // Regresar paginación normal
    }, 500);
}


function listarMediosCumplimiento() {
    const condicion = $('#cbc_id').val();

    tabla = $('#tblMediosCumplimiento').DataTable({
        destroy: true,
        ordering: true,
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],

        ajax: {
            url: "../controladores/estadisticas.php?op=listarMediosCumplimiento",
            type: "GET",
            dataType: "json",
            data: { cbc_id: condicion },
            dataSrc: "aaData"
        },


        columns: [
            {
                data: "medio_nombre",
                render: function (data, type, row) {
                    return `<p class="mb-0">${data}</p>`;
                }
            },
            {
                data: "cumplimiento_medio",
                render: function (data, type, row) {
                    let claseColor = "text-bg-secondary";

                    if (data === "No Cumple") claseColor = "text-bg-danger";
                    else if (data === "Cumple Parcialmente") claseColor = "text-bg-warning";
                    else if (data === "Si Cumple") claseColor = "text-bg-success";
                    else if (data === "No aplica") claseColor = "";

                    if (data === "No aplica") {
                        return `
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; width: 100%;">
                                <span class="badge" style="background-color: #783c8f;">No aplica</span>
                            </div>`;
                    }

                    return `
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; width: 100%;">
                            <span class="badge ${claseColor}">${data}</span>
                        </div>`;
                }
            }
        ],

        language: {
            url: "../json/es-ES.json"
        }
    });
}

function graficoResumen(condicion = '') {
    const contenedor = document.querySelector('#chartCumplimientoMV');
    if (!contenedor) return;

    $.ajax({
        url: '../controladores/estadisticas.php',
        type: 'GET',
        dataType: 'json',
        data: { op: 'resumenCumplimientoMV', cbc_id: condicion },
        success: function (data) {

            const total = data.no_aplica + data.pendiente + data.no_cumple + data.cumple_parcial + data.si_cumple;

            if (total === 0) {
                contenedor.innerHTML = '<p class="text-center">Sin datos para mostrar</p>';
                return;
            }

            const valores = [data.si_cumple, data.cumple_parcial, data.no_cumple, data.pendiente, data.no_aplica];

            const opciones = {
                chart: { type: 'donut', height: 350, toolbar: { show: false } },
                series: valores,
                labels: ['Si Cumple', 'Cumple Parcialmente', 'No Cumple', 'Pendiente', 'No Aplica'],
                colors: ['#4CAF50', '#FFC107', '#F44336', '#9E9E9E', '#783c8f']
            };

            contenedor.innerHTML = ""; // Limpia antes de renderizar

            window.graficoDonut = new ApexCharts(contenedor, opciones);
            window.graficoDonut.render();
        }
    });
}

function listarMediosCumplimientoPorOficina() {

    const oficina_id = $("#oficina-id").data("oficina-id");
    tabla = $('#tblOficinaMediosCumplimiento').DataTable({
        destroy: true,
        ordering: false,
        pageLength: 10,
        searching: false,


        ajax: {
            url: "../controladores/estadisticas.php?op=listarMediosCumplimientoPorOficina",
            type: "GET",
            dataType: "json",
            data: { oficina_id: oficina_id },
            dataSrc: "aaData"
        },
        columns: [
            {
                data: "evidencia_nombre",
                render: function (data) {
                    return `<p class="mb-0">${data}</p>`;
                }
            },
            {
                data: "estado_revision",
                render: function (data) {
                    return `<p class="mb-0 d-flex justify-content-center">${data}</p>`;
                }
            },
            {
                data: "grado_cumplimiento",
                render: function (data) {
                    let claseColor = "text-bg-secondary";
                    if (data === "No Cumple") claseColor = "text-bg-danger";
                    else if (data === "Cumple Parcialmente") claseColor = "text-bg-warning";
                    else if (data === "Si Cumple") claseColor = "text-bg-success";

                    return `<div class="d-flex justify-content-center"><span class="badge ${claseColor}">${data}</span></div>`;
                }
            }
        ],
        language: { url: "../json/es-ES.json" }
    });
}

function graficoResumenPorOficina() {
    const contenedor = document.querySelector('#chartOficinaCumplimientoMV');
    if (!contenedor) return;

    const oficina_id = $("#oficina-id").data("oficina-id");

    $.ajax({
        url: '../controladores/estadisticas.php',
        type: 'GET',
        dataType: 'json',
        data: { op: 'resumenOficinaCumplimientoMV', oficina_id: oficina_id },
        success: function (data) {
            // Verifica si los datos están vacíos o sin contenido útil
            const total = (data?.pendiente ?? 0) + (data?.no_cumple ?? 0) + (data?.cumple_parcial ?? 0) + (data?.si_cumple ?? 0);

            if (!data || Object.keys(data).length === 0 || total === 0) {
                contenedor.innerHTML = '<p class="text-center">Sin datos para mostrar</p>';
                return;
            }

            const valores = [
                parseInt(data.si_cumple),
                parseInt(data.cumple_parcial),
                parseInt(data.no_cumple),
                parseInt(data.pendiente)
            ];

            const opciones = {
                chart: {
                    type: 'donut',
                    height: 300,
                    toolbar: { show: false }
                },
                series: valores,
                labels: ['Si Cumple', 'Cumple Parcialmente', 'No Cumple', 'Por Revisar'],
                colors: ['#4CAF50', '#FFC107', '#F44336', '#9E9E9E'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%'
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            contenedor.innerHTML = ''; // Limpia antes de renderizar
            const chart = new ApexCharts(contenedor, opciones);
            chart.render();
        },
        error: function () {
            contenedor.innerHTML = '<p class="text-center">Error al cargar los datos</p>';
        }
    });
}


function cargarDatos() {
    $.ajax({
        url: '../controladores/estadisticas.php',
        type: 'GET',
        data: { op: 'evidenciaEstadoPorOficina' },
        dataType: 'json'
    })
        .done(function (json) {

            const datos = json.aaData || [];
            const totalEvidencias = json.totalEvidencias || 0;

            renderCards(datos, totalEvidencias);
        })
        .fail(function (xhr, status, error) {
            console.error('Error cargando datos del gráfico:', error);
        });
}

function renderCards(data, totalEvidencias) {
    const cardsContainer = document.getElementById('cards-container');
    if (!cardsContainer) return; // Solo si existe, continúa

    cardsContainer.innerHTML = '';

    const totalCardHtml = `
       <div class="col-sm-6 col-md-3">
            <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <!-- Contenedor para el título y texto alineado a la izquierda -->
                    <div class="d-flex flex-column align-items-center text-center">
                        <h2 class="text-white m-0 mb-2">${totalEvidencias}</h2>
                        <span class="badge bg-light-primary border border-primary d-inline-flex align-items-center justify-content-center">
                           <b> Porcentaje: 100% </b>
                        </span>
                    </div>

                    <!-- Contenedor para el porcentaje de avance, alineado a la derecha -->
                    <div class="d-flex flex-column align-items-end">
                        <p class="mb-0 mx-3 text-white text-sm">Total de ${totalEvidencias} evidencias asignadas a su oficina.</p>
                    </div>

                    <!-- Icono alineado a la derecha -->
                    <span class="pc-micon">
                        <svg class="pc-icon" style="width: 24px; height: 24px;">
                            <use xlink:href="#custom-element-plus"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    `;
    cardsContainer.innerHTML += totalCardHtml;

    const estadoClases = {
        'Pendientes': '#dc7c1b', // Rojo
        'Enviados': '#dc7c1b', // Amarillo
        'Finalizado': '#dc7c1b', // Verde
    };

    const estadoClasesPorcentaje = {
        'Pendientes': 'bg-light-warning border border-warning',
        'Enviados': 'bg-light-warning border border-warning',
        'Finalizado': 'bg-light-warning border border-warning',
    };

    data.forEach((item, index) => {
        const evidencias = item.TotalPorEstado;
        const porcentaje = ((evidencias / totalEvidencias) * 100).toFixed(2);
        const mensajeEvidencias = `${evidencias} evidencias de ${totalEvidencias} totales`;

        // El color de fondo depende del estado
        const estadoClase = estadoClases[item.estado_revision] || '#e5e7eb'; // Color por defecto (gris claro)
        const clasePorcentaje = estadoClasesPorcentaje[item.estado_revision] || 'bg-light-secondary border-secondary';

        // HTML para la tarjeta con el color de fondo adecuado
        const cardHtml = `
        <div class="col-sm-6 col-md-3">
            <div class="card social-widget-card available-balance-card" style="background-color:${estadoClase};">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <h2 class="text-white m-0 mb-2">${evidencias}</h2>
                        <span class="badge ${clasePorcentaje} d-inline-flex align-items-center justify-content-center">
                            <b>Porcentaje: ${porcentaje}%</b>
                        </span>
                    </div>

                    <!-- Contenedor para el porcentaje de avance, alineado a la derecha -->
                    <div class="d-flex flex-column align-items-end">
                        <h6 class="mb-0 mx-3 text-white">${item.estado_revision}</h6>
                        <p class="mb-0 mx-3 text-white text-sm">${mensajeEvidencias}</p>
                    </div>

                    <!-- Icono alineado a la derecha -->
                    <span class="pc-micon">
                        <svg class="pc-icon" style="width: 24px; height: 24px;">
                            <use xlink:href="#custom-element-plus"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    `;

        cardsContainer.innerHTML += cardHtml;

    });
}


// Ejecutar la carga de datos cuando la página esté lista
document.addEventListener('DOMContentLoaded', function () {
    cargarDatos();
});

function limpiar_filtro() {

    if (choicesOficinas) {
        choicesOficinas.destroy();
        choicesOficinas = null;
    }
    if (choicesCbc) {
        choicesCbc.destroy();
        choicesCbc = null;
    }

    // Limpiar valores antes de reinicializar
    $("#oficina_id").val("").trigger('change');
    $("#cbc_id").val("").trigger('change');

    // Reinicializar Choices.js
    choicesOficinas = new Choices('#oficina_id', {
        searchEnabled: true,
        itemSelectText: 'Presionar',
        searchResultLimit: 5,
        renderChoiceLimit: 5,
        shouldSort: false
    });

    choicesCbc = new Choices('#cbc_id', {
        searchEnabled: true,
        itemSelectText: 'Presionar',
        searchResultLimit: 5,
        renderChoiceLimit: 5,
        shouldSort: false
    });
}
