
(function () {
  function collectFields(form) {
    var items = [];
    form.querySelectorAll('input, textarea, select').forEach(function (el) {
      if (!el.name || el.type === 'submit' || el.type === 'hidden') return;
      var label = '';
      var field = el.closest('.wpbb-field');
      if (field) {
        var labelEl = field.querySelector('.form-label');
        if (labelEl) label = labelEl.textContent.replace('*', '').trim();
      }
      items.push({ name: el.name, label: label || el.name, value: el.value });
    });
    return items;
  }

  function validate(form) {
    var required = form.querySelectorAll('[required]');
    for (var i = 0; i < required.length; i++) {
      if (!required[i].value) return false;
    }
    return true;
  }

  document.addEventListener('submit', function (e) {
    var form = e.target.closest('.wpbb-dynamic-form');
    if (!form) return;
    e.preventDefault();

    var message = form.querySelector('.wpbb-form-message');
    var submit = form.querySelector('[type="submit"]');
    var original = submit.textContent;

    if (!validate(form)) {
      message.textContent = form.dataset.validation || (window.wpbbForm && window.wpbbForm.validationText) || 'Please fill in all required fields correctly.';
      return;
    }

    submit.disabled = true;
    submit.textContent = 'Submitting...';
    message.textContent = '';

    var payload = new URLSearchParams();
    payload.append('action', 'wpbb_submit_form');
    payload.append('nonce', (window.wpbbForm && window.wpbbForm.nonce) || '');
    payload.append('fields', JSON.stringify(collectFields(form)));
    payload.append('settings', JSON.stringify({
      recipient: form.dataset.recipient || '',
      email_subject: form.dataset.subject || '',
      success_message: form.dataset.success || ''
    }));

    fetch((window.wpbbForm && window.wpbbForm.ajaxUrl) || '', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: payload.toString()
    })
    .then(function (res) { return res.json(); })
    .then(function (res) {
      if (res && res.success) {
        message.textContent = (res.data && res.data.message) || form.dataset.success || 'Success';
        form.reset();
      } else {
        message.textContent = (res && res.data && res.data.message) || (window.wpbbForm && window.wpbbForm.error) || 'Error';
      }
    })
    .catch(function () {
      message.textContent = (window.wpbbForm && window.wpbbForm.error) || 'Error';
    })
    .finally(function () {
      submit.disabled = false;
      submit.textContent = original;
    });
  });
})();
