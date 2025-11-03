<?php
/**
 * Helper Functions
 * Common utility functions used throughout the application
 */

/**
 * Sanitize string input
 */
function sanitize_string($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate required field
 */
function validate_required($value) {
    return !empty(trim($value));
}

/**
 * Generate secure random token
 */
function generate_token($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Format file size (bytes to human readable)
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Generate slug from string
 */
function generate_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Generate unique filename
 */
function generate_unique_filename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    $basename = pathinfo($original_filename, PATHINFO_FILENAME);
    $hash = substr(md5(uniqid() . time()), 0, 16);
    return sanitize_string($basename) . '_' . $hash . '.' . $extension;
}

/**
 * Get file type from extension
 */
function get_file_type($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $types = [
        'doc' => 'word',
        'docx' => 'word',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'pdf' => 'pdf',
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'gif' => 'image',
        'webp' => 'image'
    ];

    return $types[$extension] ?? 'other';
}

/**
 * Validate file extension
 */
function validate_file_extension($filename, $allowed_extensions) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $allowed_extensions);
}

/**
 * Get user IP address
 */
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

/**
 * Format date for display
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 */
function format_datetime($datetime, $format = 'M d, Y g:i A') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

/**
 * Check if date is overdue
 */
function is_overdue($date) {
    if (empty($date)) return false;
    return strtotime($date) < strtotime('today');
}

/**
 * Check if date is today
 */
function is_today($date) {
    if (empty($date)) return false;
    return date('Y-m-d', strtotime($date)) === date('Y-m-d');
}

/**
 * Check if date is within this week
 */
function is_this_week($date) {
    if (empty($date)) return false;
    $week_start = strtotime('monday this week');
    $week_end = strtotime('sunday this week');
    $timestamp = strtotime($date);
    return $timestamp >= $week_start && $timestamp <= $week_end;
}

/**
 * Send JSON response
 */
function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send success JSON response
 */
function send_success($data = [], $message = 'Success', $status_code = 200) {
    send_json_response([
        'success' => true,
        'data' => $data,
        'message' => $message
    ], $status_code);
}

/**
 * Send error JSON response
 */
function send_error($message, $error_details = null, $status_code = 400) {
    $response = [
        'success' => false,
        'message' => $message
    ];

    if ($error_details !== null && ENV === 'development') {
        $response['error'] = $error_details;
    }

    send_json_response($response, $status_code);
}

/**
 * Validate JSON input
 */
function get_json_input() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        send_error('Invalid JSON input', json_last_error_msg(), 400);
    }

    return $data;
}

/**
 * Check rate limit (simple implementation using sessions)
 */
function check_rate_limit($key, $max_attempts, $time_window) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $now = time();
    $rate_limit_key = 'rate_limit_' . $key;

    if (!isset($_SESSION[$rate_limit_key])) {
        $_SESSION[$rate_limit_key] = [];
    }

    // Clean old attempts outside time window
    $_SESSION[$rate_limit_key] = array_filter($_SESSION[$rate_limit_key], function($timestamp) use ($now, $time_window) {
        return ($now - $timestamp) < $time_window;
    });

    // Check if limit exceeded
    if (count($_SESSION[$rate_limit_key]) >= $max_attempts) {
        return false;
    }

    // Add current attempt
    $_SESSION[$rate_limit_key][] = $now;
    return true;
}

/**
 * Count words in HTML content
 */
function count_words_html($html) {
    $text = strip_tags($html);
    $text = preg_replace('/\s+/', ' ', $text);
    $words = explode(' ', trim($text));
    return count(array_filter($words));
}

/**
 * Truncate text
 */
function truncate_text($text, $length = 50, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}
