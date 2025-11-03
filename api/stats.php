<?php
/**
 * Statistics API
 * Provides dashboard statistics
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $currentUserId = $_SESSION['user_id'];

    // Get all user IDs for partner assignment check
    $users = $db->fetchAll("SELECT id, full_name FROM users WHERE is_active = 1");
    $userIds = array_column($users, 'id');
    $partnerIds = array_diff($userIds, [$currentUserId]);

    // ==========================================
    // Row 1: Clients and Projects
    // ==========================================

    // Total Clients
    $totalClients = $db->fetchOne("SELECT COUNT(*) as count FROM clients")['count'];

    // Active Projects
    $activeProjects = $db->fetchOne("SELECT COUNT(*) as count FROM clients WHERE project_status = 'Active'")['count'];

    // Completed Projects
    $completedProjects = $db->fetchOne("SELECT COUNT(*) as count FROM clients WHERE project_status = 'Completed'")['count'];

    // New Contact Forms
    $newContacts = $db->fetchOne("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'New'")['count'];

    // ==========================================
    // Row 2: Tasks
    // ==========================================

    // Total Tasks
    $totalTasks = $db->fetchOne("SELECT COUNT(*) as count FROM tasks")['count'];

    // In Progress Tasks
    $inProgressTasks = $db->fetchOne("SELECT COUNT(*) as count FROM tasks WHERE status = 'In Progress'")['count'];

    // Completed This Week
    $completedThisWeek = $db->fetchOne("
        SELECT COUNT(*) as count FROM tasks
        WHERE status = 'Completed'
        AND completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ")['count'];

    // Overdue Tasks
    $overdueTasks = $db->fetchOne("
        SELECT COUNT(*) as count FROM tasks
        WHERE due_date < CURDATE()
        AND status != 'Completed'
    ")['count'];

    // ==========================================
    // Row 3: Files, Documents, and User Tasks
    // ==========================================

    // Total Files
    $totalFiles = $db->fetchOne("SELECT COUNT(*) as count FROM files")['count'];

    // Total Documents
    $totalDocuments = $db->fetchOne("SELECT COUNT(*) as count FROM documents")['count'];

    // Your Tasks (assigned to current user)
    $yourTasks = $db->fetchOne("
        SELECT COUNT(*) as count FROM tasks
        WHERE assigned_to = ?
        AND status != 'Completed'
    ", [$currentUserId])['count'];

    // Partner Tasks (assigned to other users)
    $partnerTasks = 0;
    if (!empty($partnerIds)) {
        $placeholders = implode(',', array_fill(0, count($partnerIds), '?'));
        $partnerTasks = $db->fetchOne("
            SELECT COUNT(*) as count FROM tasks
            WHERE assigned_to IN ($placeholders)
            AND status != 'Completed'
        ", $partnerIds)['count'];
    }

    // ==========================================
    // Additional stats for charts/graphs (optional)
    // ==========================================

    // Tasks by status
    $tasksByStatus = $db->fetchAll("
        SELECT status, COUNT(*) as count
        FROM tasks
        GROUP BY status
    ");

    // Tasks by priority
    $tasksByPriority = $db->fetchAll("
        SELECT priority, COUNT(*) as count
        FROM tasks
        GROUP BY priority
    ");

    // Recent activities (last 5 tasks)
    $recentTasks = $db->fetchAll("
        SELECT t.*, c.client_name, u.full_name as assigned_to_name
        FROM tasks t
        LEFT JOIN clients c ON t.client_id = c.id
        LEFT JOIN users u ON t.assigned_to = u.id
        ORDER BY t.created_at DESC
        LIMIT 5
    ");

    // Prepare response
    $stats = [
        'row1' => [
            'total_clients' => (int) $totalClients,
            'active_projects' => (int) $activeProjects,
            'completed_projects' => (int) $completedProjects,
            'new_contacts' => (int) $newContacts
        ],
        'row2' => [
            'total_tasks' => (int) $totalTasks,
            'in_progress_tasks' => (int) $inProgressTasks,
            'completed_this_week' => (int) $completedThisWeek,
            'overdue_tasks' => (int) $overdueTasks
        ],
        'row3' => [
            'total_files' => (int) $totalFiles,
            'total_documents' => (int) $totalDocuments,
            'your_tasks' => (int) $yourTasks,
            'partner_tasks' => (int) $partnerTasks
        ],
        'charts' => [
            'tasks_by_status' => $tasksByStatus,
            'tasks_by_priority' => $tasksByPriority
        ],
        'recent_tasks' => $recentTasks
    ];

    send_success($stats, 'Statistics retrieved successfully');

} catch (Exception $e) {
    error_log("Stats API error: " . $e->getMessage());
    send_error('Failed to retrieve statistics', $e->getMessage(), 500);
}
