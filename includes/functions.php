<?php

// Start the session only once.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect the browser to another page.
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Escape output before displaying database or user data in HTML.
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Create and store a CSRF token for forms and AJAX requests.
function csrf_token() {

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Check whether the supplied CSRF token is valid.
function verify_csrf($token) {

    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token ?? '');

}

// Require an administrator to be logged in.
function requireLogin() {

    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }

}

// Return a safe integer for quantities and IDs.
function positiveInt($value, $default = 0) {

    $number = filter_var($value, FILTER_VALIDATE_INT);

    if ($number === false || $number < 0) {
        return $default;
    }

    return $number;

}

// Return the ticket price as a formatted currency value.
function money($amount) {
    return 'LKR ' . number_format((float)$amount, 2);
}

?>
