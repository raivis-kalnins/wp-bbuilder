from pathlib import Path
root = Path('/mnt/data/wp-bbuilder')

# helpers.php
p = root/'includes/helpers.php'
text = p.read_text()
text = text.replace("        'hcaptcha_site_key' => '','hcaptcha_secret_key' => '','recaptcha_site_key' => '','recaptcha_secret_key' => '',\n",
"        'hcaptcha_site_key' => '','hcaptcha_secret_key' => '','recaptcha_site_key' => '','recaptcha_secret_key' => '',\n        'smtp_enabled' => 0,\n        'smtp_host' => '',\n        'smtp_port' => '587',\n        'smtp_encryption' => 'tls',\n        'smtp_username' => '',\n        'smtp_password' => '',\n        'smtp_from_email' => '',\n        'smtp_from_name' => '',\n        'form_honeypot_enabled' => 1,\n        'form_min_submit_time' => '3',\n        'form_spam_message' => __('Your submission was blocked as spam. Please try again.', 'wp-bbuilder'),\n")
p.write_text(text)

# class-settings.php
p = root/'includes/class-settings.php'
text = p.read_text()
text = text.replace("foreach (['disable_core_group','disable_core_columns','disable_core_column','disable_core_table','disable_core_embed','disable_core_gallery','disable_core_image','disable_core_cover','disable_core_media_text','disable_core_buttons','disable_core_button','disable_core_query','load_bootstrap_css','load_bootstrap_js','load_shared_css','load_bootstrap_editor_css','force_bootstrap_enqueue','save_entries','bootstrap_optimize_frontend','bootstrap_enable_utilities','bootstrap_allow_custom_classes','cookie_consent_enabled','google_analytics_enabled','admin_spellcheck_enabled'] as $flag) {",
"foreach (['disable_core_group','disable_core_columns','disable_core_column','disable_core_table','disable_core_embed','disable_core_gallery','disable_core_image','disable_core_cover','disable_core_media_text','disable_core_buttons','disable_core_button','disable_core_query','load_bootstrap_css','load_bootstrap_js','load_shared_css','load_bootstrap_editor_css','force_bootstrap_enqueue','save_entries','bootstrap_optimize_frontend','bootstrap_enable_utilities','bootstrap_allow_custom_classes','cookie_consent_enabled','google_analytics_enabled','admin_spellcheck_enabled','smtp_enabled','form_honeypot_enabled'] as $flag) {")
text = text.replace("foreach (['default_recipient_email','default_success_message','default_error_message','default_validation_text','button_class','form_class','admin_max_width','hcaptcha_site_key','hcaptcha_secret_key','recaptcha_site_key','recaptcha_secret_key','whatsapp_phone','whatsapp_message','whatsapp_position','cookie_consent_text','cookie_accept_text','cookie_reject_text','cookie_policy_url','cookie_position'] as $field) {",
"foreach (['default_recipient_email','default_success_message','default_error_message','default_validation_text','button_class','form_class','admin_max_width','hcaptcha_site_key','hcaptcha_secret_key','recaptcha_site_key','recaptcha_secret_key','smtp_host','smtp_port','smtp_username','smtp_password','smtp_from_email','smtp_from_name','form_min_submit_time','form_spam_message','whatsapp_phone','whatsapp_message','whatsapp_position','cookie_consent_text','cookie_accept_text','cookie_reject_text','cookie_policy_url','cookie_position'] as $field) {")
text = text.replace("        $out['bootstrap_css_mode'] = in_array(($input['bootstrap_css_mode'] ?? 'full'), ['full','custom','grid','utilities'], true) ? $input['bootstrap_css_mode'] : 'full';\n",
"        $out['bootstrap_css_mode'] = in_array(($input['bootstrap_css_mode'] ?? 'full'), ['full','custom','grid','utilities'], true) ? $input['bootstrap_css_mode'] : 'full';\n        $out['smtp_encryption'] = in_array(($input['smtp_encryption'] ?? 'tls'), ['none','ssl','tls'], true) ? $input['smtp_encryption'] : 'tls';\n")
p.write_text(text)

