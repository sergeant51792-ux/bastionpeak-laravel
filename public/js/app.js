/* app.js — BASTION PEAK internal banking system */

(function () {
  "use strict";

  /* =========================================================
     Theme
     ========================================================= */

  const THEME_KEY = "bp-theme";
  const BALANCE_KEY = "bp-balance-visible";
  const REDUCED_MOTION = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function getSystemTheme() {
    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
  }

  function getStoredTheme() {
    try {
      return localStorage.getItem(THEME_KEY);
    } catch {
      return null;
    }
  }

  function resolveTheme() {
    return getStoredTheme() || getSystemTheme();
  }

  function applyTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
  }

  function toggleTheme() {
    const next = resolveTheme() === "dark" ? "light" : "dark";
    try {
      localStorage.setItem(THEME_KEY, next);
    } catch {}
    applyTheme(next);
  }

  /* =========================================================
     Balance eye toggle
     ========================================================= */

  function getBalanceVisible() {
    try {
      return localStorage.getItem(BALANCE_KEY) !== "false";
    } catch {
      return true;
    }
  }

  function setBalanceVisible(value) {
    try {
      localStorage.setItem(BALANCE_KEY, value ? "true" : "false");
    } catch {}
  }

  function toggleBalance() {
    const next = !getBalanceVisible();
    setBalanceVisible(next);
    refreshBalances(next);
    return next;
  }

  function refreshBalances(visible) {
    document.querySelectorAll("[data-balance]").forEach(function (el) {
      el.textContent = visible ? el.getAttribute("data-balance") : "\u2022\u2022\u2022\u2022\u2022\u2022";
    });
  }

  /* =========================================================
     Bottom nav / rail switching
     ========================================================= */

  function setActiveNav(name) {
    document.querySelectorAll("[data-nav]").forEach(function (btn) {
      const isActive = btn.getAttribute("data-nav") === name;
      btn.setAttribute("aria-current", isActive ? "page" : "false");
      if (isActive) {
        btn.classList.add("is-active");
      } else {
        btn.classList.remove("is-active");
      }
    });

    const subtitle = document.getElementById("page-subtitle");
    if (subtitle) {
      const labels = {
        accounts: "Accounts",
        transfers: "Transfers",
        payments: "Payments",
        requests: "Requests",
        approvals: "Approvals",
      };
      subtitle.textContent = labels[name] || "Bastion Peak";
    }
  }

  function initNav() {
    document.querySelectorAll("[data-nav]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        setActiveNav(btn.getAttribute("data-nav"));
      });
    });
  }

  /* =========================================================
     Account carousel dots
     ========================================================= */

  function initCarousel() {
    var carousel = document.querySelector(".carousel");
    if (!carousel) return;

    var items = carousel.querySelectorAll(".carousel-item");
    var dotsRegion = carousel.nextElementSibling;
    if (!dotsRegion || !dotsRegion.classList.contains("carousel-dots")) return;

    var dots = Array.from(dotsRegion.querySelectorAll(".carousel-dot"));
    if (!dots.length) return;

    function update() {
      var index = Math.round(carousel.scrollLeft / (carousel.clientWidth - (carousel.querySelector(".carousel-item")?.offsetWidth || 1)));
      if (isNaN(index)) index = 0;
      dots.forEach(function (dot, i) {
        if (i === index) {
          dot.classList.add("is-active");
          dot.setAttribute("aria-current", "true");
        } else {
          dot.classList.remove("is-active");
          dot.removeAttribute("aria-current");
        }
      });
    }

    carousel.addEventListener("scroll", update, { passive: true });
    update();

    dots.forEach(function (dot, i) {
      dot.addEventListener("click", function () {
        carousel.scrollTo({ left: carousel.querySelector(".carousel-item")?.offsetLeft * i || 0, behavior: REDUCED_MOTION ? "auto" : "smooth" });
      });
    });
  }

  /* =========================================================
     Copy to clipboard
     ========================================================= */

  function initCopy() {
    document.querySelectorAll("[data-copy]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var text = btn.getAttribute("data-copy");
        if (!text) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function () {
            showCopied(btn);
          });
        } else {
          var ta = document.createElement("textarea");
          ta.value = text;
          ta.style.position = "fixed";
          ta.style.opacity = "0";
          document.body.appendChild(ta);
          ta.select();
          try {
            document.execCommand("copy");
            showCopied(btn);
          } catch {}
          document.body.removeChild(ta);
        }
      });
    });
  }

  function showCopied(btn) {
    var original = btn.innerHTML;
    btn.setAttribute("aria-label", "Copied");
    btn.innerHTML = '<svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg><span>Copied</span>';
    setTimeout(function () {
      btn.innerHTML = original;
      btn.removeAttribute("aria-label");
    }, 1200);
  }

  /* =========================================================
     Toast system
     ========================================================= */

  var toastRegion = document.getElementById("toast-region");

  function showToast(message, type) {
    type = type || "info";
    var toast = document.createElement("div");
    toast.className = "toast toast--" + type;
    toast.setAttribute("role", "status");

    var icons = {
      success: '<svg class="icon" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
      error: '<svg class="icon" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
      warning: '<svg class="icon" viewBox="0 0 24 24"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
      info: '<svg class="icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    };

    toast.innerHTML = (icons[type] || icons.info) + '<span>' + escapeHtml(message) + "</span>";
    toastRegion.appendChild(toast);

    var timer = setTimeout(function () {
      dismissToast(toast);
    }, 4000);

    toast.addEventListener("mouseenter", function () {
      clearTimeout(timer);
    });

    toast.addEventListener("mouseleave", function () {
      timer = setTimeout(function () {
        dismissToast(toast);
      }, 2000);
    });

    return toast;
  }

  function dismissToast(toast) {
    if (!toast || !toast.parentNode) return;
    toast.classList.add("is-hiding");
    setTimeout(function () {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 220);
  }

  /* =========================================================
     Sheet / Dialog / Drawer
     ========================================================= */

  function openSheet(sheetEl) {
    var overlay = document.getElementById("global-overlay");
    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden", "false");
    sheetEl.classList.add("is-open");
    sheetEl.setAttribute("aria-hidden", "false");
    trapFocus(sheetEl);
    document.addEventListener("keydown", onEscSheet);
  }

  function closeSheet(sheetEl) {
    var overlay = document.getElementById("global-overlay");
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden", "true");
    sheetEl.classList.remove("is-open");
    sheetEl.setAttribute("aria-hidden", "true");
    document.removeEventListener("keydown", onEscSheet);
    if (sheetEl._focusBack) sheetEl._focusBack.focus();
  }

  function onEscSheet(e) {
    if (e.key === "Escape") {
      var sheet = document.querySelector(".sheet.is-open");
      if (sheet) closeSheet(sheet);
    }
  }

  function openDialog(overlayEl) {
    overlayEl.classList.add("is-open");
    overlayEl.setAttribute("aria-hidden", "false");
    var dialog = overlayEl.querySelector(".dialog");
    if (dialog) {
      dialog._focusBack = document.activeElement;
      trapFocus(dialog);
    }
    document.addEventListener("keydown", onEscDialog);
  }

  function closeDialog(overlayEl) {
    overlayEl.classList.remove("is-open");
    overlayEl.setAttribute("aria-hidden", "true");
    var dialog = overlayEl.querySelector(".dialog");
    if (dialog && dialog._focusBack) dialog._focusBack.focus();
    document.removeEventListener("keydown", onEscDialog);
  }

  function onEscDialog(e) {
    if (e.key === "Escape") {
      var overlay = document.querySelector(".dialog-overlay.is-open");
      if (overlay) closeDialog(overlay);
    }
  }

  function openDrawer(drawerEl) {
    var overlay = document.getElementById("global-drawer-overlay");
    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden", "false");
    drawerEl.classList.add("is-open");
    drawerEl.setAttribute("aria-hidden", "false");
    trapFocus(drawerEl);
    document.addEventListener("keydown", onEscDrawer);
  }

  function closeDrawer(drawerEl) {
    var overlay = document.getElementById("global-drawer-overlay");
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden", "true");
    drawerEl.classList.remove("is-open");
    drawerEl.setAttribute("aria-hidden", "true");
    document.removeEventListener("keydown", onEscDrawer);
    if (drawerEl._focusBack) drawerEl._focusBack.focus();
  }

  function onEscDrawer(e) {
    if (e.key === "Escape") {
      var drawer = document.querySelector(".drawer.is-open");
      if (drawer) closeDrawer(drawer);
    }
  }

  function trapFocus(root) {
    var focusable = root.querySelectorAll(
      'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );
    if (!focusable.length) return;
    var first = focusable[0];
    var last = focusable[focusable.length - 1];
    root.addEventListener("keydown", function trap(e) {
      if (e.key !== "Tab") return;
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    });
  }

  function initOverlayClosers() {
    document.getElementById("global-overlay").addEventListener("click", function () {
      var sheet = document.querySelector(".sheet.is-open");
      if (sheet) closeSheet(sheet);
    });
    document.getElementById("global-dialog-overlay").addEventListener("click", function () {
      var overlay = document.querySelector(".dialog-overlay.is-open");
      if (overlay) closeDialog(overlay);
    });
    document.getElementById("global-drawer-overlay").addEventListener("click", function () {
      var drawer = document.querySelector(".drawer.is-open");
      if (drawer) closeDrawer(drawer);
    });
  }

  /* =========================================================
     Keyboard shortcuts — admin approvals
     ========================================================= */

  function initKeyboardShortcuts() {
    document.addEventListener("keydown", function (e) {
      // Ignore when typing in inputs
      var tag = document.activeElement.tagName.toLowerCase();
      var isInput = tag === "input" || tag === "textarea" || tag === "select" || document.activeElement.isContentEditable;
      if (isInput) return;

      var key = e.key.toLowerCase();
      switch (key) {
        case "j":
          approveCurrent();
          break;
        case "k":
          rejectCurrent();
          break;
        case "a":
          bulkApprove();
          break;
        case "r":
          bulkReject();
          break;
        case "escape":
          closeAnyOpen();
          break;
        default:
          break;
      }
    });
  }

  function closeAnyOpen() {
    var sheet = document.querySelector(".sheet.is-open");
    var dialog = document.querySelector(".dialog-overlay.is-open");
    var drawer = document.querySelector(".drawer.is-open");
    if (sheet) closeSheet(sheet);
    else if (dialog) closeDialog(dialog);
    else if (drawer) closeDrawer(drawer);
  }

  function approveCurrent() {
    var item = document.querySelector(".approval-item[data-selected]");
    if (!item) {
      showToast("No approval selected", "warning");
      return;
    }
    item.setAttribute("data-status", "approved");
    item.classList.add("flash", "flash--success");
    showToast("Approved", "success");
  }

  function rejectCurrent() {
    var item = document.querySelector(".approval-item[data-selected]");
    if (!item) {
      showToast("No approval selected", "warning");
      return;
    }
    item.setAttribute("data-status", "rejected");
    item.classList.add("flash");
    showToast("Rejected", "error");
  }

  function bulkApprove() {
    document.querySelectorAll(".approval-item[data-selected]").forEach(function (el) {
      el.setAttribute("data-status", "approved");
      el.classList.add("flash", "flash--success");
    });
    showToast("Selected approvals processed", "success");
  }

  function bulkReject() {
    document.querySelectorAll(".approval-item[data-selected]").forEach(function (el) {
      el.setAttribute("data-status", "rejected");
      el.classList.add("flash");
    });
    showToast("Selected approvals rejected", "error");
  }

  /* =========================================================
     Idempotency key
     ========================================================= */

  function generateIdempotencyKey() {
    var ts = Date.now().toString(36);
    var rand = Math.random().toString(36).slice(2, 8);
    return ts + "-" + rand;
  }

  function ensureIdempotencyField(form) {
    var existing = form.querySelector('input[name="idempotency_key"]');
    if (existing) existing.value = generateIdempotencyKey();
    var hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "idempotency_key";
    hidden.value = generateIdempotencyKey();
    form.appendChild(hidden);
  }

  /* =========================================================
     Form validation
     ========================================================= */

  function validateRequired(inputs) {
    var errors = [];
    inputs.forEach(function (input) {
      var value = (input.value || "").trim();
      var required = input.hasAttribute("required") || input.getAttribute("aria-required") === "true";
      if (required && !value) {
        errors.push({ input: input, message: input.getAttribute("data-error-missing") || "This field is required" });
        input.classList.add("is-invalid");
        input.setAttribute("aria-invalid", "true");
      } else {
        input.classList.remove("is-invalid");
        input.removeAttribute("aria-invalid");
      }
    });
    return errors;
  }

  function validateMinLength(inputs) {
    var errors = [];
    inputs.forEach(function (input) {
      var value = (input.value || "").trim();
      var min = parseInt(input.getAttribute("minlength"), 10);
      if (min && value.length < min) {
        errors.push({ input: input, message: input.getAttribute("data-error-minlength") || "Too short" });
        input.classList.add("is-invalid");
        input.setAttribute("aria-invalid", "true");
      }
    });
    return errors;
  }

  function validatePattern(inputs) {
    var errors = [];
    inputs.forEach(function (input) {
      var value = (input.value || "").trim();
      var pattern = input.getAttribute("pattern");
      if (pattern && value) {
        var regex = new RegExp("^" + pattern + "$");
        if (!regex.test(value)) {
          errors.push({ input: input, message: input.getAttribute("data-error-pattern") || "Invalid format" });
          input.classList.add("is-invalid");
          input.setAttribute("aria-invalid", "true");
        }
      }
    });
    return errors;
  }

  function validateForm(form) {
    var inputs = Array.from(form.querySelectorAll("input, select, textarea"));
    var errors = validateRequired(inputs).concat(validateMinLength(inputs)).concat(validatePattern(inputs));
    return errors.length ? errors : null;
  }

  function initForms() {
    document.querySelectorAll("form[data-validate]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        var errors = validateForm(form);
        if (errors) {
          e.preventDefault();
          errors.forEach(function (err) {
            showFieldError(err.input, err.message);
          });
          if (errors[0] && errors[0].input.focus) errors[0].input.focus();
          showToast("Please fix the errors above", "warning");
        } else {
          ensureIdempotencyField(form);
        }
      });
    });

    document.querySelectorAll("[data-validate-on-input]").forEach(function (input) {
      input.addEventListener("input", function () {
        if (input.classList.contains("is-invalid")) {
          validateForm(input.form);
        }
      });
    });
  }

  function showFieldError(input, message) {
    var field = input.closest(".field");
    if (!field) return;
    var errorEl = field.querySelector(".field-error");
    if (!errorEl) {
      errorEl = document.createElement("div");
      errorEl.className = "field-error";
      errorEl.setAttribute("role", "alert");
      field.appendChild(errorEl);
    }
    errorEl.textContent = message;
  }

  function clearFieldError(input) {
    var field = input.closest(".field");
    if (!field) return;
    var errorEl = field.querySelector(".field-error");
    if (errorEl) errorEl.textContent = "";
    input.classList.remove("is-invalid");
    input.removeAttribute("aria-invalid");
  }

  /* =========================================================
     Number / money formatting
     ========================================================= */

  function formatMoney(amount) {
    var n = parseFloat(amount, 10);
    if (isNaN(n)) return amount;
    var parts = n.toFixed(2).split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return "$" + parts.join(".");
  }

  function formatCurrency(amount, currency) {
    currency = currency || "USD";
    if (currency === "GBP") return "£" + formatMoney(amount).slice(1);
    if (currency === "EUR") return "€" + formatMoney(amount).slice(1);
    return formatMoney(amount);
  }

  function formatNumber(num) {
    return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  /* =========================================================
     Polling pause when tab hidden
     ========================================================= */

  var polls = [];
  var isHidden = false;

  function addPoll(fn, interval) {
    var timer = setInterval(fn, interval || 5000);
    polls.push(timer);
    return timer;
  }

  function pausePolls() {
    if (isHidden) return;
    isHidden = true;
    polls.forEach(clearInterval);
    polls = [];
  }

  function resumePolls() {
    if (!isHidden) return;
    isHidden = false;
    polls.forEach(function (t) { clearInterval(t); });
    polls = [];
  }

  function initVisibility() {
    document.addEventListener("visibilitychange", function () {
      if (document.hidden) {
        pausePolls();
      } else {
        resumePolls();
      }
    });
  }

  /* =========================================================
     Reduced motion check
     ========================================================= */

  function prefersReducedMotion() {
    return REDUCED_MOTION;
  }

  /* =========================================================
     Helpers
     ========================================================= */

  function escapeHtml(str) {
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  /* =========================================================
     Initialization
     ========================================================= */

  function init() {
    applyTheme(resolveTheme());
    initNav();
    initCarousel();
    initCopy();
    initOverlayClosers();
    initKeyboardShortcuts();
    initForms();
    initVisibility();

    var themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
      themeToggle.addEventListener("click", toggleTheme);
    }

    var balanceEye = document.getElementById("balance-eye");
    if (balanceEye) {
      balanceEye.addEventListener("click", function () {
        var visible = toggleBalance();
        balanceEye.querySelector("span").textContent = visible ? "Hide balance" : "Show balance";
      });
    }

    refreshBalances(getBalanceVisible());

    // Listen for system theme changes when no stored theme
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", function () {
      if (!getStoredTheme()) {
        applyTheme(getSystemTheme());
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  /* =========================================================
     Public API
     ========================================================= */

  window.BastionPeak = {
    theme: { toggle: toggleTheme, get: resolveTheme, apply: applyTheme },
    balance: { toggle: toggleBalance, get: getBalanceVisible, set: setBalanceVisible, refresh: refreshBalances },
    nav: { setActive: setActiveNav },
    carousel: { init: initCarousel },
    clipboard: { copy: initCopy },
    toast: { show: showToast, dismiss: dismissToast },
    sheet: { open: openSheet, close: closeSheet },
    dialog: { open: openDialog, close: closeDialog },
    drawer: { open: openDrawer, close: closeDrawer },
    form: { validate: validateForm, validateRequired: validateRequired, validateMinLength: validateMinLength, validatePattern: validatePattern, clearError: clearFieldError },
    money: { format: formatMoney, formatCurrency: formatCurrency, formatNumber: formatNumber },
    idempotency: { generateKey: generateIdempotencyKey, ensure: ensureIdempotencyField },
    poll: { add: addPoll, pause: pausePolls, resume: resumePolls },
    motion: { reduced: prefersReducedMotion },
  };
})();
