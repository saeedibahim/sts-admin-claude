<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Finance Statistics API
 * Dashboard statistics, charts, and analytics for finance module
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    if ($method !== 'GET') {
        send_error('Method not allowed', null, 405);
    }

    $action = $_GET['action'] ?? 'overview';

    switch ($action) {
        case 'overview':
            getFinanceOverview($db);
            break;

        case 'expense-trends':
            getExpenseTrends($db);
            break;

        case 'category-breakdown':
            getCategoryBreakdown($db);
            break;

        case 'budget-performance':
            getBudgetPerformance($db);
            break;

        case 'top-expenses':
            getTopExpenses($db);
            break;

        case 'monthly-comparison':
            getMonthlyComparison($db);
            break;

        case 'payment-methods':
            getPaymentMethodStats($db);
            break;

        case 'client-spending':
            getClientSpending($db);
            break;

        default:
            send_error('Invalid action', null, 400);
    }

} catch (Exception $e) {
    error_log("Finance Stats API error: " . $e->getMessage());
    send_error('Operation failed', $e->getMessage(), 500);
}

/**
 * Get overall finance overview/dashboard
 */
function getFinanceOverview($db) {
    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? date('m');

    // Total expenses stats
    $totalSql = "SELECT 
                    COUNT(*) as total_expenses,
                    COALESCE(SUM(amount), 0) as total_amount,
                    COALESCE(AVG(amount), 0) as average_amount,
                    COALESCE(MAX(amount), 0) as highest_expense,
                    COALESCE(MIN(amount), 0) as lowest_expense
                FROM expenses
                WHERE status != 'Cancelled'
                AND YEAR(expense_date) = ?
                AND MONTH(expense_date) = ?";

    $totals = $db->fetchOne($totalSql, [$year, $month]);

    // If no data, return defaults
    if (!$totals || $totals['total_expenses'] == 0) {
        send_success([
            'period' => [
                'year' => (int) $year,
                'month' => (int) $month,
                'month_name' => date('F Y', strtotime("$year-$month-01"))
            ],
            'totals' => [
                'total_expenses' => 0,
                'total_amount' => 0,
                'average_amount' => 0,
                'highest_expense' => 0,
                'lowest_expense' => 0
            ],
            'by_status' => [],
            'budgets' => [
                'total_budgets' => 0,
                'total_budget' => 0,
                'exceeded_count' => 0
            ],
            'comparison' => [
                'current_month' => 0,
                'previous_month' => 0,
                'change' => 0,
                'percentage_change' => 0
            ],
            'recent_expenses' => []
        ], 'Finance overview retrieved successfully');
        return;
    }

    // Expenses by status
    $statusSql = "SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(amount) as total
                FROM expenses
                WHERE YEAR(expense_date) = ?
                AND MONTH(expense_date) = ?
                GROUP BY status";

    $byStatus = $db->fetchAll($statusSql, [$year, $month]);

    // Active budgets summary
    $budgetSql = "SELECT 
                    COUNT(*) as total_budgets,
                    COALESCE(SUM(amount), 0) as total_budget,
                    SUM(CASE WHEN status = 'Exceeded' THEN 1 ELSE 0 END) as exceeded_count
                FROM budgets
                WHERE status = 'Active'
                AND period_start <= CURDATE()
                AND period_end >= CURDATE()";

    $budgets = $db->fetchOne($budgetSql);

    // Recent expenses (last 5)
    $recentSql = "SELECT 
                    e.*,
                    ec.name as category_name,
                    ec.color as category_color,
                    ec.icon as category_icon
                FROM expenses e
                LEFT JOIN expense_categories ec ON e.category_id = ec.id
                WHERE YEAR(e.expense_date) = ?
                AND MONTH(e.expense_date) = ?
                ORDER BY e.expense_date DESC, e.created_at DESC
                LIMIT 5";

    $recentExpenses = $db->fetchAll($recentSql, [$year, $month]);

    // Month-over-month comparison
    $lastMonth = date('Y-m', strtotime("$year-$month-01 -1 month"));
    list($lastYear, $lastMonthNum) = explode('-', $lastMonth);

    $lastMonthSql = "SELECT COALESCE(SUM(amount), 0) as total
                    FROM expenses
                    WHERE status != 'Cancelled'
                    AND YEAR(expense_date) = ?
                    AND MONTH(expense_date) = ?";

    $lastMonthTotal = $db->fetchOne($lastMonthSql, [$lastYear, $lastMonthNum]);

    $currentTotal = (float) $totals['total_amount'];
    $previousTotal = (float) $lastMonthTotal['total'];
    $percentageChange = 0;

    if ($previousTotal > 0) {
        $percentageChange = round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2);
    }

    send_success([
        'period' => [
            'year' => (int) $year,
            'month' => (int) $month,
            'month_name' => date('F Y', strtotime("$year-$month-01"))
        ],
        'totals' => [
            'total_expenses' => (int) $totals['total_expenses'],
            'total_amount' => (float) $totals['total_amount'],
            'average_amount' => round((float) $totals['average_amount'], 2),
            'highest_expense' => (float) $totals['highest_expense'],
            'lowest_expense' => (float) $totals['lowest_expense']
        ],
        'by_status' => $byStatus,
        'budgets' => [
            'total_budgets' => (int) ($budgets['total_budgets'] ?? 0),
            'total_budget' => (float) ($budgets['total_budget'] ?? 0),
            'exceeded_count' => (int) ($budgets['exceeded_count'] ?? 0)
        ],
        'comparison' => [
            'current_month' => $currentTotal,
            'previous_month' => $previousTotal,
            'change' => $currentTotal - $previousTotal,
            'percentage_change' => $percentageChange
        ],
        'recent_expenses' => $recentExpenses
    ], 'Finance overview retrieved successfully');
}

