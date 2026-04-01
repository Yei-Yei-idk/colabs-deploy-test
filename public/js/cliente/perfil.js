/**
 * Lógica para la página de Perfil del Cliente
 * Validación al guardar cambios
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPerfil');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        const newpass = document.getElementById('newpassword').value;
        const confirmpass = document.getElementById('confirmpassword');
        
        // Validar que las contraseñas coincidan si se intenta cambiar
        if (newpass && confirmpass && confirmpass.value !== newpass) {
            e.preventDefault();
            alert('Las contraseñas nuevas no coinciden.');
        }
    });
});
