document.addEventListener('click', function (e) {
  var btn = e.target.closest('.wpbb-copy-code-btn');
  if (!btn) return;
  var box = btn.closest('.wpbb-code-display');
  if (!box) return;
  var code = box.querySelector('code');
  if (!code) return;
  navigator.clipboard.writeText(code.textContent || '').then(function(){
    btn.textContent = '✓';
    setTimeout(function(){ btn.textContent = '⧉'; }, 1200);
  });
});