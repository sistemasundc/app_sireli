let tabla;


$(document).ready(function () {
    if (!$.fn.DataTable) {
        console.error("DataTables no está cargado correctamente.");
        return;
    }

    listar();

    $.post("../controladores/indicadores.php?op=seleccionarIndicador", function (r) {
        $("#indicador_id").html(r);
    });

    $("#formVerificacion").on("submit", function (e) {
        e.preventDefault();
        registrarVerificacion(e);
    });

    $(document).on('click', '#btnEditar', function () {

        var medio_id = $(this).data('id');

        $.ajax({
            url: '../controladores/verificaciones.php?op=obtenerMedio',
            type: 'POST',
            data: {
                'medio_id': medio_id,
            },
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);

                $('#medio_id').val(data.medio_id);
                $('#medio_nombre_actual').val(data.medio_nombre);

                $('#EditarMedioModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos del Medio');
            }
        });
    });

    $(document).on("click", "#btnDesactivar", function () {
        const medio_id = $(this).data("id");
        desactivarMedio(medio_id);
    });

    $(document).on("click", "#btnActivar", function () {
        const medio_id = $(this).data("id");
        activarMedio(medio_id);
    });
});


$('#tblVerificaciones').on('init.dt', function () {
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});



function registrarVerificacion() {
    let indicador_id = $("#indicador_id").val();
    let medio_nombre = $("#medio_nombre").val();

    if (medio_nombre === "" || indicador_id === "") {
        mostrarToastAdvertencia("Existen campos vacíos, por favor verifique.");
        return;
    }

    $.post("../controladores/verificaciones.php?op=guardar", {
        indicador_id, medio_nombre
    }, function (response) {
        const data = JSON.parse(response);

        if (data.success) {
            mostrarToastExito("Medios de Verificación registrado con éxito.");
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
    $("#medio_nombre").val("");
    $("indicador_id").val("");
}

function listar() {
    tabla = $('#tblVerificaciones').DataTable({
        "ordering": true,
        "ajax": {
            url: "../controladores/verificaciones.php?op=listar",
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
                    return `
                        <div class="row">
                                <p class="mb-0">${row.indicador_nombre}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.medio_nombre}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<span class="badge bg-light-success ms-2">${row.cantidad_evidencias}</span>`;
                }
            },
            /*   {
                     "data": null,
                     "render": function (data, type, row) {
                         return `<span class="badge bg-light-secondary rounded-circle me-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="tooltip on top">${row.numero_medios_verificacion}</span>`;
                     }
                 }, */
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
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Edit">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnEditar" data-id="${row.medio_id}">
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Delete">
                                <a class="avtar avtar-xs btn-link-danger btn-pc-default" id="btnDesactivar" data-id="${row.medio_id}">
                                    <i class="ti ti-trash f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnActivar" data-id="${row.medio_id}">
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

$("#btnEditarMedio").on("click", function (e) {
    e.preventDefault(); 

    var medio_id = $("#medio_id").val();
    var medio_nombre_actual = $("#medio_nombre_actual").val();

    if (medio_nombre_actual === "") {
        mostrarToastAdvertencia("El nombre del Medio de Verificación no puede estar vacío.");
        return;
    }

    $.ajax({
        url: "../controladores/verificaciones.php?op=actualizarMedio",
        type: "POST",
        data: {
            medio_id: medio_id,
            medio_nombre: medio_nombre_actual
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Medio de Verificacion actualizada correctamente.");
                $('#EditarMedioModal').modal('hide');
                tabla.ajax.reload(null, true);
            } else {
                mostrarToastAdvertencia("El Medio de Verificación ya está registrada. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
});

function desactivarMedio(medio_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Eliminar Medio de Verificación?</span>' +
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
            $.post("../controladores/verificaciones.php?op=desactivarMedio", {
                medio_id: medio_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Medio de Verificación eliminado con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se puede eliminar el medio porque tiene evidencias activas o hubo un error.");
                }
            });
        }
    });
}

function activarMedio(medio_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Activar Medio de Verificación?</span>' +
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
            $.post("../controladores/verificaciones.php?op=activarMedio", {
                medio_id: medio_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Medio de Verificación activada con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se pudo activar el Medio de Verificación.");
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


document.addEventListener('DOMContentLoaded', function () {
    const btnLimpiar = document.getElementById('limpiarCampos');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function (e) {
            e.preventDefault();
            limpiar();
        });
    }
});