# class-admin.php add SMTP + spam UI after recaptcha secret key
p = root/'includes/class-admin.php'
text = p.read_text()
old = "                        <p><label>reCAPTCHA secret key<br><input type=\"text\" name=\"wpbb_settings[recaptcha_secret_key]\" value=\"<?php echo esc_attr($opts['recaptcha_secret_key']); ?>\"></label></p>\n                        <h3><?php esc_html_e('Form colors', 'wp-bbuilder'); ?></h3>\n"
new = "                        <p><label>reCAPTCHA secret key<br><input type=\"text\" name=\"wpbb_settings[recaptcha_secret_key]\" value=\"<?php echo esc_attr($opts['recaptcha_secret_key']); ?>\"></label></p>\n                        <h3><?php esc_html_e('SMTP delivery', 'wp-bbuilder'); ?></h3>\n                        <label class=\"wpbb-check\"><input type=\"checkbox\" name=\"wpbb_settings[smtp_enabled]\" value=\"1\" <?php checked(!empty($opts['smtp_enabled'])); ?>><span><?php esc_html_e('Send form emails via SMTP', 'wp-bbuilder'); ?></span></label>\n                        <p class=\"description\"><?php esc_html_e('Helps form submissions avoid spam folders by using authenticated SMTP instead of the default PHP mail transport.', 'wp-bbuilder'); ?></p>\n                        <p><label><?php esc_html_e('SMTP host', 'wp-bbuilder'); ?><br><input type=\"text\" name=\"wpbb_settings[smtp_host]\" value=\"<?php echo esc_attr($opts['smtp_host'] ?? ''); ?>\"></label></p>\n                        <div class=\"wpbb-subsettings\">\n                            <p><label><?php esc_html_e('SMTP port', 'wp-bbuilder'); ?><br><input type=\"text\" name=\"wpbb_settings[smtp_port]\" value=\"<?php echo esc_attr($opts['smtp_port'] ?? '587'); ?>\"></label></p>\n                            <p><label><?php esc_html_e('Encryption', 'wp-bbuilder'); ?><br>\n                                <select name=\"wpbb_settings[smtp_encryption]\">\n                                    <option value=\"tls\" <?php selected(($opts['smtp_encryption'] ?? 'tls'), 'tls'); ?>>TLS</option>\n                                    <option value=\"ssl\" <?php selected(($opts['smtp_encryption'] ?? 'tls'), 'ssl'); ?>>SSL</option>\n                                    <option value=\"none\" <?php selected(($opts['smtp_encryption'] ?? 'tls'), 'none'); ?>><?php esc_html_e('None', 'wp-bbuilder'); ?></option>\n                                </select>\n                            </label></p>\n                        </div>\n                        <p><label><?php esc_html_e('SMTP username', 'wp-bbuilder'); ?><br><input type=\"text\" name=\"wpbb_settings[smtp_username]\" value=\"<?php echo esc_attr($opts['smtp_username'] ?? ''); ?>\" autocomplete=\"off\"></label></p>\n                        <p><label><?php esc_html_e('SMTP password', 'wp-bbuilder'); ?><br><input type=\"password\" name=\"wpbb_settings[smtp_password]\" value=\"<?php echo esc_attr($opts['smtp_password'] ?? ''); ?>\" autocomplete=\"new-password\"></label></p>\n                        <div class=\"wpbb-subsettings\">\n                            <p><label><?php esc_html_e('From email', 'wp-bbuilder'); ?><br><input type=\"email\" name=\"wpbb_settings[smtp_from_email]\" value=\"<?php echo esc_attr($opts['smtp_from_email'] ?? ''); ?>\"></label></p>\n                            <p><label><?php esc_html_e('From name', 'wp-bbuilder'); ?><br><input type=\"text\" name=\"wpbb_settings[smtp_from_name]\" value=\"<?php echo esc_attr($opts['smtp_from_name'] ?? ''); ?>\"></label></p>\n                        </div>\n                        <h3><?php esc_html_e('Spam protection', 'wp-bbuilder'); ?></h3>\n                        <label class=\"wpbb-check\"><input type=\"checkbox\" name=\"wpbb_settings[form_honeypot_enabled]\" value=\"1\" <?php checked(!empty($opts['form_honeypot_enabled'])); ?>><span><?php esc_html_e('Enable hidden honeypot + time trap', 'wp-bbuilder'); ?></span></label>\n                        <p><label><?php esc_html_e('Minimum submit time (seconds)', 'wp-bbuilder'); ?><br><input type=\"number\" min=\"0\" step=\"1\" name=\"wpbb_settings[form_min_submit_time]\" value=\"<?php echo esc_attr($opts['form_min_submit_time'] ?? '3'); ?>\"></label></p>\n                        <p><label><?php esc_html_e('Spam blocked message', 'wp-bbuilder'); ?><br><input type=\"text\" name=\"wpbb_settings[form_spam_message]\" value=\"<?php echo esc_attr($opts['form_spam_message'] ?? __('Your submission was blocked as spam. Please try again.', 'wp-bbuilder')); ?>\"></label></p>\n                        <h3><?php esc_html_e('Form colors', 'wp-bbuilder'); ?></h3>\n"
if old not in text:
    raise SystemExit('admin forms anchor not found')
