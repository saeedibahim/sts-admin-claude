<?php
// Temporary debug file - DELETE after fixing!
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

echo json_encode([
    'test' => 'API is reachable',
    'php_version' => phpversion(),
    'current_dir' => __DIR__
]);

// Test database connection
try {
    require_once '../includes/db.php';
    echo json_encode(['db_status' => 'Database connection successful']);
} catch (Exception $e) {
    echo json_encode(['db_error' => $e->getMessage()]);
}
?>