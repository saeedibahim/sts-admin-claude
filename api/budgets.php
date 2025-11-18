<?php
/**
 * Budgets API
 * CRUD operations for budgets
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    switch ($method) {
        case 'GET':
            getBudgets($db);
            break;

        case 'POST':
            createBudget($db);
            break;

        case 'PUT':
            updateBudget($db);
            break;

        case 'DELETE':
            deleteBudget($db);
            break;

        default:
            send_error('Method not allowed', null, 405);
    }

} catch (Exception $e) {
    error_log("Budgets API error: " . $e->getMessage());
    send_error('Operation failed', $e->getMessage(), 500);
}

function getBudgets($db) {
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $sql = "SELECT 
                    b.*,
                    ec.name as category_name,
                    ec.color as category_color,
                    c.client_name,
                    u.full_name as created_by_name
                FROM budgets b
                LEFT JOIN expense_categories ec ON b.category_id = ec.id
                LEFT JOIN clients c ON b.client_id = c.id
                LEFT JOIN users u ON b.created_by = u.id
                WHERE b.id = ?";
        
        $budget = $db->fetchOne($sql, [$id]);

        if (!$budget) {
            send_error('Budget not found', null, 404);
        }

        // Calculate spent amount
        $spentSql = "SELECT COALESCE(SUM(amount), 0) as spent
                     FROM expenses
                     WHERE expense_date BETWEEN ? AND ?
                     AND status != 'Cancelled'";
        
        $params = [$budget['period_start'], $budget['period_end']];
        
        if ($budget['category_id']) {
            $spentSql .= " AND category_id = ?";
            $params[] = $budget['category_id'];
        }
        
        if ($budget['client_id']) {
            $spentSql .= " AND client_id = ?";
            $params[] = $budget['client_id'];
        }

        $spent = $db->fetchOne($spentSql, $params);
        $budget['spent_amount'] = (float) $spent['spent'];
        $budget['remaining_amount'] = (float) $budget['amount'] - (float) $spent['spent'];
        
        send_success($budget, 'Budget retrieved successfully');
    } else {
        // Get all budgets
        $sql = "SELECT 
                    b.*,
                    ec.name as category_name,
                    ec.color as category_color,
                    c.client_name,
                    u.full_name as created_by_name
                FROM budgets b
                LEFT JOIN expense_categories ec ON b.category_id = ec.id
                LEFT JOIN clients c ON b.client_id = c.id
                LEFT JOIN users u ON b.created_by = u.id
                ORDER BY b.period_start DESC";

        $budgets = $db->fetchAll($sql);

        // Calculate spent for each budget
        foreach ($budgets as &$budget) {
            $spentSql = "SELECT COALESCE(SUM(amount), 0) as spent
                         FROM expenses
                         WHERE expense_date BETWEEN ? AND ?
                         AND status != 'Cancelled'";
            
            $params = [$budget['period_start'], $budget['period_end']];
            
            if ($budget['category_id']) {
                $spentSql .= " AND category_id = ?";
                $params[] = $budget['category_id'];
            }
            
            if ($budget['client_id']) {
                $spentSql .= " AND client_id = ?";
                $params[] = $budget['client_id'];
            }

            $spent = $db->fetchOne($spentSql, $params);
            $budget['spent_amount'] = (float) $spent['spent'];
            $budget['remaining_amount'] = (float) $budget['amount'] - (float) $spent['spent'];
            $budget['spent_percentage'] = $budget['amount'] > 0 ? 
                round(($budget['spent_amount'] / $budget['amount']) * 100, 2) : 0;
        }

        send_success($budgets, 'Budgets retrieved successfully');
    }
}

function createBudget($db) {
    $input = get_json_input();
    
    if (!isset($input['name']) || !isset($input['amount'])) {
        send_error('Name and amount are required', null, 400);
    }
    
    if (!isset($input['period_start']) || !isset($input['period_end'])) {
        send_error('Period start and end dates are required', null, 400);
    }
    
    $sql = "INSERT INTO budgets (
                name, budget_type, category_id, client_id, amount,
                period_start, period_end, alert_threshold, alert_enabled,
                status, notes, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $input['name'],
        $input['budget_type'] ?? 'Monthly',
        !empty($input['category_id']) ? (int) $input['category_id'] : null,
        !empty($input['client_id']) ? (int) $input['client_id'] : null,
        (float) $input['amount'],
        $input['period_start'],
        $input['period_end'],
        isset($input['alert_threshold']) ? (int) $input['alert_threshold'] : 80,
        isset($input['alert_enabled']) ? (int) $input['alert_enabled'] : 1,
        $input['status'] ?? 'Active',
        $input['notes'] ?? '',
        1 // Default user ID
    ];
    
    $result = $db->execute($sql, $params);
    
    if ($result) {
        $budgetId = $db->lastInsertId();
        send_success(['id' => $budgetId], 'Budget created successfully', 201);
    } else {
        send_error('Failed to create budget', null, 500);
    }
}

function updateBudget($db) {
    $input = get_json_input();
    
    if (!isset($input['id'])) {
        send_error('Budget ID is required', null, 400);
    }
    
    $updates = [];
    $params = [];
    
    $fields = ['name', 'budget_type', 'category_id', 'client_id', 'amount',
               'period_start', 'period_end', 'alert_threshold', 'alert_enabled',
               'status', 'notes'];
    
    foreach ($fields as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = ?";
            
            if ($field === 'amount') {
                $params[] = (float) $input[$field];
            } elseif (in_array($field, ['category_id', 'client_id', 'alert_threshold', 'alert_enabled'])) {
                $params[] = !empty($input[$field]) ? (int) $input[$field] : null;
            } else {
                $params[] = $input[$field];
            }
        }
    }
    
    if (empty($updates)) {
        send_error('No fields to update', null, 400);
    }
    
    $params[] = $input['id'];
    
    $sql = "UPDATE budgets SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = ?";
    $result = $db->execute($sql, $params);
    
    if ($result !== false) {
        send_success(['affected_rows' => $result], 'Budget updated successfully');
    } else {
        send_error('Failed to update budget', null, 500);
    }
}

function deleteBudget($db) {
    $input = get_json_input();
    
    if (!isset($input['id'])) {
        send_error('Budget ID is required', null, 400);
    }
    
    $sql = "DELETE FROM budgets WHERE id = ?";
    $result = $db->execute($sql, [$input['id']]);
    
    if ($result) {
        send_success(['affected_rows' => $result], 'Budget deleted successfully');
    } else {
        send_error('Failed to delete budget', null, 500);
    }
}
?>