document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-reenviar');
    if (!btn) return;

    // Obtenemos los datos inyectados desde el HTML (vía data-attributes)
    const container = document.querySelector('[data-verificacion-config]');
    if (!container) return;

    const intentos = parseInt(container.getAttribute('data-intentos')) || 0;
    const recienEnviado = container.getAttribute('data-recien-enviado') === 'true';

    // Verificamos si ya hay un bloqueo activo en localStorage
    let unlockTime = localStorage.getItem('verification_unlock_time');
    
    // Si NO hay bloqueo activo, iniciamos uno de 30s por defecto al entrar
    // o al detectar un reenvío exitoso.
    if (!unlockTime) {
        if (recienEnviado || intentos === 0) {
            let wait = (intentos >= 2) ? 60 : 30; 
            unlockTime = Date.now() + (wait * 1000);
            localStorage.setItem('verification_unlock_time', unlockTime);
        }
    } else if (recienEnviado) {
        // Si ya hay un timer pero se detecta un reenvío exitoso (status en sesión),
        // reiniciamos el bloqueo según la escala (30s o 60s)
        let wait = (intentos >= 2) ? 60 : 30;
        unlockTime = Date.now() + (wait * 1000);
        localStorage.setItem('verification_unlock_time', unlockTime);
    }

    // Función recursiva para el contador
    function checkTimer() {
        const currentUnlock = localStorage.getItem('verification_unlock_time');
        if (currentUnlock && Date.now() < parseInt(currentUnlock)) {
            let remaining = Math.ceil((parseInt(currentUnlock) - Date.now()) / 1000);
            btn.disabled = true;
            btn.style.opacity = "0.7";
            btn.innerText = `Reenviar en ${remaining}s...`;
            setTimeout(checkTimer, 1000);
        } else {
            btn.disabled = false;
            btn.style.opacity = "1";
            btn.innerText = "Reenviar llave de acceso";
            localStorage.removeItem('verification_unlock_time');
        }
    }

    checkTimer();
    
    // Evitar envío por teclado si está bloqueado
    const formReenviar = document.getElementById('form-reenviar');
    if (formReenviar) {
        formReenviar.addEventListener('submit', (e) => {
            if (btn.disabled) {
                e.preventDefault();
            }
        });
    }
});
