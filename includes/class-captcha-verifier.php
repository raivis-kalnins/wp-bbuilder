<?php
class BBlocks_Captcha_Verifier {
    
    public static function verify($response, $secret_key, $type = 'hcaptcha') {
        if (empty($response) || empty($secret_key)) {
            return false;
        }

        switch ($type) {
            case 'hcaptcha':
                return self::verify_hcaptcha($response, $secret_key);
            case 'recaptcha_v2':
            case 'recaptcha_v3':
                return self::verify_recaptcha($response, $secret_key);
            default:
                return false;
        }
    }

    private static function verify_hcaptcha($response, $secret_key) {
        $verify_url = 'https://hcaptcha.com/siteverify';
        
        $args = [
            'body' => [
                'secret' => $secret_key,
                'response' => $response
            ],
            'timeout' => 30
        ];

        $result = wp_remote_post($verify_url, $args);
        
        if (is_wp_error($result)) {
            error_log('hCaptcha verification error: ' . $result->get_error_message());
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($result), true);
        
        if (isset($body['success']) && $body['success'] === true) {
            return true;
        }

        if (isset($body['error-codes'])) {
            error_log('hCaptcha errors: ' . implode(', ', $body['error-codes']));
        }

        return false;
    }

    private static function verify_recaptcha($response, $secret_key) {
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        
        $args = [
            'body' => [
                'secret' => $secret_key,
                'response' => $response
            ],
            'timeout' => 30
        ];

        $result = wp_remote_post($verify_url, $args);
        
        if (is_wp_error($result)) {
            error_log('reCaptcha verification error: ' . $result->get_error_message());
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($result), true);
        
        // For v3, also check score
        if (isset($body['score']) && $body['score'] < 0.5) {
            error_log('reCaptcha v3 score too low: ' . $body['score']);
            return false;
        }

        return isset($body['success']) && $body['success'] === true;
    }
}