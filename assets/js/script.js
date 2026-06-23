// ===== Año en footer =====
document.getElementById('anio').textContent = new Date().getFullYear();

// ===== Menú móvil simple =====
const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');
if (menuToggle) {
  menuToggle.addEventListener('click', () => {
    navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
    navLinks.style.flexDirection = 'column';
    navLinks.style.position = 'absolute';
    navLinks.style.top = '64px';
    navLinks.style.left = '0';
    navLinks.style.right = '0';
    navLinks.style.background = '#F8F6F0';
    navLinks.style.padding = '20px 24px';
    navLinks.style.gap = '14px';
  });
}

// ===== Slider de servicios =====
const track = document.getElementById('sliderTrack');
const prevBtn = document.getElementById('prevSlide');
const nextBtn = document.getElementById('nextSlide');
if (track) {
  const scrollAmount = () => track.querySelector('.slide-card').offsetWidth + 24;
  nextBtn.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));
  prevBtn.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
}

// =========================================================
// Sistema de citas: consulta disponibilidad y envía solicitud
// Ajusta API_BASE si subes el sitio a una subcarpeta.
// =========================================================
const API_BASE = 'backend/api';

const fechaInput = document.getElementById('fecha');
const slotsGrid = document.getElementById('slotsGrid');
const horaInput = document.getElementById('hora');
const legendHours = document.getElementById('legendHours');
const bookingForm = document.getElementById('bookingForm');
const formMsg = document.getElementById('formMsg');

let HORARIOS = ['10:00','11:00','12:00','13:00','14:00','15:00', '16:00', '17:00', '18:00', '19:00', '20:00', ];
let OCUPADAS = []; // [{fecha, hora, estado}]

// Fecha mínima = hoy
if (fechaInput) {
  const hoy = new Date();
  fechaInput.min = hoy.toISOString().split('T')[0];
}

async function cargarDisponibilidad() {
  try {
    const res = await fetch(`${API_BASE}/disponibilidad.php`);
    const data = await res.json();
    if (data.ok) {
      HORARIOS = data.horas_disponibles;
      OCUPADAS = data.ocupadas;
    }
  } catch (e) {
    console.warn('No se pudo conectar con el sistema de citas todavía. Revisa backend/config.php.', e);
  }
  pintarLeyendaHoras();
  pintarSlots();
}

function pintarLeyendaHoras() {
  if (!legendHours) return;
  legendHours.innerHTML = HORARIOS.map(h => `<span>${h} hrs</span>`).join('');
}

function pintarSlots() {
  if (!slotsGrid) return;
  const fecha = fechaInput.value;
  slotsGrid.innerHTML = '';
  horaInput.value = '';

  HORARIOS.forEach(hora => {
    const ocupado = OCUPADAS.some(o => o.fecha === fecha && o.hora === hora);
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'slot-btn' + (ocupado ? ' ocupado' : '');
    btn.textContent = hora;
    btn.disabled = ocupado || !fecha;
    btn.addEventListener('click', () => {
      document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      horaInput.value = hora;
    });
    slotsGrid.appendChild(btn);
  });
}

if (fechaInput) {
  fechaInput.addEventListener('change', pintarSlots);
}

if (bookingForm) {
  bookingForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    formMsg.className = 'form-msg';
    formMsg.textContent = '';

    if (!horaInput.value) {
      formMsg.classList.add('error');
      formMsg.textContent = 'Por favor selecciona un horario disponible.';
      return;
    }

    const fd = new FormData(bookingForm);
    fd.set('acepto_aviso', document.getElementById('acepto').checked ? '1' : '0');

    const submitBtn = bookingForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';

    try {
      const res = await fetch(`${API_BASE}/solicitar_cita.php`, { method: 'POST', body: fd });
      const data = await res.json();
      if (data.ok) {
        formMsg.classList.add('ok');
        formMsg.textContent = data.mensaje;
        bookingForm.reset();
        await cargarDisponibilidad();
      } else {
        formMsg.classList.add('error');
        formMsg.textContent = data.error || 'Ocurrió un error, intenta de nuevo.';
      }
    } catch (err) {
      formMsg.classList.add('error');
      formMsg.textContent = 'No se pudo conectar con el servidor. Intenta más tarde.';
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Enviar solicitud de cita';
    }
  });
}

cargarDisponibilidad();
