<?php
// Server-side verification for reCAPTCHA token
function verify_recaptcha(string $secret): bool {
    $token = isset($_POST['g-recaptcha-response']) ? trim($_POST['g-recaptcha-response']) : '';
    if ($token === '') {
        if (defined('RECAPTCHA_DEBUG') && RECAPTCHA_DEBUG) {
            error_log('reCAPTCHA: missing token');
        }
        return false;
    }

    $endpoint = 'https://www.google.com/recaptcha/api/siteverify';
    $payload = http_build_query([
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
    ]);

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($result === false) {
        if (defined('RECAPTCHA_DEBUG') && RECAPTCHA_DEBUG) {
            error_log('reCAPTCHA cURL error: ' . $err);
        }
        return false;
    }

    $json = json_decode($result, true);
    $ok = is_array($json) && !empty($json['success']);
    if (defined('RECAPTCHA_DEBUG') && RECAPTCHA_DEBUG) {
        error_log('reCAPTCHA response: ' . $result);
    }
    return $ok;
}
