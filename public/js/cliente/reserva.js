/**
 * Lógica para el sistema de Reservas de COLABS
 * Maneja disponibilidad, precios, invitados y confirmación
 */

document.addEventListener('DOMContentLoaded', () => {
    // Configuración cargada desde el objeto global window.reservaConfig (definido en Blade)
    const config = window.reservaConfig;
    if (!config) {
        console.error('reservaConfig no encontrado');
        return;
    }

    const {
        precioHora,
        capacidadMaxima,
        espacioId,
        espacioNombre,
        csrfToken,
        verificarUrl,
        alternativasUrl,
        confirmarUrl,
        serverNowEpochMs,
        serverTimezone
    } = config;
    let currentGuests = 1;
    const baseServerNowEpochMs = Number.isFinite(serverNowEpochMs) ? serverNowEpochMs : Date.now();
    const baseClientEpochMs = Date.now();

    // Selectores del DOM
    const selectInicio = document.getElementById('hora_inicio');
    const selectFin    = document.getElementById('hora_fin');
    const fechaInput   = document.getElementById('fecha');
    const availabilityBox = document.getElementById('availabilityBox');
    const reserveBtn   = document.getElementById('reserveBtn');

    // TODAS las horas operativas del coworking
    const todasLasHoras = [
        {value:"06:00",label:"06:00 AM"},{value:"07:00",label:"07:00 AM"},
        {value:"08:00",label:"08:00 AM"},{value:"09:00",label:"09:00 AM"},
        {value:"10:00",label:"10:00 AM"},{value:"11:00",label:"11:00 AM"},
        {value:"12:00",label:"12:00 PM"},{value:"13:00",label:"01:00 PM"},
        {value:"14:00",label:"02:00 PM"},{value:"15:00",label:"03:00 PM"},
        {value:"16:00",label:"04:00 PM"},{value:"17:00",label:"05:00 PM"},
        {value:"18:00",label:"06:00 PM"},{value:"19:00",label:"07:00 PM"},
        {value:"20:00",label:"08:00 PM"},{value:"21:00",label:"09:00 PM"},
    ];

    function obtenerFechaHoraServidorBogota() {
        const elapsedMs = Date.now() - baseClientEpochMs;
        const nowMs = baseServerNowEpochMs + elapsedMs;
        const formatter = new Intl.DateTimeFormat('en-US', {
            timeZone: serverTimezone || 'America/Bogota',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            hour12: false
        });

        const parts = formatter.formatToParts(new Date(nowMs));
        const map = {};
        parts.forEach(({ type, value }) => {
            map[type] = value;
        });

        return {
            fecha: `${map.year}-${map.month}-${map.day}`,
            hora: parseInt(map.hour, 10)
        };
    }

    /**
     * Filtra las horas si el usuario elige el día de hoy
     */
    function getHorasDisponibles() {
        const fechaSeleccionada = fechaInput.value;
        const ahoraServidor = obtenerFechaHoraServidorBogota();
        const esHoy = fechaSeleccionada === ahoraServidor.fecha;

        if (!esHoy) return todasLasHoras;

        const horaActual = ahoraServidor.hora;
        return todasLasHoras.filter(h => {
            const horaNum = parseInt(h.value.split(':')[0]);
            return horaNum > horaActual;
        });
    }

    /**
     * Repuebla el selector de hora de inicio
     */
    function actualizarHorasInicio() {
        if (!selectInicio) return;
        const horasDisponibles = getHorasDisponibles();
        const valorAnterior = selectInicio.value;
        selectInicio.innerHTML = '<option value="">Seleccionar hora</option>';

        // Excluir 21:00 como hora de inicio, ya que la hora máxima de fin es 21:00
        const horasParaInicio = horasDisponibles.filter(h => h.value !== "21:00");
        horasParaInicio.forEach(hora => {
            const option = document.createElement('option');
            option.value = hora.value;
            option.textContent = hora.label;
            selectInicio.appendChild(option);
        });

        if (horasDisponibles.some(h => h.value === valorAnterior)) {
            selectInicio.value = valorAnterior;
        } else {
            selectInicio.value = '';
            if (selectFin) {
                selectFin.disabled = true;
                selectFin.innerHTML = '<option value="">Primero selecciona hora de inicio</option>';
            }
        }
    }

    /**
     * Visualización de secciones adicionales del formulario
     */
    function showBookingSections(show) {
        ['guestsSection','descriptionSection','pricingSummary'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            if (show) {
                el.style.display = '';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                el.style.transition = 'opacity 0.3s, transform 0.3s';
                requestAnimationFrame(() => { el.style.opacity='1'; el.style.transform='translateY(0)'; });
            } else {
                el.style.opacity = '0';
                setTimeout(() => { el.style.display = 'none'; }, 250);
            }
        });
    }

    /**
     * Verifica disponibilidad mediante fetch a Laravel
     */
    async function verificarBloqueHorario(fecha, hInicio, hFin) {
        try {
            const response = await fetch(verificarUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    espacio_id: espacioId,
                    fecha: fecha,
                    hora_inicio: hInicio,
                    hora_fin: hFin
                })
            });
            const data = await response.json();
            return {
                disponible: data.disponible,
                estado: data.estado || 'Libre'
            };
        } catch (error) {
            console.error('Error al verificar disponibilidad:', error);
            return false;
        }
    }

    function formatearHora(hora) {
        const [h, m] = hora.split(':');
        const num = parseInt(h);
        return `${num % 12 || 12}:${m} ${num >= 12 ? 'PM' : 'AM'}`;
    }

    /**
     * Lógica principal de verificación por bloques de 1 hora
     */
    async function verificarDisponibilidadPorHora() {
        if (!fechaInput || !selectInicio || !selectFin) return;
        const fecha = fechaInput.value;
        const horaInicioStr = selectInicio.value;
        const horaFinStr = selectFin.value;

        if (!fecha || !horaInicioStr || !horaFinStr) {
            availabilityBox.innerHTML = '<div id="availabilityContent"><p class="text-center text-muted">Selecciona fecha y hora para verificar disponibilidad</p></div>';
            setReserveBtn(false);
            showBookingSections(false);
            return;
        }

        const inicio = new Date('2000-01-01 ' + horaInicioStr);
        const fin    = new Date('2000-01-01 ' + horaFinStr);

        if (fin <= inicio) {
            availabilityBox.innerHTML = '<div id="availabilityContent" class="status-box status-error">⚠️ La hora de fin debe ser posterior a la hora de inicio</div>';
            setReserveBtn(false);
            showBookingSections(false);
            return;
        }

        availabilityBox.innerHTML = '<div id="availabilityContent" class="text-center"><p>Verificando disponibilidad...</p><div class="spin"></div></div>';
        
        const bloques = [];
        let cur = new Date(inicio);
        while (cur < fin) {
            const next = new Date(cur);
            next.setHours(cur.getHours() + 1);
            bloques.push({ 
                inicio: cur.toTimeString().substring(0, 5), 
                fin: (next <= fin ? next : fin).toTimeString().substring(0, 5) 
            });
            cur = next;
        }

        const resultados = await Promise.all(
            bloques.map(async (bloque) => {
                const res = await verificarBloqueHorario(fecha, bloque.inicio, bloque.fin);
                return {
                    ...bloque,
                    disponible: res.disponible,
                    estado: res.estado
                };
            })
        );

        mostrarResultados(resultados);
    }

    function mostrarResultados(resultados) {
        const todoOK = resultados.every(r => r.disponible);
        const conEspera = resultados.some(r => r.estado === 'Pendiente');
        const algunoOK = resultados.some(r => r.disponible);
        let html = '<div id="availabilityContent">';

        if (todoOK && !conEspera) {
            html += '<div class="status-box status-success">✅ <strong>¡Excelente!</strong> Todo el horario está disponible</div>';
        } else if (todoOK && conEspera) {
            html += '<div class="status-box status-warning" style="background:#fff3cd; color:#856404; border: 1px solid #ffeeba;">⚠️ <strong>Atención:</strong> Hay solicitudes anteriores en espera para algunos bloques. Tu reserva no es prioridad y podría ser rechazada.</div>';
        } else if (algunoOK) {
            html += '<div class="status-box status-info">⚠️ <strong>Atención:</strong> Algunos bloques no están disponibles</div>';
        } else {
            html += '<div class="status-box status-error">❌ <strong>Lo sentimos</strong> No hay disponibilidad en este horario</div>';
        }

        html += '<h4 class="mt-15 mb-10 text-muted font-14">Bloques de tiempo (1 hora):</h4>';
        html += '<div class="block-container">';

        resultados.forEach(r => {
            let statusClass = r.disponible ? 'aceptada' : 'rechazada';
            let statusText = r.disponible ? 'Disponible' : 'Ocupado';
            let icon = r.disponible ? '✅' : '❌';
            let bgColor = r.disponible ? '#d4edda' : '#f8d7da';

            if (r.estado === 'TuyaAceptada' || r.estado === 'TuyaPendiente') {
                statusClass = 'propia';
                statusText = (r.estado === 'TuyaPendiente') ? 'Tu Solicitud' : 'Tu Reserva';
                icon = '👤';
                bgColor = '#d1ecf1'; // Azul celeste para propiedad
            } else if (r.estado === 'Pendiente') {
                statusClass = 'pendiente';
                statusText = 'En Espera';
                icon = '⏳';
                bgColor = '#fff3cd'; // Amarillo
            }

            html += `<div class="block-item border-${statusClass}" style="background: ${bgColor};">
                        <span class="block-time">${formatearHora(r.inicio)} - ${formatearHora(r.fin)}</span>
                        <span class="block-status badge-${statusClass}">${icon} ${statusText}</span>
                     </div>`;
        });

        html += '</div></div>';

        if (!todoOK) {
            html += '<div id="alternativesContainer" class="mt-20"></div>';
        }

        availabilityBox.innerHTML = html;

        setReserveBtn(todoOK);
        showBookingSections(todoOK);
        if (todoOK) calcularPrecio();

        if (!todoOK) {
            cargarAlternativas(fechaInput.value, selectInicio.value, selectFin.value);
        }
    }

    async function cargarAlternativas(fecha, hInicio, hFin) {
        const altContainer = document.getElementById('alternativesContainer');
        const modalBody = document.getElementById('alternativasModalBody');
        if (!altContainer || !modalBody) return;

        altContainer.innerHTML = '';
        modalBody.innerHTML = '<div class="text-center" style="padding: 40px;"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted">Buscando alternativas disponibles...</p></div>';

        try {
            const response = await fetch(alternativasUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    espacio_id: espacioId,
                    fecha: fecha,
                    hora_inicio: hInicio,
                    hora_fin: hFin
                })
            });
            
            const data = await response.json();
            
            if (data.success && data.alternativas.length > 0) {
                // Alerta con botón para abrir el pop-up
                altContainer.innerHTML = `
                    <div class="alt-alert-box">
                        <div>
                            <strong class="alt-alert-title">🎯 ¡No pierdas tu día!</strong>
                            <p class="alt-alert-text">Tenemos <strong>${data.alternativas.length} alternativas perfectas</strong> libres a esa hora.</p>
                        </div>
                        <button type="button" class="btn-ver-opciones" onclick="openAlternativasModal()">
                            Ver Opciones
                        </button>
                    </div>
                `;

                // Construir tarjetas dentro del Pop-up usando las clases del cliente.css
                let html = '<p class="alt-subtitle">Estos locales del mismo tipo no tienen reservas cruzadas en el horario que solicitaste.</p>';
                html += '<div class="alt-grid">';
                
                data.alternativas.forEach(alt => {
                    html += `
                        <div class="card-alt-item">
                            <img src="${alt.imagen}" alt="${alt.nombre}" class="card-alt-img">
                            <div class="card-alt-content">
                                <h5 class="card-alt-title" title="${alt.nombre}">${alt.nombre.length > 30 ? alt.nombre.substring(0,30)+'...' : alt.nombre}</h5>
                                
                                <div class="card-alt-details">
                                    <span class="card-alt-price">
                                        $${alt.precio.toLocaleString('es-CO')} <span class="card-alt-price-unit">/ hora</span>
                                    </span>
                                    <span class="card-alt-capacity">
                                        👥 Capacidad: ${alt.capacidad} personas
                                    </span>
                                </div>

                                <a href="/cliente/reservar/${alt.id}?fecha=${fecha}&h_inicio=${hInicio}&h_fin=${hFin}" class="btn-reservar-alt">
                                    Reservar Este Local ➔
                                </a>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                modalBody.innerHTML = html;
            } else {
                altContainer.innerHTML = '';
                modalBody.innerHTML = '<div class="text-center p-5"><h5 class="text-muted mb-3">Lo sentimos</h5><p class="text-muted">No se encontraron alternativas similares libres para este horario.</p></div>';
            }
        } catch (error) {
            console.error('Error al cargar alternativas:', error);
            altContainer.innerHTML = '';
            modalBody.innerHTML = '<div class="text-center text-danger p-4"><p>Ocurrió un error de red al buscar alternativas.</p></div>';
        }
    }

    // Modal Trigger Functions for Alternativas
    window.openAlternativasModal = function() {
        const modal = document.getElementById('alternativasModal');
        if (modal) modal.classList.remove('modal-hidden');
    };

    window.closeAlternativasModal = function() {
        const modal = document.getElementById('alternativasModal');
        if (modal) modal.classList.add('modal-hidden');
    };

    function setReserveBtn(disponible) {
        reserveBtn.disabled = !disponible;
        reserveBtn.style.opacity = disponible ? '1' : '0.5';
        reserveBtn.style.cursor  = disponible ? 'pointer' : 'not-allowed';
        reserveBtn.textContent   = disponible ? 'Continuar con la reserva' : 'Verificar disponibilidad';
    }

    function calcularPrecio() {
        if (!selectInicio || !selectFin) return;
        const horaInicio = selectInicio.value;
        const horaFin = selectFin.value;
        if (!horaInicio || !horaFin) return;

        const inicio = new Date('2000-01-01 ' + horaInicio);
        const fin    = new Date('2000-01-01 ' + horaFin);
        if (fin <= inicio) return;

        const horas = Math.ceil((fin - inicio) / 3600000);
        const total = horas * precioHora;
        
        document.getElementById('pricingSummary').innerHTML = `
            <div class="pricing-line">$${precioHora.toLocaleString('es-CO')} x ${horas} hora(s)</div>
            <div class="total-line"><span class="total-label">Total</span><span class="total-amount">$${total.toLocaleString('es-CO')} COP</span></div>`;
    }

    /* ==== EVENTOS ==== */

    selectInicio.addEventListener('change', function() {
        if (!this.value) {
            selectFin.disabled = true;
            selectFin.innerHTML = '<option value="">Primero selecciona hora de inicio</option>';
        } else {
            selectFin.disabled = false;
            selectFin.innerHTML = '<option value="">Seleccionar hora</option>';
            const hNum = parseInt(this.value.split(':')[0]);
            getHorasDisponibles().filter(hr => parseInt(hr.value.split(':')[0]) > hNum).forEach(hr => {
                const opt = document.createElement('option');
                opt.value = hr.value; opt.textContent = hr.label;
                selectFin.appendChild(opt);
            });
            selectFin.value = '';
        }
        verificarDisponibilidadPorHora();
    });

    selectFin.addEventListener('change', verificarDisponibilidadPorHora);
    
    fechaInput.addEventListener('change', () => {
        actualizarHorasInicio();
        verificarDisponibilidadPorHora();
    });

    document.getElementById('bookingForm').addEventListener('submit', e => {
        e.preventDefault();
        if (!reserveBtn.disabled) openConfirmBookingPopup();
    });

    // Funciones globales vinculadas al alcance local
    window.changeGuests = (delta) => {
        currentGuests = Math.min(capacidadMaxima, Math.max(1, currentGuests + delta));
        document.getElementById('num_invitados').value = currentGuests;
        document.getElementById('guestCount').textContent = currentGuests + (currentGuests === 1 ? ' invitado' : ' invitados');
    };

    window.openConfirmBookingPopup = () => {
        document.getElementById('confirmEspacio').textContent = espacioNombre;
        const [y, m, d] = fechaInput.value.split('-');
        document.getElementById('confirmFecha').textContent = new Date(y, m - 1, d).toLocaleDateString('es-CO', { day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('confirmHorario').textContent = `${selectInicio.options[selectInicio.selectedIndex].text} - ${selectFin.options[selectFin.selectedIndex].text}`;
        
        const inicio = new Date('2000-01-01 ' + selectInicio.value);
        const fin    = new Date('2000-01-01 ' + selectFin.value);
        const duracion = Math.ceil((fin - inicio) / 3600000);
        
        document.getElementById('confirmDuracion').textContent = `${duracion} hora${duracion > 1 ? 's' : ''}`;
        document.getElementById('confirmCapacidad').textContent = `Hasta ${capacidadMaxima} personas`;
        const inv = document.getElementById('num_invitados').value;
        document.getElementById('confirmInvitados').textContent = `${inv} persona${inv > 1 ? 's' : ''}`;
        
        const total = duracion * precioHora;
        document.getElementById('confirmPrecio').innerHTML = `<span class="font-500">$${precioHora.toLocaleString('es-CO')} x ${duracion} hora${duracion > 1 ? 's' : ''}</span>`;
        document.getElementById('confirmTotal').textContent = `$${total.toLocaleString('es-CO')} COP`;
        
        document.getElementById('confirmBookingPopup').classList.remove('modal-hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeConfirmBookingPopup = () => {
        document.getElementById('confirmBookingPopup').classList.add('modal-hidden');
        document.body.style.overflow = 'auto';
    };

    window.confirmBooking = () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = confirmarUrl;

        const campos = {
            '_token': csrfToken,
            'espacio_id': espacioId,
            'fecha': fechaInput.value,
            'hora_inicio': selectInicio.value,
            'hora_fin': selectFin.value,
            'num_invitados': document.getElementById('num_invitados').value,
            'descripcion': document.querySelector('textarea[name="descripcion"]').value
        };

        Object.entries(campos).forEach(([k, v]) => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = k; inp.value = v;
            form.appendChild(inp);
        });

        document.body.appendChild(form);
        form.submit();
    };

    window.openReviewsModal = () => {
        document.getElementById('reviewsModal').classList.remove('modal-hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeReviewsModal = () => {
        document.getElementById('reviewsModal').classList.add('modal-hidden');
        document.body.style.overflow = 'auto';
    };

    document.getElementById('confirmBookingPopup').addEventListener('click', e => { if(e.target === e.currentTarget) closeConfirmBookingPopup(); });
    document.addEventListener('keydown', e => { if(e.key === 'Escape'){ closeReviewsModal(); closeConfirmBookingPopup(); } });

    // CSS para el spinner si no existe
    if (!document.getElementById('booking-spinner-style')) {
        const style = document.createElement('style');
        style.id = 'booking-spinner-style';
        style.textContent = `@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } .spin { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 10px auto; }`;
        document.head.appendChild(style);
    }

    // Inicializar
    actualizarHorasInicio();
    verificarDisponibilidadPorHora();
});
