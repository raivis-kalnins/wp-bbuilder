(function () {
  if (typeof window.wpbbSpellcheck === 'undefined' || !window.wpbbSpellcheck.enabled) {
    return;
  }

  var config = window.wpbbSpellcheck;
  var lang = config.lang || 'en';
  var selectors = Array.isArray(config.selectors) ? config.selectors : ['input[type="text"]', 'textarea', '[contenteditable="true"]'];

  function markElement(el) {
    if (!el || el.nodeType !== 1) {
      return;
    }

    var tag = (el.tagName || '').toLowerCase();
    if (tag === 'iframe') {
      try {
        var doc = el.contentDocument || (el.contentWindow && el.contentWindow.document);
        if (doc && doc.body) {
          doc.documentElement.setAttribute('lang', lang);
          doc.body.setAttribute('lang', lang);
          doc.body.setAttribute('spellcheck', 'true');
          if (!doc.body.hasAttribute('data-wpbb-spellcheck-ready')) {
            doc.body.setAttribute('data-wpbb-spellcheck-ready', '1');
            doc.body.addEventListener('focus', function () {
              doc.body.setAttribute('spellcheck', 'true');
            });
          }
        }
      } catch (e) {}
      return;
    }

    if (el.matches('input, textarea')) {
      if (el.type && ['email', 'password', 'number', 'date', 'datetime-local', 'time', 'tel', 'file', 'hidden', 'color', 'range', 'checkbox', 'radio'].indexOf(el.type) !== -1) {
        return;
      }
      el.setAttribute('spellcheck', 'true');
      el.setAttribute('lang', lang);
      el.setAttribute('data-wpbb-spellcheck', '1');
      return;
    }

    if (el.isContentEditable || el.getAttribute('contenteditable') === 'true' || el.classList.contains('block-editor-rich-text__editable')) {
      el.setAttribute('spellcheck', 'true');
      el.setAttribute('lang', lang);
      el.setAttribute('data-wpbb-spellcheck', '1');
    }
  }

  function apply(root) {
    var scope = root && root.querySelectorAll ? root : document;
    selectors.forEach(function (selector) {
      try {
        scope.querySelectorAll(selector).forEach(markElement);
      } catch (e) {}
    });
    if (root && root.nodeType === 1) {
      markElement(root);
    }
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

  document.addEventListener('DOMContentLoaded', function () {
    document.documentElement.setAttribute('lang', lang);
    apply(document);
    addIndicator();

    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node && node.nodeType === 1) {
            apply(node);
          }
        });
      });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    document.body.addEventListener('focusin', function (event) {
      if (event.target) {
        markElement(event.target);
      }
    });

    window.setInterval(function () {
      apply(document);
    }, 2500);
  });
})();