text = text.replace(old, new)
p.write_text(text)

# class-form-handler.php replace entirely for clarity
p = root/'includes/class-form-handler.php'
text = p.read_text()
text = """<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Form_Handler {
    private static $instance = null;
    private $smtp_filter_added = false;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_wpbb_submit_form', [$this, 'submit']);
        add_action('wp_ajax_nopriv_wpbb_submit_form', [$this, 'submit']);
    }

    private function get_spam_message() {
        return sanitize_text_field(wpbb_get_option('form_spam_message', __('Your submission was blocked as spam. Please try again.', 'wp-bbuilder')));
    }

    private function is_spam_request() {
        if (!wpbb_get_option('form_honeypot_enabled', 1)) {
            return false;
        }

        $honeypot = isset($_POST['website']) ? trim((string) wp_unslash($_POST['website'])) : '';
        if ($honeypot !== '') {
            return true;
        }

        $started_at = isset($_POST['started_at']) ? (int) $_POST['started_at'] : 0;
        $minimum_seconds = max(0, (int) wpbb_get_option('form_min_submit_time', '3'));
        if ($minimum_seconds > 0 && $started_at > 0 && (time() - $started_at) < $minimum_seconds) {
            return true;
        }

        return false;
    }

    public function submit() {
        check_ajax_referer('wpbb_form_nonce', 'nonce');

        if ($this->is_spam_request()) {
            wp_send_json_error(['message' => $this->get_spam_message()], 422);
        }

        $fields = json_decode(wp_unslash($_POST['fields'] ?? '[]'), true);
        $settings = json_decode(wp_unslash($_POST['settings'] ?? '{}'), true);

        if (!is_array($fields)) $fields = [];
        if (!is_array($settings)) $settings = [];

        $recipient = sanitize_email($settings['recipient'] ?? wpbb_get_option('default_recipient_email', get_option('admin_email')));
        $subject = sanitize_text_field($settings['email_subject'] ?? __('New form submission', 'wp-bbuilder'));
        $success = sanitize_text_field($settings['success_message'] ?? wpbb_get_option('default_success_message', __('Thank you for your submission!', 'wp-bbuilder')));
        $error = sanitize_text_field(wpbb_get_option('default_error_message', __('Something went wrong. Please try again.', 'wp-bbuilder')));

        $lines = [];
        $reply_to_email = '';
        $reply_to_name = '';
        foreach ($fields as $field) {
            $label = sanitize_text_field($field['label'] ?? 'Field');
            $value = sanitize_textarea_field($field['value'] ?? '');
            $name = sanitize_key($field['name'] ?? '');
            if ($name === 'website') {
                continue;
            }
            $lines[] = $label . ': ' . $value;
            if ($reply_to_email === '' && is_email($value) && ($name === 'email' || stripos($label, 'email') !== false)) {
                $reply_to_email = sanitize_email($value);
            }
            if ($reply_to_name === '' && ($name === 'name' || stripos($label, 'name') !== false)) {
                $reply_to_name = sanitize_text_field($value);
            }
        }

        $attachments = [];
        if (!empty($_FILES)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            foreach ($_FILES as $key => $file) {
                if (empty($file['name'])) continue;
                $uploaded = wp_handle_upload($file, ['test_form' => false]);
                if (!empty($uploaded['file'])) {
                    $attachments[] = $uploaded['file'];
                    $lines[] = sanitize_text_field($key) . ': ' . esc_url_raw($uploaded['url'] ?? '');
                }
            }
        }

        $message = implode("\n", $lines);

        $headers = [];
        $smtp_from_email = sanitize_email(wpbb_get_option('smtp_from_email', ''));
        $smtp_from_name = sanitize_text_field(wpbb_get_option('smtp_from_name', ''));
        if ($smtp_from_email) {
            $headers[] = 'From: ' . ($smtp_from_name ? $smtp_from_name . ' <' . $smtp_from_email . '>' : $smtp_from_email);
        }
        if ($reply_to_email) {
            $headers[] = 'Reply-To: ' . ($reply_to_name ? $reply_to_name . ' <' . $reply_to_email . '>' : $reply_to_email);
        }

        $sent = true;
        if ($recipient) {
            $this->maybe_enable_smtp();
            $sent = wp_mail($recipient, $subject, $message, $headers, $attachments);
            $this->disable_smtp();
        }

        if (!$sent) {
            wp_send_json_error(['message' => $error], 500);
        }

        if (wpbb_get_option('save_entries', 1)) {
            wp_insert_post([
                'post_type' => 'wpbb_entry',
                'post_status' => 'publish',
                'post_title' => 'Form Entry ' . current_time('mysql'),
                'meta_input' => [
                    '_wpbb_fields' => $fields,
                    '_wpbb_settings' => $settings,
                    '_wpbb_attachments' => $attachments,
                ],
            ]);
        }

        wp_send_json_success(['message' => $success]);
    }

    public function configure_phpmailer($phpmailer) {
        $host = sanitize_text_field(wpbb_get_option('smtp_host', ''));
        if ($host === '') {
            return;
        }

        $port = (int) wpbb_get_option('smtp_port', '587');
        $username = (string) wpbb_get_option('smtp_username', '');
        $password = (string) wpbb_get_option('smtp_password', '');
        $encryption = (string) wpbb_get_option('smtp_encryption', 'tls');
        $from_email = sanitize_email(wpbb_get_option('smtp_from_email', get_option('admin_email')));
        $from_name = sanitize_text_field(wpbb_get_option('smtp_from_name', get_bloginfo('name')));

        $phpmailer->isSMTP();
        $phpmailer->Host = $host;
        $phpmailer->Port = $port > 0 ? $port : 587;
        $phpmailer->SMTPAuth = ($username !== '');
        $phpmailer->Username = $username;
        $phpmailer->Password = $password;
        $phpmailer->SMTPSecure = ($encryption === 'none') ? '' : $encryption;
        $phpmailer->CharSet = 'UTF-8';
        if ($from_email) {
            $phpmailer->setFrom($from_email, $from_name ?: get_bloginfo('name'), false);
        }
    }

    private function maybe_enable_smtp() {
        if (!wpbb_get_option('smtp_enabled', 0) || $this->smtp_filter_added) {
            return;
        }
        add_action('phpmailer_init', [$this, 'configure_phpmailer']);
        $this->smtp_filter_added = true;
    }

    private function disable_smtp() {
        if (!$this->smtp_filter_added) {
            return;
        }
        remove_action('phpmailer_init', [$this, 'configure_phpmailer']);
        $this->smtp_filter_added = false;
    }
}
"""
p.write_text(text)

