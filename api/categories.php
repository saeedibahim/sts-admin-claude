<?php
/**
 * Expense Categories API
 * CRUD operations for expense categories
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

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    switch ($method) {
        case 'GET':
            // Get category/categories
            if (isset($_GET['id'])) {
                // Get single category
                $id = (int) $_GET['id'];
                $sql = "SELECT 
                            ec.*,
                            COUNT(e.id) as expense_count,
                            COALESCE(SUM(e.amount), 0) as total_spent
                        FROM expense_categories ec
                        LEFT JOIN expenses e ON ec.id = e.category_id AND e.status != 'Cancelled'
                        WHERE ec.id = ?
                        GROUP BY ec.id";
                
                $category = $db->fetchOne($sql, [$id]);

                if (!$category) {
                    send_error('Category not found', null, 404);
                }

                send_success($category, 'Category retrieved successfully');
            } else {
                // Get all categories
                $where = [];
                $params = [];

                // Filter by active status
                if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
                    $where[] = "ec.is_active = ?";
                    $params[] = (int) $_GET['is_active'];
                }

                // Search functionality
                if (isset($_GET['search']) && $_GET['search'] !== '') {
                    $search = '%' . $_GET['search'] . '%';
                    $where[] = "(ec.name LIKE ? OR ec.description LIKE ?)";
                    $params[] = $search;
                    $params[] = $search;
                }

                // Build query
                $sql = "SELECT 
                            ec.*,
                            COUNT(e.id) as expense_count,
                            COALESCE(SUM(e.amount), 0) as total_spent
                        FROM expense_categories ec
                        LEFT JOIN expenses e ON ec.id = e.category_id AND e.status != 'Cancelled'";

                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }

                $sql .= " GROUP BY ec.id ORDER BY ec.name ASC";

                $categories = $db->fetchAll($sql, $params);

                send_success($categories, 'Categories retrieved successfully');
            }
            break;

        case 'POST':
            // Create new category
            $data = get_json_input();

            // Validate required fields
            if (!validate_required($data['name'] ?? '')) {
                send_error('Category name is required', null, 400);
            }

            // Check if category name already exists
            $existing = $db->fetchOne(
                "SELECT id FROM expense_categories WHERE name = ?",
                [sanitize_string($data['name'])]
            );

            if ($existing) {
                send_error('Category with this name already exists', null, 400);
            }

            // Insert category
            $sql = "INSERT INTO expense_categories (
                        name, description, color, icon, is_active
                    ) VALUES (?, ?, ?, ?, ?)";

            $db->execute($sql, [
                sanitize_string($data['name']),
                sanitize_string($data['description'] ?? ''),
                $data['color'] ?? '#45C4B0',
                $data['icon'] ?? 'tag',
                isset($data['is_active']) ? (int) $data['is_active'] : 1
            ]);

            $categoryId = $db->lastInsertId();

            send_success(['id' => $categoryId], 'Category created successfully', 201);
            break;

        case 'PUT':
            // Update category
            $data = get_json_input();

            if (!isset($data['id'])) {
                send_error('Category ID is required', null, 400);
            }

            $id = (int) $data['id'];

            // Check if category exists
            $existing = $db->fetchOne("SELECT id FROM expense_categories WHERE id = ?", [$id]);
            if (!$existing) {
                send_error('Category not found', null, 404);
            }

            // Check for duplicate name (excluding current category)
            if (isset($data['name'])) {
                $duplicate = $db->fetchOne(
                    "SELECT id FROM expense_categories WHERE name = ? AND id != ?",
                    [sanitize_string($data['name']), $id]
                );

                if ($duplicate) {
                    send_error('Category with this name already exists', null, 400);
                }
            }

            // Build update query dynamically
            $updates = [];
            $params = [];

            $fields = ['name', 'description', 'color', 'icon'];

            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = sanitize_string($data[$field]);
                }
            }

            if (isset($data['is_active'])) {
                $updates[] = "is_active = ?";
                $params[] = (int) $data['is_active'];
            }

            if (empty($updates)) {
                send_error('No fields to update', null, 400);
            }

            $params[] = $id;

            $sql = "UPDATE expense_categories SET " . implode(", ", $updates) . " WHERE id = ?";
            $db->execute($sql, $params);

            send_success(['id' => $id], 'Category updated successfully');
            break;

        case 'DELETE':
            // Delete category
            $data = get_json_input();

            if (!isset($data['id'])) {
                send_error('Category ID is required', null, 400);
            }

            $id = (int) $data['id'];

            // Check if category exists
            $existing = $db->fetchOne("SELECT id FROM expense_categories WHERE id = ?", [$id]);
            if (!$existing) {
                send_error('Category not found', null, 404);
            }

            // Check if category is being used
            $inUse = $db->fetchOne(
                "SELECT COUNT(*) as count FROM expenses WHERE category_id = ?",
                [$id]
            );

            if ($inUse['count'] > 0) {
                send_error(
                    'Cannot delete category that has expenses. Please reassign or delete expenses first.',
                    null,
                    400
                );
            }

            // Delete category
            $sql = "DELETE FROM expense_categories WHERE id = ?";
            $db->execute($sql, [$id]);

            send_success([], 'Category deleted successfully');
            break;

        default:
            send_error('Method not allowed', null, 405);
    }

} catch (Exception $e) {
    error_log("Categories API error: " . $e->getMessage());
    send_error('Operation failed', $e->getMessage(), 500);
}
?>
