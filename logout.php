<?php
/**
 * Logout Handler
 * Destroys session and redirects to login
 */

require_once __DIR__ . '/includes/db.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Delete session from database if exists
if (isset($_SESSION['session_token']) && isset($_SESSION['user_id'])) {
    try {
        $db = Database::getInstance();
        $sql = "DELETE FROM sessions WHERE session_token = ? AND user_id = ?";
        $db->execute($sql, [$_SESSION['session_token'], $_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Destroy session
session_destroy();

// Redirect to login page with success message
header('Location: login.php?success=logout');
exit;
