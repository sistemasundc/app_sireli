let tabla;

$(document).ready(function () {

    mostrarDatosUsuario();

    $("#btnActualizarPerfil").click(function () {
        actualizarMiUsuario();
    });

    if (!$.fn.DataTable) {
        console.error("DataTables no está cargado correctamente.");
        return;
    }

    listar();

    $("#formUsuario").on("submit", function (e) {
        e.preventDefault();
        registrarUsuario(e);
    });

    $.post("../controladores/roles.php?op=seleccionarRol", function (r) {
        $("#rol_id").html(r);
    });
    $.post("../controladores/oficinas.php?op=seleccionarOficina", function (r) {
        $("#oficina_id").html(r);

    });

    $(document).on("click", "#btnDesactivar", function () {
        const usu_id = $(this).data("id");
        desactivarUsuario(usu_id);
    });

    $(document).on("click", "#btnActivar", function () {
        const usu_id = $(this).data("id");
        activarUsuario(usu_id);
    });

    $(document).on('click', '#btnEditar', function () {
        var usu_id = $(this).data('id');

        $.post("../controladores/roles.php?op=seleccionarRol", function (r) {
            $("#rol_id_actual").html(r);
        });

        $.post("../controladores/oficinas.php?op=seleccionarOficina", function (r) {
            $("#oficina_id_actual").html(r);
        });

        $.ajax({
            url: '../controladores/usuarios.php?op=obtenerUsuario',
            type: 'POST',
            data: {
                'usu_id': usu_id,
            },
            success: function (response) {
               /*  console.log(response); */
                var data = JSON.parse(response);

                $('#usu_id').val(data.usu_id);
                $('#usu_nom_actual').val(data.usu_nom);
                $('#usu_ape_actual').val(data.usu_ape);
                $('#usu_correo_actual').val(data.usu_correo);
                $('#usu_telf_actual').val(data.usu_telf);
                $('#rol_id_actual').val(data.rol_id);
                $('#oficina_id_actual').val(data.oficina_id);

                $('#EditarUsuarioModal').modal('show');
            },
            error: function () {
                alert('Error al obtener los datos del usuario');
            }
        });


    });

});


function registrarUsuario() {
    let usu_nom = $("#usu_nom").val();
    let usu_ape = $("#usu_ape").val();
    let usu_telf = $("#usu_telf").val();
    let usu_correo = $("#usu_correo").val();
    let rol_id = $("#rol_id").val();
    let oficina_id = $("#oficina_id").val();
    let fech_crea = new Date().toISOString().slice(0, 19).replace('T', ' ');

    if (usu_nom === "" || usu_ape === "" || usu_correo === "" || rol_id === "" || oficina_id === "") {
        mostrarToastAdvertencia("Existen campos vacíos, por favor verifique.");
        return;
    }

    // Validación del correo
    const correoRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!correoRegex.test(usu_correo)) {
        mostrarToastAdvertencia("El correo electrónico ingresado no es válido.");
        return;
    }

    // Validación de celular solo si el campo no está vacío
    if (usu_telf !== "") {
        const telfRegex = /^[0-9]{9}$/;
        if (!telfRegex.test(usu_telf)) {
            mostrarToastAdvertencia("El número de celular ingresado no es válido.");
            return;
        }
    }

    $.post("../controladores/usuarios.php?op=guardar", {
        usu_nom, usu_ape, usu_telf, usu_correo, rol_id, oficina_id, fech_crea
    }, function (response) {
        const data = JSON.parse(response);

        if (data.success) {
            mostrarToastExito("Usuario registrado con éxito.");
            $('#RegistraUsuarioModal').modal('hide');
            tabla.ajax.reload(null, true);
            limpiar();
        } else {
            mostrarToastAdvertencia("El correo ya está registrado. No se pudo completar el registro.");
        }
    }).fail(function (xhr, status, error) {
        mostrarToastAdvertencia("Error en la solicitud: " + error);
    });
}

$("#btnEditarUsuario").on("click", function (e) {
    e.preventDefault();

    // Obtener los datos del formulario
    var usu_id = $("#usu_id").val();
    var usu_nom = $("#usu_nom_actual").val();
    var usu_ape = $("#usu_ape_actual").val();
    var usu_correo = $("#usu_correo_actual").val();
    var usu_telf = $("#usu_telf_actual").val();
    var rol_id = $("#rol_id_actual").val();
    var oficina_id = $("#oficina_id_actual").val();

    $.ajax({
        url: "../controladores/usuarios.php?op=actualizarUsuario",
        type: "POST",
        data: {
            usu_id: usu_id,
            usu_nom: usu_nom,
            usu_ape: usu_ape,
            usu_correo: usu_correo,
            usu_telf: usu_telf,
            rol_id: rol_id,
            oficina_id: oficina_id
        },
        success: function (response) {
            var data = JSON.parse(response);

            // Si la actualización fue exitosa
            if (data.success) {
                mostrarToastExito("Usuario actualizado correctamente.");
                $('#EditarUsuarioModal').modal('hide');
                tabla.ajax.reload(null, true);  // Si estás usando una tabla con DataTables
            } else {
                mostrarToastAdvertencia("El correo ya está registrado. No se pudo completar el registro.");
            }
        },
        error: function () {
            mostrarToastAdvertencia("Error al realizar la solicitud de actualización.");
        }
    });
});

