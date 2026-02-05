let tabla;


$(document).ready(function () {
    if (!$.fn.DataTable) {
        console.error("DataTables no está cargado correctamente.");
        return;
    }

    listar();

    $("#formOficina").on("submit", function (e) {
        e.preventDefault();
        registrarOficina(e);
    });

    $(document).on("click", "#btnDesactivar", function () {
        const oficina_id = $(this).data("id");
        desactivarOficina(oficina_id);
    });

    $(document).on("click", "#btnActivar", function () {
        const oficina_id = $(this).data("id");
        activarOficina(oficina_id);
    });

    $(document).on('click', '#btnEditar', function () {
        var oficina_id = $(this).data('id');

        $.ajax({
            url: '../controladores/oficinas.php?op=obtenerOficina',
            type: 'POST',
            data: {
                'oficina_id': oficina_id,
            },
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);

                $('#oficina_id').val(data.oficina_id);
                $('#oficina_nom_actual').val(data.oficina_nom);

                $('#EditarOficinaModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos del usuario');
            }
        });
    });
});


function registrarOficina() {
    let oficina_nom = $("#oficina_nom").val();

    if (oficina_nom === "") {
        mostrarToastAdvertencia("Debe ingresar el nombre de la oficina.");
        return;
    }

    $.post("../controladores/oficinas.php?op=guardar", {
        oficina_nom: oficina_nom
    }, function (response) {
        const data = JSON.parse(response);

        if (data.success) {
            mostrarToastExito("Oficina registrado con éxito.");
            $('#RegistraOficinaModal').modal('hide');
            tabla.ajax.reload(null, true);
            limpiar();
        } else {
            mostrarToastAdvertencia("La oficina ya está registrada. No se pudo completar el registro.");
        }
    }).fail(function (xhr, status, error) {
        mostrarToastAdvertencia("Error en la solicitud: " + error);
    });
}

$("#btnEditarOficina").on("click", function (e) {
    e.preventDefault();  // Prevenir el envío del formulario

    // Obtener los valores del formulario
    var oficina_id = $("#oficina_id").val();
    var oficina_nom = $("#oficina_nom_actual").val();

    if (oficina_nom === "") {
        mostrarToastAdvertencia("El nombre de la oficina no puede estar vacío.");
        return;
    }

    $.ajax({
        url: "../controladores/oficinas.php?op=actualizarOficina",
        type: "POST",
        data: {
            oficina_id: oficina_id,
            oficina_nom: oficina_nom,
        },
        success: function (response) {
            var data = JSON.parse(response);

            // Si la actualización fue exitosa
            if (data.success) {
                mostrarToastExito("Oficina actualizado correctamente.");
                $('#EditarOficinaModal').modal('hide');
                tabla.ajax.reload(null, true);
            } else {
                mostrarToastAdvertencia("La oficina ya está registrada. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
});
// Limpiar el formulario después de registrar
function limpiar() {
    $("#oficina_nom").val("");
}

$('#tblOficinas').on('init.dt', function () {  
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});


function listar() {
    tabla = $('#tblOficinas').DataTable({
        "ordering": true,
        "ajax": {
            url: "../controladores/oficinas.php?op=listar",
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
                                <p class="mb-0">${row.oficina_nom}</p>
                        </div>`;
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
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnEditar" data-bs-target="#EditarOficinaModal" data-id="${row.oficina_id}">
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete">
                                <a class="avtar avtar-xs btn-link-danger btn-pc-default" id="btnDesactivar" data-id="${row.oficina_id}">
                                    <i class="ti ti-trash f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnActivar" data-id="${row.oficina_id}">
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

function desactivarOficina(oficina_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Desactivar oficina?</span>' +
            '</div>',
        showCancelButton: true,
        confirmButtonColor: "#4680ff",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, desactivar",
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
            $.post("../controladores/oficinas.php?op=desactivarOficina", {
                oficina_id: oficina_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Oficina desactivado con éxito."); 
                    tabla.ajax.reload(null, true); 
                } else {
                    mostrarToastAdvertencia("No se pudo desactivar la oficina.");
                }
            });
        }
    });
}

function activarOficina(oficina_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Activar oficina?</span>' +
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
            $.post("../controladores/oficinas.php?op=activarOficina", {
                oficina_id: oficina_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Oficina activado con éxito."); 
                    tabla.ajax.reload(null, true); 
                } else {
                    mostrarToastAdvertencia("No se pudo activar la oficina.");
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


document.getElementById('limpiarCampos').addEventListener('click', function (e) {
    e.preventDefault();
    limpiar();
});