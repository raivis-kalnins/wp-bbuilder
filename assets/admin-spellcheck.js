(function () {
  if (typeof window.wpbbSpellcheck === 'undefined' || !window.wpbbSpellcheck.enabled) {
    return;
  }

  var config = window.wpbbSpellcheck;
  var lang = config.lang || 'en';
  var selectors = Array.isArray(config.selectors) ? config.selectors : ['input[type="text"]', 'textarea', '[contenteditable="true"]'];
  var iframeSelectors = Array.isArray(config.editorIframeSelectors) ? config.editorIframeSelectors : ['iframe'];
  var observedDocs = new WeakSet();
  var observedFrames = new WeakSet();

  function canSpellcheckInput(el) {
    if (!el || !el.type) return true;
    return ['email', 'password', 'number', 'date', 'datetime-local', 'time', 'tel', 'file', 'hidden', 'color', 'range', 'checkbox', 'radio'].indexOf(el.type) === -1;
  }

  function markElement(el) {
    if (!el || el.nodeType !== 1) return;

    var tag = (el.tagName || '').toLowerCase();
    if (tag === 'iframe') {
      attachToFrame(el);
      return;
    }

    if (el.matches('input, textarea')) {
      if (!canSpellcheckInput(el)) return;
      el.setAttribute('spellcheck', 'true');
      el.setAttribute('lang', lang);
      el.setAttribute('data-wpbb-spellcheck', '1');
      return;
    }

    if (el.isContentEditable || el.getAttribute('contenteditable') === 'true' || el.classList.contains('block-editor-rich-text__editable') || el.classList.contains('mce-content-body')) {
      el.setAttribute('spellcheck', 'true');
      el.setAttribute('lang', lang);
      el.setAttribute('data-wpbb-spellcheck', '1');
    }
  }

  function findIframeCandidates(doc) {
    var frames = [];
    iframeSelectors.forEach(function (selector) {
      try {
        doc.querySelectorAll(selector).forEach(function (frame) { frames.push(frame); });
      } catch (e) {}
    });
    return frames.filter(function (frame, index) { return frames.indexOf(frame) === index; });
  }

  function apply(doc, root) {
    var scope = root && root.querySelectorAll ? root : doc;

    if (doc && doc.documentElement) {
      doc.documentElement.setAttribute('lang', lang);
    }
    if (doc && doc.body) {
      doc.body.setAttribute('lang', lang);
      doc.body.setAttribute('spellcheck', 'true');
    }

    selectors.forEach(function (selector) {
      try {
        scope.querySelectorAll(selector).forEach(markElement);
      } catch (e) {}
    });

    if (root && root.nodeType === 1) {
      markElement(root);
    }

    findIframeCandidates(doc).forEach(attachToFrame);
  }

  function observeDocument(doc) {
    if (!doc || observedDocs.has(doc) || !doc.body) return;
    observedDocs.add(doc);

    apply(doc, doc);

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node && node.nodeType === 1) {
            apply(doc, node);
          }
        });
      });
    });

    observer.observe(doc.body, { childList: true, subtree: true });

    doc.body.addEventListener('focusin', function (event) {
      if (event.target) {
        markElement(event.target);
      }
    });
  }

  function attachToFrame(frame) {
    if (!frame || observedFrames.has(frame)) return;
    observedFrames.add(frame);

    function bindFrame() {
      try {
        var doc = frame.contentDocument || (frame.contentWindow && frame.contentWindow.document);
        if (!doc || !doc.body) return;
        observeDocument(doc);
      } catch (e) {}
    }

    frame.addEventListener('load', bindFrame);
    bindFrame();
  }

  function addIndicator() {
    if (document.getElementById('wpbb-spellcheck-indicator')) {
      return;
    }
    var target = document.querySelector('.edit-post-header, .interface-interface-skeleton__header, .wrap h1, .acf-admin-toolbar');
    if (!target) {
      return;
    }
    var badge = document.createElement('div');
    badge.id = 'wpbb-spellcheck-indicator';
    badge.textContent = config.notice || 'Spellcheck enabled';
    badge.style.cssText = 'margin:8px 0;padding:8px 12px;border-left:4px solid #2271b1;background:#fff;font-size:12px;line-height:1.4;';
    if (target.parentNode) {
      target.parentNode.insertBefore(badge, target.nextSibling);
    }
  }

  function bindAcfHooks() {
    if (!window.acf || typeof window.acf.addAction !== 'function') {
      return;
    }

    ['ready', 'append', 'show', 'new_field'].forEach(function (hook) {
      window.acf.addAction(hook, function ($el) {
        if ($el && $el[0] && $el[0].ownerDocument) {
          apply($el[0].ownerDocument, $el[0]);
        } else {
          apply(document, document);
        }
      });
    });
  }

  function boot() {
    observeDocument(document);
    addIndicator();
    bindAcfHooks();
    window.setInterval(function () {
      apply(document, document);
    }, 2500);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
