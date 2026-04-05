(function($){
  function initEditor(textarea, settings){
    if (!textarea || typeof wp === 'undefined' || !wp.codeEditor) return null;
    try { return wp.codeEditor.initialize(textarea, settings || {}); } catch (e) { return null; }
  }
  $(function(){
    var scssEditor = null, cssOutput = null;
    $('.wpbb-code-editor--scss').each(function(){ scssEditor = initEditor(this, (window.wpbbBuilder && window.wpbbBuilder.scss) || {}); });
    $('.wpbb-code-editor--html').each(function(){ initEditor(this, (window.wpbbBuilder && window.wpbbBuilder.html) || {}); });
    $('.wpbb-code-editor--css-output').each(function(){
      cssOutput = initEditor(this, (window.wpbbBuilder && window.wpbbBuilder.css) || {});
      if (cssOutput && cssOutput.codemirror) cssOutput.codemirror.setOption('readOnly', true);
    });
    $('.wpbb-build-scss').on('click', function(e){
      e.preventDefault();
      var $btn = $(this), $status = $('.wpbb-build-status');
      var scss = scssEditor && scssEditor.codemirror ? scssEditor.codemirror.getValue() : $('.wpbb-code-editor--scss').val();
      $btn.prop('disabled', true); $status.text('Building...');
      $.ajax({
        url: window.wpbbBuilder.ajaxUrl,
        method: 'POST',
        dataType: 'json',
        data: { action: 'wpbb_compile_scss', nonce: window.wpbbBuilder.nonce, scss: scss }
      }).done(function(resp){
        if (resp && resp.success) {
          if (cssOutput && cssOutput.codemirror) cssOutput.codemirror.setValue(resp.data.css || '');
          else $('.wpbb-code-editor--css-output').val(resp.data.css || '');
          $status.text(window.wpbbBuilder.compiledText);
        } else {
          $status.text((resp && resp.data && resp.data.message) ? resp.data.message : window.wpbbBuilder.errorText);
        }
      }).fail(function(xhr){
        $status.text(window.wpbbBuilder.errorText + ' ' + (xhr.status || ''));
      }).always(function(){ $btn.prop('disabled', false); });
    });
  });
})(jQuery);