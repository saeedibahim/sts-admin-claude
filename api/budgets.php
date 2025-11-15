<?php
/**
 * Categories API
 * Handles expense category CRUD operations
 */

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    switch ($method) {
        case 'GET':
            getCategories($db);
            break;

        case 'POST':
            createCategory($db);
            break;

        case 'PUT':
            updateCategory($db);
            break;

        case 'DELETE':
            deleteCategory($db);
            break;

        default:
            send_error('Method not allowed', null, 405);
    }

} catch (Exception $e) {
    error_log("Categories API error: " . $e->getMessage());
    send_error('Operation failed', $e->getMessage(), 500);
}

/**
 * Get all categories with usage stats
 */
function getCategories($db) {
    $sql = "SELECT 
                ec.*,
                COUNT(e.id) as expense_count,
                COALESCE(SUM(e.amount), 0) as total_spent
            FROM expense_categories ec
            LEFT JOIN expenses e ON ec.id = e.category_id AND e.status != 'Cancelled'
            GROUP BY ec.id
            ORDER BY ec.name ASC";
    
    $categories = $db->fetchAll($sql);
    
    send_success($categories, 'Categories retrieved successfully');
}

/**
 * Create new category
 */
function createCategory($db) {
    $input = get_json_input();
    
    // Validate required fields
    if (!isset($input['name'])) {
        send_error('Category name is required', null, 400);
    }
    
    $sql = "INSERT INTO expense_categories (
                name, description, color, icon, is_active
            ) VALUES (?, ?, ?, ?, ?)";
    
    $params = [
        $input['name'],
        $input['description'] ?? null,
        $input['color'] ?? '#45C4B0',
        $input['icon'] ?? 'tag',
        $input['is_active'] ?? 1
    ];
    
    $result = $db->execute($sql, $params);
    
    if ($result) {
        $categoryId = $db->lastInsertId();
        send_success(['id' => $categoryId], 'Category created successfully', 201);
    } else {
        send_error('Failed to create category', null, 500);
    }
}

/**
 * Update category
 */
function updateCategory($db) {
    $input = get_json_input();
    
    if (!isset($input['id'])) {
        send_error('Category ID is required', null, 400);
    }
    
    $sql = "UPDATE expense_categories SET
                name = ?,
                description = ?,
                color = ?,
                icon = ?,
                is_active = ?,
                updated_at = NOW()
            WHERE id = ?";
    
    $params = [
        $input['name'],
        $input['description'] ?? null,
        $input['color'] ?? '#45C4B0',
        $input['icon'] ?? 'tag',
        $input['is_active'] ?? 1,
        $input['id']
    ];
    
    $result = $db->execute($sql, $params);
    
    if ($result !== false) {
        send_success(['affected_rows' => $result], 'Category updated successfully');
    } else {
        send_error('Failed to update category', null, 500);
    }
}

/**
 * Delete category
 */
function deleteCategory($db) {
    $input = get_json_input();
    
    if (!isset($input['id'])) {
        send_error('Category ID is required', null, 400);
    }
    
    // Check if category is being used
    $checkSql = "SELECT COUNT(*) as count FROM expenses WHERE category_id = ?";
    $check = $db->fetchOne($checkSql, [$input['id']]);
    
    if ($check['count'] > 0) {
        send_error('Cannot delete category that is in use by expenses', null, 400);
    }
    
    $sql = "DELETE FROM expense_categories WHERE id = ?";
    $result = $db->execute($sql, [$input['id']]);
    
    if ($result) {
        send_success(['affected_rows' => $result], 'Category deleted successfully');
    } else {
        send_error('Failed to delete category', null, 500);
    }
}
?>