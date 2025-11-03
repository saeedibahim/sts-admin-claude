<?php
/**
 * STS Dashboard Configuration
 * Environment-specific settings for development and production
 */

// Detect environment (can be set via environment variable or default to development)
define('ENV', getenv('APP_ENV') ?: 'development');

// ==========================================
// Database Configuration
// ==========================================
if (ENV === 'production') {
    // Production settings (Hostinger)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
    define('DB_NAME', getenv('DB_NAME') ?: 'sts_dashboard');
} else {
    // Development settings (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3307');  // ← ADD THIS LINE
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'sts_dashboard');
}

// ==========================================
// File Upload Configuration
// ==========================================
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_DOCS_DIR', UPLOAD_DIR . 'documents/');
define('UPLOAD_IMAGES_DIR', UPLOAD_DIR . 'images/');
define('UPLOAD_TEMP_DIR', UPLOAD_DIR . 'temp/');

define('MAX_FILE_SIZE', 10485760); // 10MB in bytes
define('ALLOWED_EXTENSIONS', ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']);

// ==========================================
// API Configuration
// ==========================================
if (ENV === 'production') {
    define('API_KEY', getenv('API_KEY') ?: 'sts_prod_key_change_this');
} else {
    define('API_KEY', 'sts_dev_key_local_12345');
}

// ==========================================
// Session Configuration
// ==========================================
define('SESSION_LIFETIME', 86400); // 24 hours in seconds

// ==========================================
// Application URLs
// ==========================================
if (ENV === 'production') {
    define('BASE_URL', getenv('BASE_URL') ?: 'https://your-domain.com');
    define('APP_PATH', '/');
} else {
    define('BASE_URL', 'http://localhost');
    define('APP_PATH', '/sts-admin-claude/');
}

// ==========================================
// Error Reporting
// ==========================================
if (ENV === 'development') {
    // Show all errors in development
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    // Hide errors in production, log them instead
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// ==========================================
// Timezone
// ==========================================
date_default_timezone_set('UTC');

// ==========================================
// CORS Headers (for contact form API)
// ==========================================
function set_cors_headers($allow_origin = '*') {
    header('Access-Control-Allow-Origin: ' . $allow_origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-API-Key, Authorization');
    header('Access-Control-Max-Age: 3600');

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// ==========================================
// Helper Functions
// ==========================================

/**
 * Get base URL with path
 */
function get_base_url($path = '') {
    return BASE_URL . APP_PATH . ltrim($path, '/');
}

/**
 * Redirect helper
 */
function redirect($path) {
    header('Location: ' . get_base_url($path));
    exit;
}
