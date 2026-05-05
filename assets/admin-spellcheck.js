(function () {
  if (typeof window.wpbbSpellcheck === 'undefined' || !window.wpbbSpellcheck.enabled) {
    return;
  }

  var config = window.wpbbSpellcheck;
  var lang = config.lang || 'en';
  var selectors = Array.isArray(config.selectors) ? config.selectors : [];
  var iframeSelectors = Array.isArray(config.editorIframeSelectors) ? config.editorIframeSelectors : [];
  var seenFrames = new WeakSet();
  var seenDocs = new WeakSet();

  function canSpellcheckInput(el) {
    if (!el || !el.type) return true;
    return ['email', 'password', 'number', 'date', 'datetime-local', 'time', 'tel', 'file', 'hidden', 'color', 'range', 'checkbox', 'radio'].indexOf(el.type) === -1;
  }

  function setFlags(el) {
    if (!el || el.nodeType !== 1) return;
    try { el.setAttribute('lang', lang); } catch (e) {}
    try { el.setAttribute('spellcheck', 'true'); } catch (e) {}
    try { el.spellcheck = true; } catch (e) {}
  }

  function markElement(el) {
    if (!el || el.nodeType !== 1) return;

    var tag = (el.tagName || '').toLowerCase();
    if (tag === 'iframe') {
      bindFrame(el);
      return;
    }

    if (tag === 'input' || tag === 'textarea') {
      if (!canSpellcheckInput(el)) return;
      setFlags(el);
      return;
    }

    if (el.isContentEditable || el.getAttribute('contenteditable') === 'true' || el.classList.contains('block-editor-rich-text__editable') || el.classList.contains('mce-content-body')) {
      setFlags(el);
    }
  }

  function applyToDocument(doc, root) {
    if (!doc) return;

    try {
      if (doc.documentElement) doc.documentElement.setAttribute('lang', lang);
      if (doc.body) {
        doc.body.setAttribute('lang', lang);
        doc.body.setAttribute('spellcheck', 'true');
        try { doc.body.spellcheck = true; } catch (e) {}
      }
    } catch (e) {}

    var scope = root && root.querySelectorAll ? root : doc;

    selectors.forEach(function (selector) {
      try {
        scope.querySelectorAll(selector).forEach(markElement);
      } catch (e) {}
    });

    if (root && root.nodeType === 1) {
      markElement(root);
    }

    iframeSelectors.forEach(function (selector) {
      try {
        doc.querySelectorAll(selector).forEach(bindFrame);
      } catch (e) {}
    });
  }

  function bindFrame(frame) {
    if (!frame || seenFrames.has(frame)) return;
    seenFrames.add(frame);

    function applyFrame() {
      try {
        var frameDoc = frame.contentDocument || (frame.contentWindow && frame.contentWindow.document);
        if (!frameDoc) return;
        observeDocument(frameDoc);
        applyToDocument(frameDoc, frameDoc);
      } catch (e) {}
    }

    frame.addEventListener('load', applyFrame);
    applyFrame();
  }

  function observeDocument(doc) {
    if (!doc || seenDocs.has(doc)) return;
    seenDocs.add(doc);

    var target = doc.body || doc.documentElement;
    if (!target || typeof MutationObserver === 'undefined') return;

    try {
      new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          mutation.addedNodes.forEach(function (node) {
            if (node && node.nodeType === 1) {
              applyToDocument(doc, node);
            }
          });
        });
      }).observe(target, { childList: true, subtree: true });
    } catch (e) {}
  }

  function bindAcfHooks() {
    if (!window.acf || typeof window.acf.addAction !== 'function') {
      return;
    }

    ['ready', 'append', 'show', 'new_field'].forEach(function (hook) {
      window.acf.addAction(hook, function ($el) {
        var node = $el && $el[0] ? $el[0] : document;
        var doc = node.ownerDocument || document;
        applyToDocument(doc, node);
      });
    });
  }

  function bindTinyMCEHooks() {
    if (!window.tinymce || typeof window.tinymce.on !== 'function') {
      return;
    }

    window.tinymce.on('AddEditor', function (evt) {
      try {
        var editor = evt && evt.editor;
        if (!editor) return;
        editor.on('init', function () {
          try {
            var body = editor.getBody && editor.getBody();
            if (body) {
              setFlags(body);
              var doc = body.ownerDocument;
              observeDocument(doc);
              applyToDocument(doc, doc);
            }
          } catch (e) {}
        });
      } catch (e) {}
    });
  }

  function boot() {
    applyToDocument(document, document);
    observeDocument(document);
    bindAcfHooks();
    bindTinyMCEHooks();

    document.addEventListener('focusin', function (event) {
      if (event && event.target) {
        markElement(event.target);
      }
    }, true);

    window.setTimeout(function () { applyToDocument(document, document); }, 300);
    window.setTimeout(function () { applyToDocument(document, document); }, 1200);
    window.setTimeout(function () { applyToDocument(document, document); }, 2500);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
