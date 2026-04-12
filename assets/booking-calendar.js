document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.wpbb-booking').forEach(function (wrap) {
    var form = wrap.querySelector('.wpbb-booking__form');
    if (!form || !window.wpbbBooking) return;
    var booked = [];
    try { booked = JSON.parse(wrap.getAttribute('data-booked') || '[]'); } catch (e) {}
    var dateInput = form.querySelector('input[name="date"]');
    var message = wrap.querySelector('.wpbb-booking__message');
    if (dateInput) {
      dateInput.addEventListener('change', function () {
        if (booked.indexOf(this.value) !== -1) {
          this.setCustomValidity('Selected day is already booked.');
        } else {
          this.setCustomValidity('');
        }
      });
    }
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (dateInput && booked.indexOf(dateInput.value) !== -1) {
        message.textContent = 'Selected day is already booked.';
        message.className = 'wpbb-booking__message small text-danger';
        return;
      }
      var fd = new FormData(form);
      fd.append('action', 'wpbb_submit_booking');
      fd.append('nonce', wpbbBooking.nonce);
      fetch(wpbbBooking.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (json) {
          if (!json.success) throw new Error((json.data && json.data.message) || wpbbBooking.error);
          message.textContent = (json.data && json.data.message) || wrap.getAttribute('data-success') || wpbbBooking.success;
          message.className = 'wpbb-booking__message small text-success';
          if (dateInput && dateInput.value && booked.indexOf(dateInput.value) === -1) booked.push(dateInput.value);
          form.reset();
        })
        .catch(function (err) {
          message.textContent = err.message || wpbbBooking.error;
          message.className = 'wpbb-booking__message small text-danger';
        });
    });
  });
});
