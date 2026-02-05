let tabla;


$(document).ready(function () {
    if (!$.fn.DataTable) {
        console.error("DataTables no está cargado correctamente.");
        return;
    }

    listar();

    $.post("../controladores/condiciones.php?op=seleccionarCondicion", function (r) {
        $("#cbc_id").html(r);
    });

    $("#formComponente").on("submit", function (e) {
        e.preventDefault();
        registrarComponente(e);
    });

    $(document).on('click', '#btnEditar', function () {
        
        var componente_id = $(this).data('id');

        $.ajax({
            url: '../controladores/componentes.php?op=obtenerComponente',
            type: 'POST',
            data: {
                'componente_id': componente_id,
            },
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);

                $('#componente_id').val(data.componente_id);
                $('#componente_nombre_actual').val(data.componente_nombre);

                $('#EditarComponenteModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos del Componente');
            }
        });
    });
    
    $(document).on("click", "#btnDesactivar", function () {
        const componente_id = $(this).data("id");
        desactivarComponente(componente_id);
    });

    $(document).on("click", "#btnActivar", function () {
        const componente_id = $(this).data("id");
        activarComponente(componente_id);
    });

});


$('#tblComponentes').on('init.dt', function () {
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});



function registrarComponente() {
    let cbc_id = $("#cbc_id").val();
    let componente_nombre = $("#componente_nombre").val();

    if (componente_nombre === "" || cbc_id === "") {
        mostrarToastAdvertencia("Existen campos vacíos, por favor verifique.");
        return;
    }

    $.post("../controladores/componentes.php?op=guardar", {
        cbc_id, componente_nombre
    }, function (response) {
        const data = JSON.parse(response);

        if (data.success) {
            mostrarToastExito("Componente registrado con éxito.");
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
    $("#componente_nombre").val("");
    $("#cbc_id").val("");
}

function listar() {
    tabla = $('#tblComponentes').DataTable({
        "ordering": true,
        "ajax": {
            url: "../controladores/componentes.php?op=listar",
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
                    return `<span class="badge bg-light-primary ms-2">${row.cbc_nombre}</span>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.componente_nombre}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<span class="badge bg-light-secondary rounded-circle me-0" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="tooltip on top">${row.numero_indicadores}</span>`;
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
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnEditar" data-id="${row.componente_id}">
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" aria-label="Delete">
                                <a class="avtar avtar-xs btn-link-danger btn-pc-default" id="btnDesactivar" data-id="${row.componente_id}">
                                    <i class="ti ti-trash f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnActivar" data-id="${row.componente_id}">
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


$("#btnEditarComponente").on("click", function (e) {
    e.preventDefault(); 

    var componente_id = $("#componente_id").val();
    var componente_nombre_actual = $("#componente_nombre_actual").val();

    if (componente_nombre_actual === "") {
        mostrarToastAdvertencia("El nombre de la componente no puede estar vacío.");
        return;
    }

    $.ajax({
        url: "../controladores/componentes.php?op=actualizarComponente",
        type: "POST",
        data: {
            componente_id: componente_id,
            componente_nombre: componente_nombre_actual
        },
        success: function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                mostrarToastExito("Componente actualizada correctamente.");
                $('#EditarComponenteModal').modal('hide');
                tabla.ajax.reload(null, true);
            } else {
                mostrarToastAdvertencia("El componente ya está registrada. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
});

function desactivarComponente(componente_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Eliminar Componente?</span>' +
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
            $.post("../controladores/componentes.php?op=desactivarComponente", {
                componente_id: componente_id
            }, function (response) {
                const data = JSON.parse(response);

                 if (data.success) {
                    mostrarToastExito("Componente eliminado con éxito.");
                    tabla.ajax.reload(null, true); 
                } else {
                    
                    mostrarToastAdvertencia("No se puede eliminar el componente porque tiene indicadores activos o hubo un error.");
                }
            });
        }
    });
}

function activarComponente(componente_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Activar Componente?</span>' +
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
            $.post("../controladores/componentes.php?op=activarComponente", {
                componente_id: componente_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Componente activada con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se pudo activar la componente.");
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