/**
 * Get expense trends over time
 */
function getExpenseTrends($db) {
    $period = $_GET['period'] ?? 'year';
    
    $monthsBack = 12;
    if ($period === '6months') $monthsBack = 6;
    if ($period === '3months') $monthsBack = 3;

    $sql = "SELECT 
                DATE_FORMAT(expense_date, '%Y-%m') as month,
                DATE_FORMAT(expense_date, '%b %Y') as month_label,
                COUNT(*) as expense_count,
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(AVG(amount), 0) as average_amount
            FROM expenses
            WHERE status != 'Cancelled'
            AND expense_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
            ORDER BY month ASC";

    $trends = $db->fetchAll($sql, [$monthsBack]);

    send_success([
        'period' => $period,
        'months_back' => $monthsBack,
        'data' => $trends
    ], 'Expense trends retrieved successfully');
}

/**
 * Get category breakdown/distribution
 */
function getCategoryBreakdown($db) {
    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? date('m');

    $sql = "SELECT 
                ec.id,
                ec.name,
                ec.color,
                ec.icon,
                COUNT(e.id) as expense_count,
                COALESCE(SUM(e.amount), 0) as total_amount
            FROM expense_categories ec
            LEFT JOIN expenses e ON ec.id = e.category_id 
                AND e.status != 'Cancelled'
                AND YEAR(e.expense_date) = ?
                AND MONTH(e.expense_date) = ?
            WHERE ec.is_active = 1
            GROUP BY ec.id
            HAVING expense_count > 0
            ORDER BY total_amount DESC";

    $breakdown = $db->fetchAll($sql, [$year, $month]);

    // Calculate percentages
    $total = array_sum(array_column($breakdown, 'total_amount'));
    foreach ($breakdown as &$cat) {
        $cat['percentage'] = $total > 0 ? round(($cat['total_amount'] / $total) * 100, 2) : 0;
    }

    send_success([
        'period' => [
            'year' => (int) $year,
            'month' => (int) $month
        ],
        'categories' => $breakdown
    ], 'Category breakdown retrieved successfully');
}

/**
 * Get budget performance
 */
function getBudgetPerformance($db) {
    $sql = "SELECT 
                b.*,
                ec.name as category_name,
                ec.color as category_color,
                c.client_name
            FROM budgets b
            LEFT JOIN expense_categories ec ON b.category_id = ec.id
            LEFT JOIN clients c ON b.client_id = c.id
            WHERE b.status = 'Active'
            AND b.period_start <= CURDATE()
            AND b.period_end >= CURDATE()
            ORDER BY b.budget_type, b.name";

    $budgets = $db->fetchAll($sql);

    foreach ($budgets as &$budget) {
        $where = ["e.expense_date BETWEEN ? AND ?", "e.status != 'Cancelled'"];
        $params = [$budget['period_start'], $budget['period_end']];

        if ($budget['category_id']) {
            $where[] = "e.category_id = ?";
            $params[] = $budget['category_id'];
        }

        if ($budget['client_id']) {
            $where[] = "e.client_id = ?";
            $params[] = $budget['client_id'];
        }

        $spendingSql = "SELECT COALESCE(SUM(e.amount), 0) as spent
                       FROM expenses e
                       WHERE " . implode(" AND ", $where);

        $spending = $db->fetchOne($spendingSql, $params);
        $spent = (float) $spending['spent'];
        $budgetAmount = (float) $budget['amount'];

        $budget['spent_amount'] = $spent;
        $budget['remaining_amount'] = $budgetAmount - $spent;
        $budget['spent_percentage'] = $budgetAmount > 0 ? round(($spent / $budgetAmount) * 100, 2) : 0;
        $budget['is_exceeded'] = $spent > $budgetAmount;
        $budget['is_warning'] = $budget['spent_percentage'] >= $budget['alert_threshold'];
    }

    send_success($budgets, 'Budget performance retrieved successfully');
}

// Placeholder functions for other endpoints
function getTopExpenses($db) {
    send_success([], 'Top expenses - not implemented yet');
}

function getMonthlyComparison($db) {
    send_success([], 'Monthly comparison - not implemented yet');
}

function getPaymentMethodStats($db) {
    send_success([], 'Payment methods - not implemented yet');
}

function getClientSpending($db) {
    send_success([], 'Client spending - not implemented yet');
}
?>
