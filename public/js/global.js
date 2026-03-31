/**
 * Función global para mostrar notificaciones tipo Snack (Toast)
 * @param {string} msg Mensaje a mostrar
 */
function snack(msg) {
    let bar = document.getElementById("snackbar");
    if (!bar) return;
    bar.innerHTML = msg;
    bar.classList.add("show");
    setTimeout(() => {
        bar.classList.remove("show");
    }, 3500);
}

document.addEventListener('DOMContentLoaded', () => {
    // Escuchar mensajes de estado inyectados en el body (si existen)
    const statusMsg = document.body.getAttribute('data-session-status');
    if (statusMsg) {
        snack(statusMsg);
    }
});
