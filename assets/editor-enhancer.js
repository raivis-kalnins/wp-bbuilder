(function(){
  if (typeof wp === 'undefined' || !wp.domReady) return;
  function initEditors(root){
    if (!root || !wp.codeEditor || !wp.codeEditor.initialize) return;
    var areas = root.querySelectorAll('.wpbb-code-editor textarea');
    Array.prototype.forEach.call(areas, function(textarea){
      if (textarea.dataset.wpbbEditorReady === '1') return;
      textarea.dataset.wpbbEditorReady = '1';
      try {
        var instance = wp.codeEditor.initialize(textarea, (window.wpbbEditorEnhancer && window.wpbbEditorEnhancer.scss) || { codemirror: { mode: 'text/x-scss', lineNumbers: true, lineWrapping: true } });
        if (instance && instance.codemirror) {
          textarea.wpbbCodeMirror = instance.codemirror;
          if (textarea.readOnly) instance.codemirror.setOption('readOnly', true);
          instance.codemirror.on('change', function(cm){
            try {
              textarea.value = cm.getValue();
              textarea.dispatchEvent(new Event('input', { bubbles: true }));
              textarea.dispatchEvent(new Event('change', { bubbles: true }));
            } catch (err) {}
          });
        }
      } catch (e) {}
    });
  }
  wp.domReady(function(){
    initEditors(document);
    if (typeof MutationObserver !== 'undefined') {
      var observer = new MutationObserver(function(mutations){
        mutations.forEach(function(mutation){
          Array.prototype.forEach.call(mutation.addedNodes || [], function(node){
            if (node && node.nodeType === 1) initEditors(node);
          });
        });
      });
      observer.observe(document.body, { childList: true, subtree: true });
    }
  });
})();
