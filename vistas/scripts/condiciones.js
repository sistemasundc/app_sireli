let tabla;


$(document).ready(function () {
    if (!$.fn.DataTable) {
        console.error("DataTables no está cargado correctamente.");
        return;
    }

    listar();

    $("#formCondicion").on("submit", function (e) {
        e.preventDefault();
        registrarCondicion(e);
    });

    $(document).on('click', '#btnEditar', function () {
        var cbc_id = $(this).data('id');

        $.ajax({
            url: '../controladores/condiciones.php?op=obtenerCondicion',
            type: 'POST',
            data: {
                'cbc_id': cbc_id,
            },
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);

                $('#cbc_id').val(data.cbc_id);
                $('#cbc_nombre_actual').val(data.cbc_nombre);
                $('#cbc_descripcion_actual').val(data.cbc_descripcion);

                $('#EditarCondicionModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos del usuario');
            }
        });
    });

    $(document).on("click", "#btnDesactivar", function () {
        const cbc_id = $(this).data("id");
        desactivarCondicion(cbc_id);
    });

    $(document).on("click", "#btnActivar", function () {
        const cbc_id = $(this).data("id");
        activarCondicion(cbc_id);
    });
});


$('#tblCondiciones').on('init.dt', function () {
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});



function registrarCondicion() {
    let cbc_nombre = $("#cbc_nombre").val();
    let cbc_descripcion = $("#cbc_descripcion").val();

    if (cbc_nombre === "" || cbc_descripcion === "") {
        mostrarToastAdvertencia("Existen campos vacíos, por favor verifique.");
        return;
    }

    $.post("../controladores/condiciones.php?op=guardar", {
        cbc_nombre, cbc_descripcion
    }, function (response) {
        const data = JSON.parse(response);

        if (data.success) {
            mostrarToastExito("Condición registrado con éxito.");
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
    $("#cbc_nombre").val("");
    $("#cbc_descripcion").val("");
}

function listar() {
    tabla = $('#tblCondiciones').DataTable({
        "ordering": true,
        "ajax": {
            url: "../controladores/condiciones.php?op=listar",
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
                                <p class="mb-0">${row.cbc_nombre}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.cbc_descripcion}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<span class="badge bg-light-secondary rounded-circle me-0">${row.numero_componentes}</span>`;
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
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Edit">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnEditar" data-bs-target="#EditarCondicionModal" data-id="${row.cbc_id}">
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Delete">
                                <a class="avtar avtar-xs btn-link-danger btn-pc-default" id="btnDesactivar" data-id="${row.cbc_id}">
                                    <i class="ti ti-trash f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnActivar" data-id="${row.cbc_id}">
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


$("#btnEditarCondicion").on("click", function (e) {
    e.preventDefault();

    var cbc_id = $("#cbc_id").val();
    var cbc_nombre_actual = $("#cbc_nombre_actual").val();
    var cbc_descripcion_actual = $("#cbc_descripcion_actual").val();

    if (cbc_nombre_actual === "" || cbc_descripcion_actual === "") {
        mostrarToastAdvertencia("El nombre de la condición no puede estar vacío.");
        return;
    }

    $.ajax({
        url: "../controladores/condiciones.php?op=actualizarCondicion",
        type: "POST",
        data: {
            cbc_id: cbc_id,
            cbc_nombre: cbc_nombre_actual,
            cbc_descripcion: cbc_descripcion_actual
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Condición actualizada correctamente.");
                $('#EditarCondicionModal').modal('hide');
                tabla.ajax.reload(null, true);
            } else {
                mostrarToastAdvertencia("La condicion ya está registrada. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
});


function desactivarCondicion(cbc_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Eliminar Condición?</span>' +
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

            $.post("../controladores/condiciones.php?op=desactivarCondicion", {
                cbc_id: cbc_id
            }, function (response) {

                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Condición eliminada con éxito.");
                    tabla.ajax.reload(null, true);
                } else {

                    mostrarToastAdvertencia("No se puede eliminar la condición porque tiene componentes activos o hubo un error.");
                }
            }).fail(function (xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            });
        }
    });
}

function activarCondicion(cbc_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Activar Condicion?</span>' +
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
            $.post("../controladores/condiciones.php?op=activarCondicion", {
                cbc_id: cbc_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Condición activada con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se pudo activar la condicion.");
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