# class-blocks.php add honeypot fields and data attr
p = root/'includes/class-blocks.php'
text = p.read_text()
text = text.replace("        $hcaptcha_site_key = wpbb_get_option('hcaptcha_site_key', '');\n        $recaptcha_site_key = wpbb_get_option('recaptcha_site_key', '');\n",
"        $hcaptcha_site_key = wpbb_get_option('hcaptcha_site_key', '');\n        $recaptcha_site_key = wpbb_get_option('recaptcha_site_key', '');\n        $honeypot_enabled = (bool) wpbb_get_option('form_honeypot_enabled', 1);\n")
text = text.replace("            <form class=\"<?php echo $form_class; ?> wpbb-dynamic-form\" data-recipient=\"<?php echo $recipient; ?>\" data-subject=\"<?php echo $subject; ?>\" data-success=\"<?php echo $success; ?>\" data-validation=\"<?php echo $validation; ?>\" data-steps=\"<?php echo esc_attr($enable_steps ? '1' : '0'); ?>\" data-conditional=\"<?php echo esc_attr($enable_conditional ? '1' : '0'); ?>\" enctype=\"multipart/form-data\">",
"            <form class=\"<?php echo $form_class; ?> wpbb-dynamic-form\" data-recipient=\"<?php echo $recipient; ?>\" data-subject=\"<?php echo $subject; ?>\" data-success=\"<?php echo $success; ?>\" data-validation=\"<?php echo $validation; ?>\" data-steps=\"<?php echo esc_attr($enable_steps ? '1' : '0'); ?>\" data-conditional=\"<?php echo esc_attr($enable_conditional ? '1' : '0'); ?>\" data-honeypot=\"<?php echo esc_attr($honeypot_enabled ? '1' : '0'); ?>\" enctype=\"multipart/form-data\">")
text = text.replace("                <?php if ($enable_steps && $step_total > 1): ?>\n",
"                <?php if ($honeypot_enabled): ?>\n                    <div class=\"wpbb-form-bot-field\" hidden aria-hidden=\"true\">\n                        <label for=\"<?php echo esc_attr('wpbb-website-' . wp_unique_id()); ?>\"><?php esc_html_e('Leave this field empty', 'wp-bbuilder'); ?></label>\n                        <input type=\"text\" name=\"website\" value=\"\" tabindex=\"-1\" autocomplete=\"off\">\n                        <input type=\"hidden\" name=\"started_at\" value=\"<?php echo esc_attr(time()); ?>\">\n                    </div>\n                <?php endif; ?>\n                <?php if ($enable_steps && $step_total > 1): ?>\n")
p.write_text(text)

