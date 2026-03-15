/**
 * Lógica global para el dashboard del cliente
 * Notificaciones, Modales y Popups
 */

document.addEventListener('DOMContentLoaded', () => {
    /* ==== MANEJO DE NOTIFICACIONES ==== */
    const btnNotificaciones = document.getElementById('btnNotificaciones');
    const menuNotificaciones = document.getElementById('notificacionesMenu');

    if (btnNotificaciones && menuNotificaciones) {
        btnNotificaciones.addEventListener('click', (e) => {
            e.stopPropagation();
            menuNotificaciones.style.display = menuNotificaciones.style.display === 'flex' ? 'none' : 'flex';
        });

        document.addEventListener('click', () => {
            menuNotificaciones.style.display = 'none';
        });

        menuNotificaciones.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    /* ==== MODAL DE CALIFICACIÓN (LOGICA DE ESTRELLAS) ==== */
    const modalCalificar = document.getElementById('modalCalificar');
    if (modalCalificar) {
        const modalStars = document.querySelectorAll('#modalStarRating .star');
        const modalPuntuacionInput = document.getElementById('modal_puntuacion');
        const modalRatingText = document.getElementById('modalRatingText');
        const modalTextarea = document.getElementById('modal_calif_txt');
        const modalCharCount = document.getElementById('modalCharCount');
        let currentModalRating = 5;

        const updateModalStars = (rating) => {
            modalStars.forEach(star => {
                const starRating = parseInt(star.dataset.rating);
                star.classList.toggle('active', starRating <= rating);
            });
        };

        modalStars.forEach(star => {
            star.addEventListener('click', function() {
                currentModalRating = parseInt(this.dataset.rating);
                if (modalPuntuacionInput) modalPuntuacionInput.value = currentModalRating;
                updateModalStars(currentModalRating);
                if (modalRatingText) modalRatingText.textContent = `${currentModalRating} de 5 estrellas`;
            });

            star.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.dataset.rating);
                updateModalStars(hoverRating);
            });
        });

        const starRatingContainer = document.getElementById('modalStarRating');
        if (starRatingContainer) {
            starRatingContainer.addEventListener('mouseleave', () => {
                updateModalStars(currentModalRating);
            });
        }

        if (modalTextarea && modalCharCount) {
            modalTextarea.addEventListener('input', function() {
                modalCharCount.textContent = this.value.length;
            });
        }

        window.addEventListener('click', (e) => {
            if (e.target === modalCalificar) closeReviewModal();
        });
    }

    /* ==== CIERRE DE POPUP LOGOUT AL CLICK FUERA ==== */
    const logoutPopup = document.getElementById("logoutPopup");
    if (logoutPopup) {
        logoutPopup.addEventListener("click", function(e) {
            if (e.target === this) {
                closeLogoutPopup();
            }
        });
    }
});

/* ==== FUNCIONES GLOBALES (ACCESIBLES DESDE BLADE) ==== */

function openLogoutPopup() {
    const popup = document.getElementById("logoutPopup");
    if (popup) popup.style.display = "flex";
    
    const menuPerfil = document.getElementById("menuPerfil");
    if (menuPerfil) {
        menuPerfil.classList.remove("show");
        menuPerfil.hidden = true;
    }
}

function closeLogoutPopup() {
    const popup = document.getElementById("logoutPopup");
    if (popup) popup.style.display = "none";
}

function openReviewModal(espacioId, reservaId) {
    const modal = document.getElementById('modalCalificar');
    if (!modal) return;

    document.getElementById('modal_espacio_id').value = espacioId;
    document.getElementById('modal_reserva_id').value = reservaId;
    modal.classList.remove('modal-hidden');
    
    // Resetear valores
    const puntuacionInput = document.getElementById('modal_puntuacion');
    const ratingText = document.getElementById('modalRatingText');
    const textarea = document.getElementById('modal_calif_txt');
    const charCount = document.getElementById('modalCharCount');

    if (puntuacionInput) puntuacionInput.value = 5;
    if (ratingText) ratingText.textContent = "5 de 5 estrellas";
    if (textarea) textarea.value = '';
    if (charCount) charCount.textContent = '0';
    
    // Resetear estrellas (suponiendo que existe la función interna o el evento)
    const modalStars = document.querySelectorAll('#modalStarRating .star');
    modalStars.forEach(star => {
        star.classList.toggle('active', parseInt(star.dataset.rating) <= 5);
    });
}

function closeReviewModal() {
    const modal = document.getElementById('modalCalificar');
    if (modal) modal.classList.add('modal-hidden');
}
