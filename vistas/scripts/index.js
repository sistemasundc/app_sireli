$(document).ready(function () {
    const oficina_id = $("#oficina-id").data("oficina-id");

    if (oficina_id) {
        cargarNotificaciones(oficina_id);
        setInterval(function () {
            cargarNotificaciones(oficina_id);
        }, 30000); // cada 30 segundos
    } else {
        console.warn("No se encontró oficina_id en el DOM.");
    }

    if (document.querySelector("#totalPorRecibir")) {
        $.post("../controladores/estadisticas.php?op=evidenciasPorRecibir", function (data, status) {
            console.log(data);
            data = JSON.parse(data);
            if (data.total !== undefined) {
                document.querySelector("#totalPorRecibir").textContent = data.total;
            } else {
                document.querySelector("#totalPorRecibir").textContent = "0";
            }
        }).fail(function () {
            document.querySelector("#totalPorRecibir").textContent = "Error";
        });
    }

    if (document.querySelector("#totalPorRevisar")) {
        $.post("../controladores/estadisticas.php?op=evidenciasPorRevisar", function (data, status) {
            console.log(data);
            data = JSON.parse(data);
            if (data.total !== undefined) {
                document.querySelector("#totalPorRevisar").textContent = data.total;
            } else {
                document.querySelector("#totalPorRevisar").textContent = "0";
            }
        }).fail(function () {
            document.querySelector("#totalPorRevisar").textContent = "Error";
        });
    }

    cargarListaNotificaciones(oficina_id);


});

function cargarNotificaciones(oficina_id) {
    $.ajax({
        url: '../controladores/evidencias.php?op=listarNotificacionesPorOficina',
        type: 'POST',
        dataType: 'json',
        data: { oficina_id: oficina_id },
        success: function (response) {
            let notificaciones = response.aaData || [];

            // Contar todas las notificaciones no leídas
            let countNoLeidas = notificaciones.filter(noti => noti.estado_leido == 0).length;

            // Actualizar contador de notificaciones no leídas (si no hay no leídas, mostrará 0)
            $('#notification-count').text(countNoLeidas === 0 ? '0' : countNoLeidas);

            // Ordenar las notificaciones por fecha (de más reciente a más antigua)
            notificaciones.sort((a, b) => new Date(b.fecha_envio) - new Date(a.fecha_envio));

            // Limitar solo a las 5 últimas notificaciones
            notificaciones = notificaciones.slice(0, 5);

            // Limpiar lista actual
            $('#notification-list').empty();

            // Si no hay notificaciones, mostrar un mensaje
            if (notificaciones.length === 0) {
                $('#notification-list').append('<p class="text-center m-3 text-muted">No hay notificaciones disponibles</p>');
            } else {
                // Mostrar las últimas 5 notificaciones
                notificaciones.forEach(noti => {
                    let notiItem = `
                        <a href="misevidencias.php" class="dropdown-item ${noti.estado_leido == 1 ? 'read' : 'unread'}" style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal;" onclick="marcarComoLeida(${noti.noti_id}, this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="icon-container mark-read">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0">${noti.mensaje}</p>
                                    <small class="text-muted">${noti.fecha_envio}</small>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    `;

                    $('#notification-list').append(notiItem);
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar notificaciones:", error);
            console.log(xhr.responseText);
        }
    });
}




// Función para cargar las notificaciones de la oficina
function cargarListaNotificaciones(oficina_id) {
    $.ajax({
        url: '../controladores/evidencias.php?op=listarNotificacionesPorOficina',
        type: 'POST',
        dataType: 'json',
        data: { oficina_id: oficina_id },
        success: function (response) {
            if (response.aaData && response.aaData.length > 0) {
                const notificaciones = response.aaData;

                let leidas = 0;
                let noLeidas = 0;

                $("#notificaciones-list").empty();

                // Recorrer las notificaciones y asignar la clase según su estado
                notificaciones.forEach(function (noti) {
                    if (noti.estado_leido == 1) {
                        leidas++;
                    } else {
                        noLeidas++;
                    }

                    const notificationHTML = `
                        <div class="col" data-id="${noti.noti_id}" class="notificacion">
                            <div class="card shadow-sm mb-3 ${noti.estado_leido == 1 ? 'read' : 'unread'}">
                                <div class="card-body d-flex align-items-center">
                                    <!-- Icono SVG del calendario en círculo -->
                                    <div class="icono-calendario mr-3">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-calendar-1"></use>
                                        </svg>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">${noti.mensaje}</p>
                                        <small class="text-muted">${noti.fecha_envio}</small>
                                    </div>
                                    <!-- Icono para marcar como leída -->
                                    <div class="mark-read" onclick="marcarComoLeida(${noti.noti_id}, this)">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    // Agregar la notificación al contenedor
                    $("#notificaciones-list").append(notificationHTML);
                });

                // Actualizar los contadores de notificaciones leídas y no leídas
                $("#notificaciones-leidas").text(leidas);
                $("#notificaciones-no-leidas").text(noLeidas);
            } else {
                $("#notificaciones-list").empty();
                $("#notificaciones-list").append(`
                    <div class="col-12 text-center text-muted">
                        <p>No hay notificaciones para esta oficina.</p>
                    </div>
                `);
                $("#notificaciones-leidas").text(0);
                $("#notificaciones-no-leidas").text(0);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar las notificaciones:", error);
        }
    });
}

// Función para marcar la notificación como leída
function marcarComoLeida(noti_id, element) {

    $(element).prop('disabled', true);
    $(element).css('pointer-events', 'none');  // Desactiva la interacción del ícono
    $(element).find("i").css('color', '#007bff');

    $.ajax({
        url: '../controladores/evidencias.php?op=marcarNotificacionLeida',
        type: 'POST',
        dataType: 'json',
        data: { noti_id: noti_id },
        success: function (response) {
            if (response.success) {
                // Cambiar el estado visual de la notificación (marcarla como leída)
                $(element).closest('.card').removeClass('unread').addClass('read');

                let leidas = parseInt($("#notificaciones-leidas").text()) + 1;
                let noLeidas = parseInt($("#notificaciones-no-leidas").text()) - 1;
                $("#notificaciones-leidas").text(leidas);
                $("#notificaciones-no-leidas").text(noLeidas);
            } else {
                alert("Hubo un error al marcar la notificación como leída.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al marcar la notificación como leída:", error);
        }
    });
}





