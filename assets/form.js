(function () {
  function qs(el, sel) { return el ? el.querySelector(sel) : null; }
  function qsa(el, sel) { return el ? Array.prototype.slice.call(el.querySelectorAll(sel)) : []; }

  function syncConditionals(form) {
    qsa(form, '[data-conditional-field]').forEach(function (wrap) {
      var fieldName = wrap.getAttribute('data-conditional-field');
      var expected = (wrap.getAttribute('data-conditional-value') || '').toLowerCase();
      var control = form.elements[fieldName] || form.querySelector('[name="' + fieldName + '"]');
      var value = '';
      if (control) {
        if (control instanceof RadioNodeList) {
          value = control.value || '';
        } else if (control.type === 'checkbox') {
          value = control.checked ? (control.value || '1') : '';
        } else {
          value = control.value || '';
        }
      }
      var visible = value.toLowerCase() === expected;
      wrap.hidden = !visible;
      qsa(wrap, 'input, textarea, select').forEach(function (input) {
        if (!visible) input.dataset.wasRequired = input.required ? '1' : '';
        input.required = visible && input.dataset.wasRequired === '1';
      });
    });
  }

  function initFileDrops(root) {
    qsa(root, '[data-file-drop]').forEach(function (drop) {
      var input = qs(drop, 'input[type="file"]');
      var meta = qs(drop, '.wpbb-file-drop__meta');
      if (!input) return;
      function renderMeta() {
        if (!meta) return;
        meta.textContent = input.files && input.files[0] ? input.files[0].name : '';
      }
      drop.addEventListener('dragover', function (e) { e.preventDefault(); drop.classList.add('is-drag'); });
      drop.addEventListener('dragleave', function () { drop.classList.remove('is-drag'); });
      drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.classList.remove('is-drag');
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
          input.files = e.dataTransfer.files;
          renderMeta();
        }
      });
      input.addEventListener('change', renderMeta);
    });
  }

  function initSignatures(root) {
    qsa(root, '[data-signature-wrap]').forEach(function (wrap) {
      var canvas = qs(wrap, 'canvas');
      var hidden = qs(wrap, 'input[type="hidden"]');
      var clear = qs(wrap, '[data-signature-clear]');
      if (!canvas || !hidden) return;
      var ctx = canvas.getContext('2d');
      var drawing = false;
      function resizeCanvas() {
        var ratio = window.devicePixelRatio || 1;
        var rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#0f172a';
      }
      resizeCanvas();
      function pos(e) {
        var rect = canvas.getBoundingClientRect();
        var p = e.touches ? e.touches[0] : e;
        return { x: p.clientX - rect.left, y: p.clientY - rect.top };
      }
      function start(e) { drawing = true; var p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); e.preventDefault(); }
      function move(e) { if (!drawing) return; var p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hidden.value = canvas.toDataURL('image/png'); e.preventDefault(); }
      function end() { drawing = false; }
      ['mousedown','touchstart'].forEach(function(ev){ canvas.addEventListener(ev,start,{passive:false}); });
      ['mousemove','touchmove'].forEach(function(ev){ canvas.addEventListener(ev,move,{passive:false}); });
      ['mouseup','mouseleave','touchend'].forEach(function(ev){ canvas.addEventListener(ev,end); });
      if (clear) clear.addEventListener('click', function(){ ctx.clearRect(0,0,canvas.width,canvas.height); hidden.value=''; });
      window.addEventListener('resize', function(){ var data = hidden.value; resizeCanvas(); if (data) { var img = new Image(); img.onload=function(){ ctx.drawImage(img,0,0,canvas.width/(window.devicePixelRatio||1),canvas.height/(window.devicePixelRatio||1)); }; img.src = data; } });
    });
  }

  function collectFields(form) {
    var items = [];
    qsa(form, 'input, textarea, select').forEach(function (el) {
      if (!el.name || el.type === 'submit' || el.type === 'button' || el.type === 'hidden' || el.closest('[hidden]')) return;
      var label = '';
      var field = el.closest('.wpbb-field');
      if (field) {
        var labelEl = field.querySelector('.form-label');
        if (labelEl) label = labelEl.textContent.replace('*', '').trim();
      }
      if (el.type === 'file') {
        items.push({ name: el.name, label: label || el.name, value: el.files && el.files[0] ? el.files[0].name : '', kind: 'file' });
      } else if (el.type === 'checkbox') {
        if (el.checked) items.push({ name: el.name, label: label || el.name, value: el.value || '1' });
      } else if (el.type === 'radio') {
        if (el.checked) items.push({ name: el.name, label: label || el.name, value: el.value });
      } else {
        items.push({ name: el.name, label: label || el.name, value: el.value });
      }
    });
    return items;
  }

  function validateStep(form, step) {
    var panel = form.querySelector('.wpbb-form-step-panel[data-step="' + step + '"]');
    if (!panel) return true;
    var ok = true;
    qsa(panel, '[required]').forEach(function (el) {
      if (el.closest('[hidden]')) return;
      var valid = true;
      if (el.type === 'file') valid = !!(el.files && el.files.length);
      else valid = !!el.value;
      if (el.type === 'email' && el.value) valid = /.+@.+\..+/.test(el.value);
      if (!valid) {
        ok = false;
        el.classList.add('is-invalid');
      } else {
        el.classList.remove('is-invalid');
      }
    });
    return ok;
  }

  function updateStepUI(form, step) {
    var total = parseInt((qs(form, '.wpbb-form-steps') || {}).dataset.total || '1', 10);
    qsa(form, '.wpbb-form-step-panel').forEach(function (panel) { panel.classList.toggle('is-active', parseInt(panel.dataset.step, 10) === step); });
    qsa(form, '.wpbb-form-step-pill').forEach(function (pill) { pill.classList.toggle('is-active', parseInt(pill.dataset.stepTarget, 10) === step); });
    var prev = qs(form, '.wpbb-step-prev');
    var next = qs(form, '.wpbb-step-next');
    var submit = qs(form, '.wpbb-submit-final');
    if (prev) prev.hidden = step <= 1;
    if (next) next.hidden = step >= total;
    if (submit) submit.hidden = step < total;
    form.dataset.currentStep = String(step);
  }

  document.addEventListener('DOMContentLoaded', function () {
    qsa(document, '.wpbb-dynamic-form').forEach(function (form) {
      initFileDrops(form);
      initSignatures(form);
      if (form.dataset.conditional === '1') {
        qsa(form, 'input, textarea, select').forEach(function (el) { el.addEventListener('change', function(){ syncConditionals(form); }); });
        syncConditionals(form);
      }
      if (form.dataset.steps === '1') {
        updateStepUI(form, 1);
        var prev = qs(form, '.wpbb-step-prev');
        var next = qs(form, '.wpbb-step-next');
        if (prev) prev.addEventListener('click', function(){ updateStepUI(form, Math.max(1, parseInt(form.dataset.currentStep || '1', 10) - 1)); });
        if (next) next.addEventListener('click', function(){
          var current = parseInt(form.dataset.currentStep || '1', 10);
          if (!validateStep(form, current)) {
            var msg = qs(form, '.wpbb-form-message');
            if (msg) msg.textContent = form.dataset.validation || 'Please fill in all required fields correctly.';
            return;
          }
          updateStepUI(form, current + 1);
        });
      }
    });
  });

  document.addEventListener('submit', function (e) {
    var form = e.target.closest('.wpbb-dynamic-form');
    if (!form) return;
    e.preventDefault();

    var message = form.querySelector('.wpbb-form-message');
    var submit = form.querySelector('[type="submit"]');
    var original = submit ? submit.textContent : 'Submit';
    var currentStep = parseInt(form.dataset.currentStep || '1', 10);

    if (!validateStep(form, currentStep)) {
      if (message) message.textContent = form.dataset.validation || (window.wpbbForm && window.wpbbForm.validationText) || 'Please fill in all required fields correctly.';
      return;
    }

    if (submit) {
      submit.disabled = true;
      submit.textContent = 'Submitting...';
    }
    if (message) message.textContent = '';

    var payload = new FormData();
    payload.append('action', 'wpbb_submit_form');
    payload.append('nonce', (window.wpbbForm && window.wpbbForm.nonce) || '');
    payload.append('fields', JSON.stringify(collectFields(form)));
    payload.append('settings', JSON.stringify({
      recipient: form.dataset.recipient || '',
      email_subject: form.dataset.subject || '',
      success_message: form.dataset.success || ''
    }));
    var honeypot = form.querySelector('input[name="website"]');
    var startedAt = form.querySelector('input[name="started_at"]');
    if (honeypot) payload.append('website', honeypot.value || '');
    if (startedAt) payload.append('started_at', startedAt.value || '');
    qsa(form, 'input[type="file"]').forEach(function (input) {
      if (input.files && input.files[0]) payload.append(input.name, input.files[0]);
    });

    fetch((window.wpbbForm && window.wpbbForm.ajaxUrl) || '', { method: 'POST', body: payload })
      .then(function (res) { return res.json(); })
      .then(function (res) {
        if (res && res.success) {
          if (message) message.textContent = (res.data && res.data.message) || form.dataset.success || 'Success';
          form.reset();
          if (form.dataset.steps === '1') updateStepUI(form, 1);
          qsa(form, '[data-signature-wrap] input[type="hidden"]').forEach(function (input) { input.value = ''; });
        } else {
          if (message) message.textContent = (res && res.data && res.data.message) || (window.wpbbForm && window.wpbbForm.error) || 'Error';
        }
      })
      .catch(function () {
        if (message) message.textContent = (window.wpbbForm && window.wpbbForm.error) || 'Error';
      })
      .finally(function () {
        if (submit) {
          submit.disabled = false;
          submit.textContent = original;
        }
      });
  });
})();
