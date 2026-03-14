// ===== SLIDER DE ESPACIOS DESTACADOS =====

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide-card');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');

    if (!slider || !slides.length || !prevBtn || !nextBtn) {
        console.warn('Slider no encontrado en esta página');
        return;
    }

    let currentIndex = 0;
    const visibleCards = getVisibleCards();
    const maxIndex = Math.max(0, slides.length - visibleCards);

    // Detectar número de cards visibles según el ancho de pantalla
    function getVisibleCards() {
        const width = window.innerWidth;
        if (width <= 600) return 1;
        if (width <= 992) return 2;
        return 3;
    }

    // Calcular el ancho de una card incluyendo el gap
    function getSlideWidth() {
        const slideWidth = slides[0].offsetWidth;
        const gap = 20; // gap entre cards
        return slideWidth + gap;
    }

    // Mostrar slide específico
    function showSlide(index) {
        currentIndex = Math.max(0, Math.min(index, maxIndex));
        const slideWidth = getSlideWidth();
        const offset = -slideWidth * currentIndex;
        
        slider.style.transform = `translateX(${offset}px)`;
        
        updateButtons();
    }

    // Actualizar estado de los botones
    function updateButtons() {
        // Deshabilitar botón prev si estamos al inicio
        if (currentIndex === 0) {
            prevBtn.style.opacity = '0.5';
            prevBtn.style.cursor = 'not-allowed';
        } else {
            prevBtn.style.opacity = '1';
            prevBtn.style.cursor = 'pointer';
        }

        // Deshabilitar botón next si estamos al final
        if (currentIndex >= maxIndex) {
            nextBtn.style.opacity = '0.5';
            nextBtn.style.cursor = 'not-allowed';
        } else {
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
        }
    }

    // Event listeners para los botones
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            showSlide(currentIndex - 1);
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentIndex < maxIndex) {
            showSlide(currentIndex + 1);
        }
    });

    // Soporte para deslizar con el mouse (drag)
    let isDragging = false;
    let startPos = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;

    slider.addEventListener('mousedown', dragStart);
    slider.addEventListener('touchstart', dragStart);
    slider.addEventListener('mouseup', dragEnd);
    slider.addEventListener('touchend', dragEnd);
    slider.addEventListener('mousemove', drag);
    slider.addEventListener('touchmove', drag);
    slider.addEventListener('mouseleave', dragEnd);

    function dragStart(e) {
        isDragging = true;
        startPos = getPositionX(e);
        slider.style.cursor = 'grabbing';
    }

    function drag(e) {
        if (!isDragging) return;
        
        const currentPosition = getPositionX(e);
        currentTranslate = prevTranslate + currentPosition - startPos;
    }

    function dragEnd() {
        isDragging = false;
        slider.style.cursor = 'grab';

        const movedBy = currentTranslate - prevTranslate;

        // Si se movió más de 50px, cambiar de slide
        if (movedBy < -50 && currentIndex < maxIndex) {
            currentIndex++;
        }
        if (movedBy > 50 && currentIndex > 0) {
            currentIndex--;
        }

        showSlide(currentIndex);
        prevTranslate = currentTranslate;
    }

    function getPositionX(e) {
        return e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
    }

    // Navegación con teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            prevBtn.click();
        } else if (e.key === 'ArrowRight') {
            nextBtn.click();
        }
    });

    // Recalcular al cambiar tamaño de ventana
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            currentIndex = 0;
            showSlide(0);
        }, 250);
    });

    //Auto-play opcional (comentado por defecto)
    
    let autoPlayInterval;
    function startAutoPlay() {
        autoPlayInterval = setInterval(function() {
            if (currentIndex >= maxIndex) {
                currentIndex = 0;
            } else {
                currentIndex++;
            }
            showSlide(currentIndex);
        }, 3000); // Cambia cada 3 segundos
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
    }

    // Iniciar autoplay
    startAutoPlay();

    // Pausar autoplay al interactuar
    slider.addEventListener('mouseenter', stopAutoPlay);
    slider.addEventListener('mouseleave', startAutoPlay);


    // Inicializar
    updateButtons();
    slider.style.cursor = 'grab';

    console.log('[Slider] Inicializado correctamente');
});