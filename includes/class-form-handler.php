<?php
if (!defined('ABSPATH')) exit;

final class WPBB_Form_Handler {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_wpbb_submit_form', [$this, 'submit']);
        add_action('wp_ajax_nopriv_wpbb_submit_form', [$this, 'submit']);
    }

    public function submit() {
        check_ajax_referer('wpbb_form_nonce', 'nonce');

        $fields = json_decode(wp_unslash($_POST['fields'] ?? '[]'), true);
        $settings = json_decode(wp_unslash($_POST['settings'] ?? '{}'), true);

        if (!is_array($fields)) $fields = [];
        if (!is_array($settings)) $settings = [];

        $recipient = sanitize_email($settings['recipient'] ?? wpbb_get_option('default_recipient_email', get_option('admin_email')));
        $subject = sanitize_text_field($settings['email_subject'] ?? __('New form submission', 'wp-bbuilder'));
        $success = sanitize_text_field($settings['success_message'] ?? wpbb_get_option('default_success_message', __('Thank you for your submission!', 'wp-bbuilder')));

        $lines = [];
        foreach ($fields as $field) {
            $label = sanitize_text_field($field['label'] ?? 'Field');
            $value = sanitize_textarea_field($field['value'] ?? '');
            $lines[] = $label . ': ' . $value;
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

        if ($recipient) {
            wp_mail($recipient, $subject, $message, [], $attachments);
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
}
