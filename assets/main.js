// ─── Menú móvil ──────────────────────────────────────────────────────────
const hamburger = document.querySelector('.nav-hamburger');
const mobileMenu = document.querySelector('.mobile-menu');

function setMenuOpen(open) {
  mobileMenu.classList.toggle('open', open);
  hamburger.textContent = open ? '✕' : '☰';
}

hamburger.addEventListener('click', () => {
  setMenuOpen(!mobileMenu.classList.contains('open'));
});

// Cierra el menú y hace scroll suave al hacer click en cualquier enlace interno
document.querySelectorAll('a[href^="#"]').forEach((link) => {
  link.addEventListener('click', (e) => {
    const targetId = link.getAttribute('href').slice(1);
    const target = document.getElementById(targetId);
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth' });
      setMenuOpen(false);
    }
  });
});

// ─── Revelado de elementos al hacer scroll (equivalente a useReveal) ──────
const revealEls = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => entry.target.classList.add('visible'), i * 80);
        revealObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.12 }
);
revealEls.forEach((el) => revealObserver.observe(el));

// ─── Barras de progreso de habilidades (equivalente a useProgressBars) ────
const skillsGrid = document.querySelector('.skills-grid');
if (skillsGrid) {
  const progressObserver = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        document.querySelectorAll('.progress-bar').forEach((bar) => {
          bar.style.width = bar.dataset.pct + '%';
        });
        progressObserver.disconnect();
      }
    },
    { threshold: 0.2 }
  );
  progressObserver.observe(skillsGrid);
}

// ─── Efecto hover de los enlaces del footer ───────────────────────────────
document.querySelectorAll('.footer-link').forEach((link) => {
  link.addEventListener('mouseenter', () => (link.style.color = 'var(--accent)'));
  link.addEventListener('mouseleave', () => (link.style.color = 'var(--text-secondary)'));
});
