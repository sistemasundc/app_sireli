let tabla;


$(document).ready(function () {
    if (!$.fn.DataTable) {
        console.error("DataTables no está cargado correctamente.");
        return;
    }

    listar();

    $.post("../controladores/componentes.php?op=seleccionarComponente", function (r) {
        $("#componente_id").html(r);
    });

    $("#formIndicador").on("submit", function (e) {
        e.preventDefault();
        registrarIndicador(e);
    });

    $(document).on('click', '#btnEditar', function () {

        var indicador_id = $(this).data('id');

        $.ajax({
            url: '../controladores/indicadores.php?op=obtenerIndicador',
            type: 'POST',
            data: {
                'indicador_id': indicador_id,
            },
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);

                $('#indicador_id').val(data.indicador_id);
                $('#indicador_nombre_actual').val(data.indicador_nombre);

                $('#EditarIndicadorModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos del Indicador');
            }
        });
    });

    $(document).on("click", "#btnDesactivar", function () {
        const indicador_id = $(this).data("id");
        desactivarIndicador(indicador_id);
    });

    $(document).on("click", "#btnActivar", function () {
        const indicador_id = $(this).data("id");
        activarIndicador(indicador_id);
    });
});


$('#tblIndicadores').on('init.dt', function () {
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});



function registrarIndicador() {
    let componente_id = $("#componente_id").val();
    let indicador_nombre = $("#indicador_nombre").val();

    if (indicador_nombre === "" || componente_id === "") {
        mostrarToastAdvertencia("Existen campos vacíos, por favor verifique.");
        return;
    }

    $.post("../controladores/indicadores.php?op=guardar", {
        componente_id, indicador_nombre
    }, function (response) {
        const data = JSON.parse(response);

        if (data.success) {
            mostrarToastExito("Indicador registrado con éxito.");
            tabla.ajax.reload(null, true);
            limpiar();
        } else {
            mostrarToastAdvertencia("No se pudo completar el registro.");
        }
    }).fail(function (xhr, status, error) {
        mostrarToastAdvertencia("Error en la solicitud: " + error);
    });
}

function limpiar() {
    $("#indicador_nombre").val("");
    $("#componente_id").val("");
}

function listar() {
    tabla = $('#tblIndicadores').DataTable({
        "ordering": true,
        "ajax": {
            url: "../controladores/indicadores.php?op=listar",
            type: "POST",
            dataType: "json",
            dataSrc: "aaData"
        },
        "columns": [
            {
                "data": null,
                "orderable": true,
                "render": function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },

            {
                "data": null,
                "render": function (data, type, row) {
                    return `<span class="badge bg-light-primary ms-2">${row.componente_nombre}</span>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.indicador_nombre}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<span class="badge bg-light-secondary rounded-circle me-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="tooltip on top">${row.numero_medios_verificacion}</span>`;
                }
            },
            {
                "data": "estado",
                "orderable": true,
                "render": function (data, type, row) {
                    return `<span class="badge bg-light-success rounded-pill f-12">${data}</span>`;
                }
            },
            {
                "data": null,
                "orderable": false,
                "render": function (data, type, row) {
                    return `
                        <ul class="list-inline me-auto mb-0 text-center">
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnEditar" data-bs-target="#EditarOficinaModal" data-id="${row.indicador_id}">
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                                <a class="avtar avtar-xs btn-link-danger btn-pc-default" id="btnDesactivar" data-id="${row.indicador_id}">
                                    <i class="ti ti-trash f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnActivar" data-id="${row.indicador_id}">
                                    <i class="ti ti-circle-check f-18"></i>
                                </a>

                            </li>
                        </ul>`;
                }
            } // Botones de acción
        ],
        "destroy": true,
        "pageLength": 10,
        "language": {
            url: "../json/es-ES.json"
        }
    });
}

$("#btnEditarIndicador").on("click", function (e) {
    e.preventDefault(); 

    var indicador_id = $("#indicador_id").val();
    var indicador_nombre_actual = $("#indicador_nombre_actual").val();

    if (indicador_nombre_actual === "") {
        mostrarToastAdvertencia("El nombre del Indicador no puede estar vacío.");
        return;
    }

    $.ajax({
        url: "../controladores/indicadores.php?op=actualizarIndicador",
        type: "POST",
        data: {
            indicador_id: indicador_id,
            indicador_nombre: indicador_nombre_actual
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Indicador actualizada correctamente.");
                $('#EditarIndicadorModal').modal('hide');
                tabla.ajax.reload(null, true);
            } else {
                mostrarToastAdvertencia("El indicador ya está registrado. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
});

function desactivarIndicador(indicador_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Eliminar indicador?</span>' +
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
            $.post("../controladores/indicadores.php?op=desactivarIndicador", {
                indicador_id: indicador_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Indicador eliminado con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se puede eliminar el indicador porque tiene medios de verificacion activos o hubo un error.");
                }
            });
        }
    });
}

function activarIndicador(indicador_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Activar indicador?</span>' +
            '</div>',
        showCancelButton: true,
        confirmButtonColor: "#4680ff",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, activar",
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
            $.post("../controladores/indicadores.php?op=activarIndicador", {
                indicador_id: indicador_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Indicador activada con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se pudo activar la indicador.");
                }
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


/* document.getElementById('limpiarCampos').addEventListener('click', function (e) {
    e.preventDefault();
    limpiar();
}); */

document.addEventListener('DOMContentLoaded', function () {
    const btnLimpiar = document.getElementById('limpiarCampos');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function (e) {
            e.preventDefault();
            limpiar();
        });
    }
});