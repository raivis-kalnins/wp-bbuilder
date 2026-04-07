(function($){
  function initEditor(textarea, settings){
    if (!textarea || typeof wp === 'undefined' || !wp.codeEditor) return null;
    try { return wp.codeEditor.initialize(textarea, settings || {}); } catch (e) { return null; }
  }

  function getEditorValue(editor, $fallback){
    if (editor && editor.codemirror) return editor.codemirror.getValue();
    return $fallback.val() || '';
  }

  function setEditorValue(editor, $fallback, value){
    value = value || '';
    $fallback.val(value);
    if (editor && editor.codemirror) {
      editor.codemirror.setValue(value);
      editor.codemirror.save();
    }
  }

  $(function(){
    var scssEditors = new Map();
    var cssEditors = new Map();

    $('.wpbb-code-editor--scss').each(function(){
      scssEditors.set(this, initEditor(this, (window.wpbbBuilder && window.wpbbBuilder.scss) || {}));
    });

    $('.wpbb-code-editor--html').each(function(){
      initEditor(this, (window.wpbbBuilder && window.wpbbBuilder.html) || {});
    });

    $('.wpbb-code-editor--css-output').each(function(){
      var editor = initEditor(this, (window.wpbbBuilder && window.wpbbBuilder.css) || {});
      if (editor && editor.codemirror) editor.codemirror.setOption('readOnly', true);
      cssEditors.set(this, editor);
    });

    $(document).on('click', '.wpbb-build-scss', function(e){
      e.preventDefault();

      var $btn = $(this);
      var $card = $btn.closest('.wpbb-card, .postbox, form, body');
      var $status = $card.find('.wpbb-build-status').first();
      var $scssField = $card.find('.wpbb-code-editor--scss').first();
      var $cssField = $card.find('.wpbb-code-editor--css-output').first();

      if (!$scssField.length) {
        $status.text('SCSS field not found.');
        return;
      }

      var scssEditor = scssEditors.get($scssField.get(0));
      var cssEditor = $cssField.length ? cssEditors.get($cssField.get(0)) : null;
      var scss = getEditorValue(scssEditor, $scssField);

      if (scssEditor && scssEditor.codemirror) scssEditor.codemirror.save();
      $scssField.val(scss);

      $btn.prop('disabled', true);
      $status.text('Building...');

      $.ajax({
        url: (window.wpbbBuilder && window.wpbbBuilder.ajaxUrl) || ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'wpbb_compile_scss',
          nonce: window.wpbbBuilder ? window.wpbbBuilder.nonce : '',
          scss: scss
        }
      }).done(function(resp){
        if (resp && resp.success) {
          var css = resp.data && resp.data.css ? resp.data.css : '';
          if ($cssField.length) setEditorValue(cssEditor, $cssField, css);
          $status.text((window.wpbbBuilder && window.wpbbBuilder.compiledText) || 'SCSS compiled successfully.');
        } else {
          var msg = (resp && resp.data && resp.data.message) ? resp.data.message : ((window.wpbbBuilder && window.wpbbBuilder.errorText) || 'Build failed.');
          $status.text(msg);
        }
      }).fail(function(xhr){
        var msg = (window.wpbbBuilder && window.wpbbBuilder.errorText) || 'Build failed.';
        if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
          msg = xhr.responseJSON.data.message;
        }
        $status.text(msg);
      }).always(function(){
        $btn.prop('disabled', false);
      });
    });
  });
})(jQuery);
