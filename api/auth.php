<?php
/**
 * Authentication API
 * Handles login and logout operations
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    switch ($method) {
        case 'POST':
            // Handle login
            $data = get_json_input();

            // Validate required fields
            if (!isset($data['username']) || !isset($data['password'])) {
                send_error('Username and password are required', null, 400);
            }

            $username = sanitize_string($data['username']);
            $password = $data['password'];

            // Validate inputs
            if (empty($username) || empty($password)) {
                send_error('Username and password cannot be empty', null, 400);
            }

            // Find user by username or email
            $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1";
            $user = $db->fetchOne($sql, [$username, $username]);

            if (!$user) {
                send_error('Invalid credentials', null, 401);
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                send_error('Invalid credentials', null, 401);
            }

            // Generate session token
            $session_token = generate_token(64);
            $expires_at = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);

            // Store session in database
            $sql = "INSERT INTO sessions (user_id, session_token, ip_address, user_agent, expires_at)
                    VALUES (?, ?, ?, ?, ?)";

            $db->execute($sql, [
                $user['id'],
                $session_token,
                get_client_ip(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expires_at
            ]);

            // Update last login
            $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $db->execute($sql, [$user['id']]);

            // Store in session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['session_token'] = $session_token;

            // Clean old sessions for this user (keep only last 5)
            $sql = "DELETE FROM sessions
                    WHERE user_id = ?
                    AND id NOT IN (
                        SELECT id FROM (
                            SELECT id FROM sessions
                            WHERE user_id = ?
                            ORDER BY created_at DESC
                            LIMIT 5
                        ) AS recent_sessions
                    )";
            $db->execute($sql, [$user['id'], $user['id']]);

            send_success([
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ],
                'redirect' => $_SESSION['redirect_after_login'] ?? 'index.html'
            ], 'Login successful');

            break;

        case 'DELETE':
            // Handle logout
            if (isset($_SESSION['session_token']) && isset($_SESSION['user_id'])) {
                // Delete session from database
                $sql = "DELETE FROM sessions WHERE session_token = ? AND user_id = ?";
                $db->execute($sql, [$_SESSION['session_token'], $_SESSION['user_id']]);
            }

            // Destroy session
            session_destroy();

            send_success([], 'Logout successful');
            break;

        case 'GET':
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                send_error('Not authenticated', null, 401);
            }

            send_success([
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'full_name' => $_SESSION['full_name'],
                    'role' => $_SESSION['role']
                ]
            ]);
            break;

        default:
            send_error('Method not allowed', null, 405);
    }

} catch (Exception $e) {
    error_log("Auth API error: " . $e->getMessage());
    send_error('Authentication failed', $e->getMessage(), 500);
}