function mostrarDatosUsuario() {
    const usu_id = $("#usu-id").data("usu-id");

    $.ajax({
        url: "../controladores/usuarios.php?op=mostrarDatosUsuario",
        type: "POST",
        data: {
            usu_id: usu_id
        },
        success: function (response) {

            var data = JSON.parse(response);
            var usuario = data.aaData[0];
            /* Primer card */
            $("#usu_completo_perfil").text(usuario.usu_nom + " " + usuario.usu_ape);
            $("#usu_rol_perfil").text(usuario.rol_nom);
            if (usuario.estado == 1) {
                $("#usu_estado_perfil").text("Activo");
            } else {
                $("#usu_estado_perfil").text("Inactivo");
            }
            $("#usu_correo_perfil").text(usuario.usu_correo);
            $("#usu_telf_perfil").html(usuario.usu_telf ? "(+51) " + usuario.usu_telf : "<span>No registrado</span>");
            $("#oficina_nom_perfil").text(usuario.oficina_nom);

            /* Segundo card */
            $("#usu_completo").text(usuario.usu_nom + " " + usuario.usu_ape);
            $("#usu_correo").text(usuario.usu_correo);
            $("#usu_telf").html(usuario.usu_telf ? "(+51) " + usuario.usu_telf : "<span>No registrado</span>");
            $("#oficina_nom").text(usuario.oficina_nom);
            $("#usu_rol").text(usuario.rol_nom);
            $("#fech_crea").text(usuario.fech_crea);

            /* Para actulizacion */
            $("#usu_nom_actual").val(usuario.usu_nom);
            $("#usu_ape_actual").val(usuario.usu_ape);
            $("#usu_telf_actual").val(usuario.usu_telf);


        },



        error: function () {
            mostrarToastAdvertencia("Error al mostrar.");
        }
    });

}

function actualizarMiUsuario() {
    const usu_id = $("#usu-id").data("usu-id");
    const usu_nom = $("#usu_nom_actual").val();
    const usu_ape = $("#usu_ape_actual").val();
    const usu_telf = $("#usu_telf_actual").val();

    if (!usu_nom || !usu_ape || !usu_telf) {
        mostrarToastAdvertencia("Todos los campos son obligatorios.");
        return;
    }

    if (usu_telf.length !== 9 || isNaN(usu_telf)) {
        mostrarToastAdvertencia("El número de teléfono debe tener 9 dígitos.");
        return;
    }

    $.ajax({
        url: "../controladores/usuarios.php?op=actualizarMiUsuario",
        type: "POST",
        data: {
            usu_id: usu_id,
            usu_nom: usu_nom,
            usu_ape: usu_ape,
            usu_telf: usu_telf
        },
        success: function (response) {
           /*  console.log(response); */

            var result = JSON.parse(response);
            if (result.success) {
                mostrarToastExito("Usuario actualizado correctamente.");
                mostrarDatosUsuario();

                Swal.fire({
                    text: "Tu actualización de datos se reflejará al cerrar sesión y volver a iniciar sesión.",
                    confirmButtonColor: "#4680ff",  
                    confirmButtonText: "Aceptar",
                    customClass: {
                        popup: 'rounded-4 shadow p-3 custom-swal-width',  
                        confirmButton: 'btn btn-primary btn-sm px-3 fs-5', 
                        icon: 'm-1' // Estilo del icono
                    }
                });
            } else {
                mostrarToastAdvertencia("El correo ya está registrado. No se pudo completar el registro.");
            }
        },
        error: function () {
            alert("Error de conexión con el servidor");
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


function limpiar() {
    $("#usu_nom").val("");
    $("#usu_ape").val("");
    $("#usu_telf").val("");
    $("#usu_correo").val("");
    $("#rol_id").selectpicker('val', '');
    $("#oficina_id").selectpicker('val', '');
    $("#rol_nom").val("");
    $("#oficina_nom").val("");
}

$('#tblUsuarios').on('init.dt', function () {
    $('.dataTables_filter').addClass('m-3');
    $('.dataTables_filter input[type="search"]').attr('placeholder', 'Buscar...');
    $('.dataTables_length').addClass('m-4');
    $('.dataTables_paginate').addClass('m-3');
    $('.dataTables_info').addClass('text-muted m-3');
});


function listar() {
    tabla = $('#tblUsuarios').DataTable({
        "ordering": true,
        "ajax": {
            url: "../controladores/usuarios.php?op=listar",
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

                            <div class="col">
                                <p class="mb-0 f-w-500">${row.nomcompleto}</p>
                                <p class="text-muted f-12 mb-0">${row.usu_correo}</p>
                            </div>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.usu_telf}</p>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.rol_nom}</p>
                        </div>`;
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
                "data": null,
                "render": function (data, type, row) {
                    return `
                        <div class="row">
                                <p class="mb-0">${row.fech_crea}</p>
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
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnEditar" data-bs-target="#EditarUsuarioModal" data-id="${row.usu_id}">
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-danger btn-pc-default" id="btnDesactivar" data-id="${row.usu_id}">
                                    <i class="ti ti-trash f-18"></i>
                                </a>

                            </li>
                            <li class="list-inline-item align-bottom">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default" id="btnActivar" data-id="${row.usu_id}">
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


function desactivarUsuario(usu_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Desactivar usuario?</span>' +
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
            $.post("../controladores/usuarios.php?op=desactivarUsuario", {
                usu_id: usu_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Usuario desactivado con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se pudo desactivar al usuario.");
                }
            });
        }
    });
}

function activarUsuario(usu_id) {
    Swal.fire({
        title: '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<i class="ti ti-alert-circle fs-2 text-warning"></i>' +
            '<span style="font-size:20px;">¿Activar usuario?</span>' +
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
            $.post("../controladores/usuarios.php?op=activarUsuario", {
                usu_id: usu_id
            }, function (response) {
                const data = JSON.parse(response);

                if (data.success) {
                    mostrarToastExito("Usuario activado con éxito.");
                    tabla.ajax.reload(null, true);
                } else {
                    mostrarToastAdvertencia("No se pudo activar al usuario.");
                }
            });
        }
    });
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