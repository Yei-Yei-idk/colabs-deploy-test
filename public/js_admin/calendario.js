document.addEventListener('DOMContentLoaded', () => {
    const reservadoCells = document.querySelectorAll('.tabla-reservas td.reservado');
    let tooltip = document.getElementById('reserva-tooltip');

    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.id = 'reserva-tooltip';
        tooltip.className = 'reserva-tooltip';
        document.body.appendChild(tooltip);
    }

    reservadoCells.forEach(cell => {
        cell.addEventListener('mouseenter', () => {
            const userName = cell.dataset.userName || 'N/A';
            const userEmail = cell.dataset.userEmail || 'N/A';
            const espacioNombre = cell.dataset.espacioNombre || 'N/A';
            const horaInicio = cell.dataset.horaInicio || 'N/A';
            const horaFin = cell.dataset.horaFin || 'N/A';
            const reservaId = cell.dataset.reservaId || 'N/A';

            tooltip.innerHTML = `
                <p><strong>Reserva ID:</strong> ${reservaId}</p>
                <p><strong>Espacio:</strong> ${espacioNombre}</p>
                <p><strong>Usuario:</strong> ${userName}</p>
                <p><strong>Email:</strong> ${userEmail}</p>
                
            `;

            const rect = cell.getBoundingClientRect();
            tooltip.style.left = `${rect.left + window.scrollX + rect.width / 2}px`;
            tooltip.style.top = `${rect.top + window.scrollY - tooltip.offsetHeight - 10}px`;

            if (parseFloat(tooltip.style.left) < 100) {
                tooltip.style.left = `${rect.left + window.scrollX + 100}px`;
            }

            tooltip.style.visibility = 'visible';
            tooltip.style.opacity = '1';
        });

        cell.addEventListener('mouseleave', () => {
            tooltip.style.visibility = 'hidden';
            tooltip.style.opacity = '0';
        });
    });
});