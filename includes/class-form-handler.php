<?php
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

        $message = implode("
", $lines);

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
