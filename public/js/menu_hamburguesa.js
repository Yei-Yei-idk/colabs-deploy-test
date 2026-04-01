// Menú hamburguesa responsivo para Colabs

document.addEventListener('DOMContentLoaded', function() {
    const menu = document.querySelector('ul.menu');
    const nav = document.querySelector('header nav');
    const burgerBtn = document.createElement('button');
    burgerBtn.className = 'burger-btn';
    burgerBtn.setAttribute('aria-label', 'Abrir menú');
    burgerBtn.innerHTML = '<span></span><span></span><span></span>';

    // Insertar el botón antes del menú
    nav.insertBefore(burgerBtn, menu);

    burgerBtn.addEventListener('click', function() {
        menu.classList.toggle('open');
        burgerBtn.classList.toggle('open');
    });

    // Cerrar menú al hacer click en un enlace (solo móvil)
    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                menu.classList.remove('open');
                burgerBtn.classList.remove('open');
            }
        });
    });

    // Opcional: cerrar menú al cambiar tamaño ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) {
            menu.classList.remove('open');
            burgerBtn.classList.remove('open');
        }
    });
});