# assets/form.js append honeypot fields
p = root/'assets/form.js'
text = p.read_text()
old = "    payload.append('settings', JSON.stringify({\n      recipient: form.dataset.recipient || '',\n      email_subject: form.dataset.subject || '',\n      success_message: form.dataset.success || ''\n    }));\n"
new = "    payload.append('settings', JSON.stringify({\n      recipient: form.dataset.recipient || '',\n      email_subject: form.dataset.subject || '',\n      success_message: form.dataset.success || ''\n    }));\n    var honeypot = form.querySelector('input[name=\"website\"]');\n    var startedAt = form.querySelector('input[name=\"started_at\"]');\n    if (honeypot) payload.append('website', honeypot.value || '');\n    if (startedAt) payload.append('started_at', startedAt.value || '');\n"
if old not in text:
    raise SystemExit('form.js anchor not found')
text = text.replace(old, new)
p.write_text(text)

# class-spellcheck.php replace selectors and localize extra flags
p = root/'includes/class-spellcheck.php'
text = p.read_text()
text = text.replace("            'selectors' => [\n                'input[type=\"text\"]',\n                'input[type=\"search\"]',\n                'input[type=\"url\"]',\n                'input:not([type])',\n                'textarea',\n                '[contenteditable=\"true\"]',\n                '.block-editor-rich-text__editable',\n                '.editor-post-title__input',\n                '.acf-input input[type=\"text\"]',\n                '.acf-input input:not([type])',\n                '.acf-input textarea',\n                '.acf-field[data-type=\"wysiwyg\"] iframe',\n                '.mce-content-body',\n            ],\n",
"            'selectors' => [\n                'input[type=\"text\"]',\n                'input[type=\"search\"]',\n                'input[type=\"url\"]',\n                'input:not([type])',\n                'textarea',\n                '[contenteditable=\"true\"]',\n                '.block-editor-rich-text__editable',\n                '.editor-post-title__input',\n                '.acf-input input[type=\"text\"]',\n                '.acf-input input:not([type])',\n                '.acf-input textarea',\n                '.acf-field[data-type=\"text\"] input',\n                '.acf-field[data-type=\"textarea\"] textarea',\n                '.acf-field[data-type=\"wysiwyg\"] iframe',\n                '.mce-content-body',\n                'iframe',\n            ],\n")
text = text.replace("            'notice' => sprintf(\n", "            'editorIframeSelectors' => ['iframe.editor-canvas__iframe', 'iframe[name=\"editor-canvas\"]', 'iframe'],\n            'notice' => sprintf(\n")
p.write_text(text)

# assets/admin-spellcheck.js replace entirely with more robust iframe-aware version
p = root/'assets/admin-spellcheck.js'
text = """(function () {
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
"""
p.write_text(text)
