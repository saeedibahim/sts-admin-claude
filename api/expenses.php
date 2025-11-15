<?php
/**
 * Expenses API
 * CRUD operations for expenses
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    switch ($method) {
        case 'GET':
            // Get expense(s)
            if (isset($_GET['id'])) {
                // Get single expense
                $id = (int) $_GET['id'];
                $sql = "SELECT 
                            e.*,
                            ec.name as category_name,
                            ec.color as category_color,
                            ec.icon as category_icon,
                            c.client_name,
                            c.company_name,
                            u.full_name as created_by_name
                        FROM expenses e
                        LEFT JOIN expense_categories ec ON e.category_id = ec.id
                        LEFT JOIN clients c ON e.client_id = c.id
                        LEFT JOIN users u ON e.created_by = u.id
                        WHERE e.id = ?";
                
                $expense = $db->fetchOne($sql, [$id]);

                if (!$expense) {
                    send_error('Expense not found', null, 404);
                }

                send_success($expense, 'Expense retrieved successfully');
            } else {
                // Get all expenses with optional filters
                $where = [];
                $params = [];

                // Filter by category
                if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
                    $where[] = "e.category_id = ?";
                    $params[] = (int) $_GET['category_id'];
                }

                // Filter by client
                if (isset($_GET['client_id']) && $_GET['client_id'] !== '') {
                    $where[] = "e.client_id = ?";
                    $params[] = (int) $_GET['client_id'];
                }

                // Filter by status
                if (isset($_GET['status']) && $_GET['status'] !== '') {
                    $where[] = "e.status = ?";
                    $params[] = $_GET['status'];
                }

                // Filter by date range
                if (isset($_GET['start_date']) && $_GET['start_date'] !== '') {
                    $where[] = "e.expense_date >= ?";
                    $params[] = $_GET['start_date'];
                }

                if (isset($_GET['end_date']) && $_GET['end_date'] !== '') {
                    $where[] = "e.expense_date <= ?";
                    $params[] = $_GET['end_date'];
                }

                // Search functionality
                if (isset($_GET['search']) && $_GET['search'] !== '') {
                    $search = '%' . $_GET['search'] . '%';
                    $where[] = "(e.title LIKE ? OR e.vendor LIKE ? OR e.description LIKE ?)";
                    $params[] = $search;
                    $params[] = $search;
                    $params[] = $search;
                }

                // Build query
                $sql = "SELECT 
                            e.*,
                            ec.name as category_name,
                            ec.color as category_color,
                            ec.icon as category_icon,
                            c.client_name,
                            c.company_name,
                            u.full_name as created_by_name
                        FROM expenses e
                        LEFT JOIN expense_categories ec ON e.category_id = ec.id
                        LEFT JOIN clients c ON e.client_id = c.id
                        LEFT JOIN users u ON e.created_by = u.id";

                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }

                $sql .= " ORDER BY e.expense_date DESC, e.created_at DESC";

                $expenses = $db->fetchAll($sql, $params);

                send_success($expenses, 'Expenses retrieved successfully');
            }
            break;

        case 'POST':
            // Create new expense
            $data = get_json_input();

            // Validate required fields
            if (!validate_required($data['title'] ?? '')) {
                send_error('Expense title is required', null, 400);
            }

            if (!validate_required($data['amount'] ?? '')) {
                send_error('Amount is required', null, 400);
            }

            if (!validate_required($data['expense_date'] ?? '')) {
                send_error('Expense date is required', null, 400);
            }

            if (!validate_required($data['category_id'] ?? '')) {
                send_error('Category is required', null, 400);
            }

            // Validate amount is numeric
            if (!is_numeric($data['amount'])) {
                send_error('Amount must be a valid number', null, 400);
            }

            // Handle file upload if present
            $receiptFile = null;
            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $receiptFile = handleReceiptUpload($_FILES['receipt']);
            }

            // Insert expense
            $sql = "INSERT INTO expenses (
                        category_id, client_id, title, description, amount,
                        expense_date, payment_method, receipt_file, vendor,
                        reference_number, status, notes, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $db->execute($sql, [
                (int) $data['category_id'],
                !empty($data['client_id']) ? (int) $data['client_id'] : null,
                sanitize_string($data['title']),
                sanitize_string($data['description'] ?? ''),
                (float) $data['amount'],
                $data['expense_date'],
                $data['payment_method'] ?? 'Bank Transfer',
                $receiptFile ?? ($data['receipt_file'] ?? null),
                sanitize_string($data['vendor'] ?? ''),
                sanitize_string($data['reference_number'] ?? ''),
                $data['status'] ?? 'Paid',
                sanitize_string($data['notes'] ?? ''),
                $_SESSION['user_id'] ?? 1
            ]);

            $expenseId = $db->lastInsertId();

            send_success(['id' => $expenseId], 'Expense created successfully', 201);
            break;

        case 'PUT':
            // Update expense
            $data = get_json_input();

            if (!isset($data['id'])) {
                send_error('Expense ID is required', null, 400);
            }

            $id = (int) $data['id'];

            // Check if expense exists
            $existing = $db->fetchOne("SELECT id FROM expenses WHERE id = ?", [$id]);
            if (!$existing) {
                send_error('Expense not found', null, 404);
            }

            // Validate amount if provided
            if (isset($data['amount']) && !is_numeric($data['amount'])) {
                send_error('Amount must be a valid number', null, 400);
            }

            // Build update query dynamically
            $updates = [];
            $params = [];

            $fields = [
                'category_id', 'client_id', 'title', 'description', 'amount',
                'expense_date', 'payment_method', 'receipt_file', 'vendor',
                'reference_number', 'status', 'notes'
            ];

            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    
                    // Handle different data types
                    if ($field === 'category_id' || $field === 'client_id') {
                        $params[] = !empty($data[$field]) ? (int) $data[$field] : null;
                    } elseif ($field === 'amount') {
                        $params[] = (float) $data[$field];
                    } else {
                        $params[] = sanitize_string($data[$field]);
                    }
                }
            }

            if (empty($updates)) {
                send_error('No fields to update', null, 400);
            }

            $params[] = $id;

            $sql = "UPDATE expenses SET " . implode(", ", $updates) . " WHERE id = ?";
            $db->execute($sql, $params);

            send_success(['id' => $id], 'Expense updated successfully');
            break;

        case 'DELETE':
            // Delete expense
            $data = get_json_input();

            if (!isset($data['id'])) {
                send_error('Expense ID is required', null, 400);
            }

            $id = (int) $data['id'];

            // Check if expense exists and get receipt file
            $existing = $db->fetchOne("SELECT receipt_file FROM expenses WHERE id = ?", [$id]);
            if (!$existing) {
                send_error('Expense not found', null, 404);
            }

            // Delete from database
            $sql = "DELETE FROM expenses WHERE id = ?";
            $db->execute($sql, [$id]);

            // Delete receipt file if exists
            if ($existing['receipt_file']) {
                $filePath = __DIR__ . '/../uploads/receipts/' . $existing['receipt_file'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            send_success([], 'Expense deleted successfully');
            break;

        default:
            send_error('Method not allowed', null, 405);
    }

} catch (Exception $e) {
    error_log("Expenses API error: " . $e->getMessage());
    send_error('Operation failed', $e->getMessage(), 500);
}

/**
 * Handle receipt file upload
 */
function handleReceiptUpload($file) {
    $uploadDir = __DIR__ . '/../uploads/receipts/';
    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        send_error('Invalid file type. Only JPG, PNG, GIF, and PDF allowed.', null, 400);
    }
    
    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        send_error('File size too large. Maximum 5MB allowed.', null, 400);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'receipt_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    } else {
        send_error('Failed to upload receipt file', null, 500);
    }
}
?>