/**
 * Sennen Frontend JavaScript
 * Handles nav scroll state, mobile menu, booking embed, and contact form.
 */
(function () {
  'use strict';

  // --- Nav scroll state ---
  function initNav() {
    var nav = document.getElementById('sennen-nav');
    if (!nav) return;

    function onScroll() {
      if (window.scrollY > 20) {
        nav.classList.remove('sennen-nav--transparent');
        nav.classList.add('sennen-nav--scrolled');
      } else {
        nav.classList.remove('sennen-nav--scrolled');
        nav.classList.add('sennen-nav--transparent');
      }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // --- Mobile menu toggle ---
  function initMobileMenu() {
    var toggle = document.getElementById('sennen-mobile-toggle');
    var menu = document.getElementById('sennen-mobile-menu');
    var iconMenu = document.getElementById('sennen-icon-menu');
    var iconClose = document.getElementById('sennen-icon-close');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', function () {
      var isOpen = menu.classList.contains('sennen-mobile-menu--open');
      if (isOpen) {
        menu.classList.remove('sennen-mobile-menu--open');
        if (iconMenu) iconMenu.style.display = '';
        if (iconClose) iconClose.style.display = 'none';
        toggle.setAttribute('aria-expanded', 'false');
      } else {
        menu.classList.add('sennen-mobile-menu--open');
        if (iconMenu) iconMenu.style.display = 'none';
        if (iconClose) iconClose.style.display = '';
        toggle.setAttribute('aria-expanded', 'true');
      }
    });

    // Close menu on link click
    var links = menu.querySelectorAll('a');
    links.forEach(function (link) {
      link.addEventListener('click', function () {
        menu.classList.remove('sennen-mobile-menu--open');
        if (iconMenu) iconMenu.style.display = '';
        if (iconClose) iconClose.style.display = 'none';
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // --- Contact form ---
  function initContactForm() {
    var forms = document.querySelectorAll('.sennen-contact-form');
    forms.forEach(function (form) {
      var statusEl = form.querySelector('.sennen-form-status');
      var btn = form.querySelector('button[type="submit"]');

      form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear previous errors
        form.querySelectorAll('.sennen-field-error').forEach(function (el) {
          el.textContent = '';
        });
        form.querySelectorAll('input, textarea').forEach(function (el) {
          el.classList.remove('sennen-field-error');
        });

        // Validate
        var errors = {};
        var name = form.querySelector('[name="name"]');
        var email = form.querySelector('[name="email"]');
        var message = form.querySelector('[name="message"]');

        if (name && (!name.value.trim() || name.value.trim().length < 2)) {
          errors.name = 'Please enter your name';
          if (name) name.classList.add('sennen-field-error');
        }
        if (email && !email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
          errors.email = 'Please enter a valid email';
          if (email) email.classList.add('sennen-field-error');
        }
        if (message && (!message.value.trim() || message.value.trim().length < 10)) {
          errors.message = 'Please enter a message (at least 10 characters)';
          if (message) message.classList.add('sennen-field-error');
        }

        // Show field errors
        Object.keys(errors).forEach(function (key) {
          var errEl = form.querySelector('.sennen-error-' + key);
          if (errEl) errEl.textContent = errors[key];
        });

        if (Object.keys(errors).length > 0) return;

        // Submit
        if (btn) {
          btn.disabled = true;
          btn.querySelector('span').textContent = 'Sending...';
        }

        var formData = new FormData(form);
        var data = {
          name: formData.get('name'),
          email: formData.get('email'),
          inquiryType: formData.get('inquiryType'),
          message: formData.get('message'),
          _wpnonce: formData.get('_wpnonce'),
        };

        fetch('/wp-json/sennen/v1/contact', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data.success) {
              if (statusEl) {
                statusEl.className = 'sennen-form-status p-4 rounded-xl text-body-md';
                statusEl.style.backgroundColor = 'var(--color-primary-container)';
                statusEl.style.color = 'var(--color-on-primary-container)';
                statusEl.textContent = 'Your message has been sent. I look forward to connecting with you soon.';
              }
              form.reset();
            } else {
              if (statusEl) {
                statusEl.className = 'sennen-form-status p-4 rounded-xl text-body-md';
                statusEl.style.backgroundColor = 'var(--color-error-container)';
                statusEl.style.color = 'var(--color-on-error-container)';
                statusEl.textContent = data.message || 'Something went wrong. Please try again.';
              }
            }
          })
          .catch(function () {
            if (statusEl) {
              statusEl.className = 'sennen-form-status p-4 rounded-xl text-body-md';
              statusEl.style.backgroundColor = 'var(--color-error-container)';
              statusEl.style.color = 'var(--color-on-error-container)';
              statusEl.textContent = 'Something went wrong. Please try again.';
            }
          })
          .finally(function () {
            if (btn) {
              btn.disabled = false;
              btn.querySelector('span').textContent = 'Send Message';
            }
          });
      });
    });
  }

  // --- Booking embed Calendly loader ---
  function initBookingEmbed() {
    var containers = document.querySelectorAll('.sennen-booking-calendly');
    containers.forEach(function (container) {
      var url = container.getAttribute('data-url');
      if (!url) return;

      var script = document.createElement('script');
      script.src = 'https://assets.calendly.com/assets/external/widget.js';
      script.async = true;
      document.body.appendChild(script);
    });
  }

  // --- Init on DOMContentLoaded ---
  document.addEventListener('DOMContentLoaded', function () {
    initNav();
    initMobileMenu();
    initContactForm();
    initBookingEmbed();
  });
})();
