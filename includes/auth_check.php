<?php
/**
 * Authentication Check Middleware
 * Include this file at the top of protected pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
    // Save the requested page to redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    // Redirect to login page
    header('Location: /sts-admin-claude/login.php');
    exit;
}

// Validate session token from database
require_once __DIR__ . '/db.php';

try {
    $db = Database::getInstance();

    $sql = "SELECT s.*, u.is_active
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            WHERE s.session_token = ?
            AND s.user_id = ?
            AND s.expires_at > NOW()";

    $session = $db->fetchOne($sql, [
        $_SESSION['session_token'],
        $_SESSION['user_id']
    ]);

    if (!$session) {
        // Invalid or expired session
        session_destroy();
        header('Location: /sts-admin-claude/login.php?error=session_expired');
        exit;
    }

    if (!$session['is_active']) {
        // User account is deactivated
        session_destroy();
        header('Location: /sts-admin-claude/login.php?error=account_disabled');
        exit;
    }

    // Session is valid - store user info in global variable for easy access
    $GLOBALS['current_user'] = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'],
        'role' => $_SESSION['role']
    ];

} catch (Exception $e) {
    error_log("Auth check error: " . $e->getMessage());
    session_destroy();
    header('Location: /sts-admin-claude/login.php?error=system_error');
    exit;
}
