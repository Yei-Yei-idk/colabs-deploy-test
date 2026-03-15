/**
 * Lógica para la página de Perfil del Cliente
 * Edición de datos y validaciones
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPerfil');
    if (!form) return;

    const editarBtn = document.querySelector('.editar-bloque');
    const guardarBtn = document.querySelector('.btn-guardar');
    const inputs = document.querySelectorAll('#nombre, #email, #telefono, #password, #newpassword, #confirmpassword');
    
    if (editarBtn && guardarBtn) {
        editarBtn.addEventListener('click', function() {
            if (editarBtn.textContent.includes('Editar')) {
                editarBtn.textContent = '✖ Cancelar';
                guardarBtn.style.display = 'block';
                
                inputs.forEach(input => {
                    input.disabled = false;
                    input.style.backgroundColor = '#ffffff';
                });
            } else {
                editarBtn.textContent = '✏ Editar';
                guardarBtn.style.display = 'none';
                
                inputs.forEach(input => {
                    input.disabled = true;
                    if (input.id !== 'password' && input.id !== 'newpassword' && input.id !== 'confirmpassword') {
                        input.value = input.defaultValue;
                    } else {
                        input.value = '';
                    }
                    input.style.backgroundColor = '';
                });
            }
        });
    }

    form.addEventListener('submit', function(e) {
        const np = document.getElementById('newpassword').value;
        const cp = document.getElementById('confirmpassword').value;

        if (np || cp) {
            if (np !== cp) {
                e.preventDefault();
                alert('Las contraseñas nuevas no coinciden.');
            }
        }
    });
});
