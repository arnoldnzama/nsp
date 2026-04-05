// ============================================
// New Strong Protection — Main Script
// ============================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- HEADER SCROLL ----
  const header = document.getElementById('header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.classList.toggle('scrolled', window.scrollY > 80);
    });
  }

  // ---- HAMBURGER MENU ----
  const hamburger = document.getElementById('hamburger');
  const navMenu = document.getElementById('nav-menu');
  if (hamburger && navMenu) {
    hamburger.addEventListener('click', function () {
      const expanded = hamburger.getAttribute('aria-expanded') === 'true';
      hamburger.classList.toggle('active');
      navMenu.classList.toggle('active');
      document.body.style.overflow = expanded ? '' : 'hidden';
      hamburger.setAttribute('aria-expanded', String(!expanded));
    });
    navMenu.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = '';
        hamburger.setAttribute('aria-expanded', 'false');
      });
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && navMenu.classList.contains('active')) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = '';
        hamburger.setAttribute('aria-expanded', 'false');
      }
    });
    document.addEventListener('click', e => {
      if (navMenu.classList.contains('active') && !navMenu.contains(e.target) && !hamburger.contains(e.target)) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = '';
        hamburger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ---- SCROLL ANIMATIONS ----
  const animatedEls = document.querySelectorAll('.animate-on-scroll');
  if (animatedEls.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
    animatedEls.forEach(el => observer.observe(el));
  }

  // ---- HERO SWIPER ----
  if (document.querySelector('.hero.swiper')) {
    new Swiper('.hero.swiper', {
      loop: true,
      speed: 1000,
      effect: 'fade',
      fadeEffect: { crossFade: true },
      autoplay: { delay: 6000, disableOnInteraction: false },
      pagination: { el: '.swiper-pagination', clickable: true },
    });
  }

  // ---- FAQ ACCORDION ----
  document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    if (!question) return;
    question.addEventListener('click', () => {
      const isActive = item.classList.contains('active');
      document.querySelectorAll('.faq-item.active').forEach(i => i.classList.remove('active'));
      if (!isActive) item.classList.add('active');
    });
  });

  // ---- SMOOTH SCROLL ----
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = (header ? header.offsetHeight : 0) + 20;
        window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
      }
    });
  });

  // ---- CONTACT FORM ----
  const devisForm = document.querySelector('.devis-form');
  if (devisForm) {
    devisForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const required = devisForm.querySelectorAll('[required]');
      let valid = true;
      required.forEach(f => {
        if (!f.value.trim()) { valid = false; f.style.borderColor = '#e74c3c'; }
        else f.style.borderColor = '';
      });
      if (!valid) { showModal('error', 'Veuillez remplir tous les champs obligatoires.'); return; }

      const formData = {
        prenom: devisForm.querySelector('#prenom') ? devisForm.querySelector('#prenom').value : '',
        nom: devisForm.querySelector('#nom') ? devisForm.querySelector('#nom').value : '',
        entreprise: devisForm.querySelector('#entreprise') ? devisForm.querySelector('#entreprise').value : '',
        email: devisForm.querySelector('#email') ? devisForm.querySelector('#email').value : '',
        telephone: devisForm.querySelector('#telephone') ? devisForm.querySelector('#telephone').value : '',
        service: devisForm.querySelector('#service') ? devisForm.querySelector('#service').value : '',
        message: devisForm.querySelector('#message') ? devisForm.querySelector('#message').value : ''
      };

      const btn = devisForm.querySelector('button[type="submit"]');
      const orig = btn.textContent;
      btn.textContent = 'Envoi en cours...';
      btn.disabled = true;

      fetch('send_email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        btn.textContent = orig; btn.disabled = false;
        if (data.success) {
          showModal('success', 'Merci ' + formData.prenom + ' ! Votre demande a ete envoyee. Nous vous repondrons sous 24h.');
          devisForm.reset();
        } else {
          showModal('error', data.message || 'Une erreur est survenue. Veuillez reessayer.');
        }
      })
      .catch(function() {
        btn.textContent = orig; btn.disabled = false;
        showModal('error', 'Erreur de connexion. Verifiez votre connexion internet.');
      });
    });
  }

  // ---- EMAIL VALIDATION ----
  document.querySelectorAll('input[type="email"]').forEach(function(input) {
    input.addEventListener('blur', function () {
      var ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
      this.style.borderColor = this.value && !ok ? '#e74c3c' : '';
    });
  });

  // ---- PHONE INPUT ----
  document.querySelectorAll('input[type="tel"]').forEach(function(input) {
    input.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9\s\-\(\)\+]/g, '');
    });
  });

  // ---- COOKIE CONSENT ----
  if (!localStorage.getItem('cookieConsent')) {
    setTimeout(function() {
      var bar = document.createElement('div');
      bar.className = 'cookie-consent';
      bar.innerHTML = '<div class="cookie-content"><p>Ce site utilise des cookies pour ameliorer votre experience.</p><div class="cookie-buttons"><button class="btn btn-gold" onclick="acceptCookies()">Accepter</button><button class="btn btn-outline" onclick="declineCookies()">Refuser</button></div></div>';
      document.body.appendChild(bar);
    }, 2500);
  }

});

// ---- MODAL ----
function showModal(type, message) {
  var iconSvg = type === 'success'
    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="35" height="35"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="35" height="35"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
  var title = type === 'success' ? 'Demande envoyee !' : 'Erreur';

  var modal = document.createElement('div');
  modal.className = 'confirmation-modal';
  modal.innerHTML = '<div class="modal-overlay"></div><div class="modal-content"><div class="modal-icon ' + type + '">' + iconSvg + '</div><h2>' + title + '</h2><p class="modal-message">' + message + '</p><button class="btn-modal-close" onclick="closeModal()">Fermer</button></div>';
  document.body.appendChild(modal);
  setTimeout(function() { modal.classList.add('show'); }, 10);
  modal.querySelector('.modal-overlay').addEventListener('click', closeModal);
  document.addEventListener('keydown', function escHandler(e) {
    if (e.key === 'Escape') { closeModal(); document.removeEventListener('keydown', escHandler); }
  });
}

function closeModal() {
  var modal = document.querySelector('.confirmation-modal');
  if (modal) { modal.classList.remove('show'); setTimeout(function() { modal.remove(); }, 300); }
}

function acceptCookies() {
  localStorage.setItem('cookieConsent', 'accepted');
  var bar = document.querySelector('.cookie-consent');
  if (bar) bar.remove();
}

function declineCookies() {
  localStorage.setItem('cookieConsent', 'declined');
  var bar = document.querySelector('.cookie-consent');
  if (bar) bar.remove();
}
