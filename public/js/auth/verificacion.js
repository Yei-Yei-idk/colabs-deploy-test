document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-reenviar');
    if (!btn) return;

    const container = document.querySelector('[data-verificacion-config]');
    if (!container) return;

    // Obtener timestamp del servidor (fuente única de verdad)
    const lastEmailSent = parseInt(container.getAttribute('data-last-email-sent')) || 0;
    const DELAY_SECONDS = 30;

    function updateButtonState() {
        const currentTime = Date.now();
        const elapsedMs = currentTime - lastEmailSent;
        const elapsedSeconds = Math.floor(elapsedMs / 1000);
        const remaining = DELAY_SECONDS - elapsedSeconds;

        if (lastEmailSent > 0 && remaining > 0) {
            btn.disabled = true;
            btn.style.opacity = "0.7";
            btn.innerText = `Reenviar en ${remaining}s...`;
            setTimeout(updateButtonState, 1000);
        } else {
            btn.disabled = false;
            btn.style.opacity = "1";
            btn.innerText = "Reenviar llave de acceso";
        }
    }

    updateButtonState();

    // Evitar envío si está bloqueado
    const formReenviar = document.getElementById('form-reenviar');
    if (formReenviar) {
        formReenviar.addEventListener('submit', (e) => {
            if (btn.disabled) {
                e.preventDefault();
            }
        });
    }
});
