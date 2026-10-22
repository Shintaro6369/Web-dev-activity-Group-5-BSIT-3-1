import './bootstrap';
/*!
 * UserHub – UI enhancements
 * Progressive enhancement only: forms, routes, field names, CSRF tokens and
 * HTTP methods are never modified, so login / register / dashboard behave
 * exactly the same with or without this file.
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  var THEME_KEY = 'userhub-theme';

  /* ---------- Theme (light / dark) ---------- */
  function readTheme() {
    try { return localStorage.getItem(THEME_KEY); } catch (e) { return null; }
  }
  function saveTheme(v) {
    try { localStorage.setItem(THEME_KEY, v); } catch (e) {}
  }
  function applyTheme(t) {
    root.setAttribute('data-theme', t);
    var b = doc.querySelector('.theme-toggle');
    if (b) {
      var next = t === 'dark' ? 'light' : 'dark';
      b.setAttribute('aria-label', 'Switch to ' + next + ' theme');
      b.setAttribute('title', 'Switch to ' + next + ' theme');
    }
  }
  var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyTheme(readTheme() || (prefersDark ? 'dark' : 'light'));

  function initThemeToggle() {
    var btn = doc.createElement('button');
    btn.type = 'button';
    btn.className = 'icon-btn theme-toggle';
    btn.innerHTML =
      '<svg class="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>' +
      '<svg class="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>';

    var navUser = doc.querySelector('.nav-user');
    if (navUser) {
      navUser.insertBefore(btn, navUser.firstChild);
    } else {
      btn.className += ' theme-float';
      doc.body.appendChild(btn);
    }
    btn.addEventListener('click', function () {
      var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      saveTheme(next);
    });
    applyTheme(root.getAttribute('data-theme'));
  }

  /* ---------- Show / hide password ---------- */
  function initPasswordToggles() {
    var inputs = doc.querySelectorAll('input[type="password"]');
    Array.prototype.forEach.call(inputs, function (input) {
      var wrap = doc.createElement('span');
      wrap.className = 'pw-wrap';
      input.parentNode.insertBefore(wrap, input);
      wrap.appendChild(input);

      var toggle = doc.createElement('button');
      toggle.type = 'button';
      toggle.className = 'pw-toggle';
      toggle.textContent = 'Show';
      toggle.setAttribute('aria-pressed', 'false');
      toggle.setAttribute('aria-label', 'Show password');
      wrap.appendChild(toggle);

      toggle.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        toggle.textContent = show ? 'Hide' : 'Show';
        toggle.setAttribute('aria-pressed', String(show));
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      });
    });
  }

  /* ---------- Loading state on submit (after the browser has started the submit) ---------- */
  function initSubmitLoading() {
    Array.prototype.forEach.call(doc.querySelectorAll('form'), function (form) {
      form.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        var btn = form.querySelector('button[type="submit"], button:not([type])');
        if (!btn) return;
        setTimeout(function () {
          btn.classList.add('is-loading');
          btn.disabled = true;
        }, 0);
      });
    });
    // Restore buttons if the user comes back with the browser Back button
    window.addEventListener('pageshow', function (e) {
      if (!e.persisted) return;
      Array.prototype.forEach.call(doc.querySelectorAll('.is-loading'), function (b) {
        b.classList.remove('is-loading');
        b.disabled = false;
      });
    });
  }

  /* ---------- Success messages fade out ---------- */
  function initAlerts() {
    Array.prototype.forEach.call(doc.querySelectorAll('.alert-success'), function (el) {
      setTimeout(function () {
        el.classList.add('is-leaving');
        setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 320);
      }, 6000);
    });
  }

  /* ---------- Nav shadow on scroll ---------- */
  function initNavShadow() {
    var nav = doc.querySelector('.nav');
    if (!nav) return;
    function update() { nav.classList.toggle('is-scrolled', window.scrollY > 4); }
    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  /* ---------- Dashboard: live search + unsaved-change highlight ---------- */
  function initDashboardTable() {
    var panel = doc.querySelector('.panel');
    var table = panel && panel.querySelector('table');
    if (!table) return;

    var rows = Array.prototype.filter.call(table.querySelectorAll('tbody tr'), function (r) {
      return !r.querySelector('td.empty');
    });

    // Unsaved-change highlight
    rows.forEach(function (row) {
      var fields = row.querySelectorAll('input[name="name"], input[name="email"]');
      var original = Array.prototype.map.call(fields, function (f) { return f.value; });
      function check() {
        var dirty = Array.prototype.some.call(fields, function (f, i) { return f.value !== original[i]; });
        row.classList.toggle('is-dirty', dirty);
      }
      Array.prototype.forEach.call(fields, function (f) { f.addEventListener('input', check); });
    });

    if (!rows.length) return;

    // Search bar
    var toolbar = doc.createElement('div');
    toolbar.className = 'toolbar';
    toolbar.innerHTML =
      '<input type="search" class="search" placeholder="Search by name, email or ID" aria-label="Search users" autocomplete="off">' +
      '<span class="result-note" aria-live="polite"></span>';
    panel.insertBefore(toolbar, panel.firstChild);

    var search = toolbar.querySelector('.search');
    var note = toolbar.querySelector('.result-note');
    var tbody = table.querySelector('tbody');
    var colCount = table.querySelectorAll('thead th').length || 4;

    var none = doc.createElement('tr');
    none.hidden = true;
    none.innerHTML = '<td colspan="' + colCount + '" class="empty"></td>';
    tbody.appendChild(none);

    function filter() {
      var q = search.value.trim().toLowerCase();
      var shown = 0;
      rows.forEach(function (row) {
        var text = (row.querySelector('td.id') ? row.querySelector('td.id').textContent : '') + ' ' +
          Array.prototype.map.call(row.querySelectorAll('input[name="name"], input[name="email"]'),
            function (i) { return i.value; }).join(' ');
        var match = !q || text.toLowerCase().indexOf(q) !== -1;
        row.hidden = !match;
        if (match) shown++;
      });
      none.hidden = shown !== 0;
      none.firstChild.textContent = 'No users match \u201C' + search.value.trim() + '\u201D.';
      note.textContent = q ? 'Showing ' + shown + ' of ' + rows.length : '';
    }

    search.addEventListener('input', filter);
    search.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { search.value = ''; filter(); }
    });
  }

  /* ---------- Init ---------- */
  function init() {
    initThemeToggle();
    initPasswordToggles();
    initSubmitLoading();
    initAlerts();
    initNavShadow();
    initDashboardTable();
  }

  if (doc.readyState === 'loading') {
    doc.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();