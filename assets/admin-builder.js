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


(function(){
  function initAdminScssCompiler(){
    var input = document.querySelector('[data-wpbb-admin-scss-input]');
    var hiddenScss = document.querySelector('[data-wpbb-admin-scss-hidden]');
    var hiddenCss = document.querySelector('[data-wpbb-admin-css-hidden]');
    var preview = document.querySelector('[data-wpbb-admin-css-preview]');
    var buildBtn = document.querySelector('[data-wpbb-admin-scss-build]');
    var status = document.querySelector('[data-wpbb-admin-scss-status]');
    if (!input || !buildBtn || !hiddenScss || !hiddenCss || !preview || !window.wpbbBuilder) return;

    function getEditorValue(textarea){
      try {
        if (textarea.nextSibling && textarea.nextSibling.CodeMirror) {
          return textarea.nextSibling.CodeMirror.getValue();
        }
        if (textarea.CodeMirror) {
          return textarea.CodeMirror.getValue();
        }
      } catch(e){}
      return textarea.value || '';
    }

    function setEditorValue(textarea, value){
      try {
        if (textarea.nextSibling && textarea.nextSibling.CodeMirror) {
          textarea.nextSibling.CodeMirror.setValue(value);
        } else if (textarea.CodeMirror) {
          textarea.CodeMirror.setValue(value);
        } else {
          textarea.value = value;
        }
      } catch(e) {
        textarea.value = value;
      }
    }

    buildBtn.addEventListener('click', function(){
      var scss = getEditorValue(input);
      hiddenScss.value = scss;
      status.textContent = 'Building...';

      var payload = new URLSearchParams();
      payload.append('action', 'wpbb_compile_scss');
      payload.append('nonce', window.wpbbBuilder.nonce || '');
      payload.append('scss', scss);

      fetch(window.wpbbBuilder.ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        credentials: 'same-origin',
        body: payload.toString()
      })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (res && res.success) {
          var css = (res.data && res.data.css) ? res.data.css : '';
          hiddenCss.value = css;
          preview.value = css;
          setEditorValue(preview, css);
          status.textContent = window.wpbbBuilder.compiledText || 'SCSS compiled successfully.';
        } else {
          status.textContent = (res && res.data && res.data.message) ? res.data.message : (window.wpbbBuilder.errorText || 'Build failed.');
        }
      })
      .catch(function(){
        status.textContent = window.wpbbBuilder.errorText || 'Build failed.';
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminScssCompiler);
  } else {
    initAdminScssCompiler();
  }
})();


(function($){
  $(function(){
    var $scss = $('[data-wpbb-admin-scss-input]');
    var $css = $('[data-wpbb-admin-css-preview]');
    if ($scss.length && !$scss.hasClass('wpbb-code-editor--scss')) $scss.addClass('wpbb-code-editor--scss');
    if ($css.length && !$css.hasClass('wpbb-code-editor--css-output')) $css.addClass('wpbb-code-editor--css-output');
  });
})(jQuery);
