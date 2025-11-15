<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview - STS Dashboard</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
</head>
<body>
    <?php require_once 'includes/auth_check.php'; ?>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="bi bi-layers-fill"></i>
                    <h3>STS Dashboard</h3>
                </div>
            </div>

            <div class="sidebar-user">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-avatar" id="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="sidebar-user-details">
                        <h6 id="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></h6>
                        <p id="user-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?></p>
                    </div>
                </div>
            </div>


            <!-- SideBar Navigation Menu -->
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="finance/">
                            <i class="bi bi-cash-stack"></i>
                            <span>Finance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clients.html">
                            <i class="bi bi-people-fill"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacts.html">
                            <i class="bi bi-envelope-fill"></i>
                            <span>Contacts</span>
                            <span class="badge bg-accent" id="new-contacts-badge">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tasks.html">
                            <i class="bi bi-check2-square"></i>
                            <span>Tasks</span>
                            <span class="badge bg-info" id="active-tasks-badge">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="files.html">
                            <i class="bi bi-folder-fill"></i>
                            <span>Files</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="documents.html">
                            <i class="bi bi-file-text-fill"></i>
                            <span>Documents</span>
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Sidebar overlay for mobile -->
        <div class="sidebar-overlay"></div>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div class="top-bar-left">
                    <button class="menu-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title">Dashboard Overview</h1>
                </div>
                <div class="top-bar-right">
                    <span class="text-muted" id="current-date"></span>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Row 1: Clients and Projects -->
                <h5 class="text-muted mb-3">Clients & Projects</h5>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Total Clients</div>
                        <h2 class="stat-value" id="total-clients">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Active Projects</div>
                        <h2 class="stat-value" id="active-projects">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Completed Projects</div>
                        <h2 class="stat-value" id="completed-projects">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">New Contact Forms</div>
                        <h2 class="stat-value" id="new-contacts">0</h2>
                    </div>
                </div>

                <!-- NEW: Finance Overview Section -->
                <h5 class="text-muted mb-3 mt-4">
                    Finance Overview
                    <a href="finance/" class="btn btn-sm btn-outline-accent float-end">View Details</a>
                </h5>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                        <div class="stat-label">This Month Expenses</div>
                        <h2 class="stat-value" id="month-expenses">$0.00</h2>
                        <div class="stat-change" id="expense-change">
                            <i class="bi bi-arrow-up"></i> 0%
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                        </div>
                        <div class="stat-label">Total Transactions</div>
                        <h2 class="stat-value" id="total-expenses">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-piggy-bank"></i>
                            </div>
                        </div>
                        <div class="stat-label">Active Budgets</div>
                        <h2 class="stat-value" id="active-budgets">0</h2>
                        <div class="stat-change" id="budget-alerts">
                            <span class="text-success">All on track</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                        </div>
                        <div class="stat-label">Avg Expense</div>
                        <h2 class="stat-value" id="avg-expense">$0.00</h2>
                    </div>
                </div>

                <!-- Row 2: Tasks -->
                <h5 class="text-muted mb-3 mt-4">Task Management</h5>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-list-check"></i>
                            </div>
                        </div>
                        <div class="stat-label">Total Tasks</div>
                        <h2 class="stat-value" id="total-tasks">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                        <div class="stat-label">In Progress</div>
                        <h2 class="stat-value" id="in-progress-tasks">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-check-all"></i>
                            </div>
                        </div>
                        <div class="stat-label">Completed This Week</div>
                        <h2 class="stat-value" id="completed-this-week">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Overdue Tasks</div>
                        <h2 class="stat-value text-danger" id="overdue-tasks">0</h2>
                    </div>
                </div>

                <!-- Row 3: Files, Documents, and Assignment -->
                <h5 class="text-muted mb-3 mt-4">Resources & Assignments</h5>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Total Files</div>
                        <h2 class="stat-value" id="total-files">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-file-text-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Total Documents</div>
                        <h2 class="stat-value" id="total-documents">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                        </div>
                        <div class="stat-label">Your Tasks</div>
                        <h2 class="stat-value" id="your-tasks">0</h2>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                        <div class="stat-label">Partner Tasks</div>
                        <h2 class="stat-value" id="partner-tasks">0</h2>
                    </div>
                </div>

                <!-- Recent Activity -->
                <h5 class="text-muted mb-3 mt-4">Recent Activity</h5>
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">Recent Tasks</h5>
                        <a href="tasks.html" class="btn btn-sm btn-outline-accent">View All</a>
                    </div>
                    <div id="recent-tasks-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-accent" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="js/api.js"></script>
    <script src="js/ui.js"></script>
    <script>
        // Load dashboard data
        document.addEventListener('DOMContentLoaded', async () => {
            // Display current date
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Load user info from session (if available via PHP inline)
            const userName = '<?php echo isset($_SESSION["full_name"]) ? $_SESSION["full_name"] : "User"; ?>';
            const userRole = '<?php echo isset($_SESSION["role"]) ? $_SESSION["role"] : "Admin"; ?>';

            if (userName && userName !== '') {
                document.getElementById('user-name').textContent = userName;
                document.getElementById('user-role').textContent = userRole;
                const initials = userName.split(' ').map(n => n[0]).join('').substring(0, 2);
                document.getElementById('user-avatar').textContent = initials;
            }

            // Load statistics
            await loadStats();
            
            // Load finance stats
            await loadFinanceStats();
        });

        async function loadStats() {
            try {
                const response = await API.getStats();

                if (response.success) {
                    const stats = response.data;

                    // Row 1
                    document.getElementById('total-clients').textContent = stats.row1.total_clients;
                    document.getElementById('active-projects').textContent = stats.row1.active_projects;
                    document.getElementById('completed-projects').textContent = stats.row1.completed_projects;
                    document.getElementById('new-contacts').textContent = stats.row1.new_contacts;

                    // Row 2
                    document.getElementById('total-tasks').textContent = stats.row2.total_tasks;
                    document.getElementById('in-progress-tasks').textContent = stats.row2.in_progress_tasks;
                    document.getElementById('completed-this-week').textContent = stats.row2.completed_this_week;
                    document.getElementById('overdue-tasks').textContent = stats.row2.overdue_tasks;

                    // Row 3
                    document.getElementById('total-files').textContent = stats.row3.total_files;
                    document.getElementById('total-documents').textContent = stats.row3.total_documents;
                    document.getElementById('your-tasks').textContent = stats.row3.your_tasks;
                    document.getElementById('partner-tasks').textContent = stats.row3.partner_tasks;

                    // Update sidebar badges
                    document.getElementById('new-contacts-badge').textContent = stats.row1.new_contacts;
                    document.getElementById('active-tasks-badge').textContent = stats.row2.in_progress_tasks;

                    // Display recent tasks
                    displayRecentTasks(stats.recent_tasks);
                } else {
                    UI.error('Failed to load statistics');
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                UI.error('An error occurred while loading dashboard data');
            }
        }

        // NEW: Load finance statistics
        // Load finance statistics
        async function loadFinanceStats() {
            try {
                const now = new Date();
                const year = now.getFullYear();
                const month = now.getMonth() + 1;
            
                const response = await fetch(`/api/finance-stats.php?action=overview&year=${year}&month=${month}`);
                
                // Check if response is OK
                if (!response.ok) {
                    console.warn('Finance stats API not available');
                    return;
                }
            
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.warn('Finance stats API returned non-JSON response');
                    return;
                }
            
                const result = await response.json();
            
                if (result.success) {
                    const data = result.data;
                
                    // Update finance cards
                    document.getElementById('month-expenses').textContent = 
                        '$' + parseFloat(data.totals.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    document.getElementById('total-expenses').textContent = data.totals.total_expenses;
                    
                    document.getElementById('avg-expense').textContent = 
                        '$' + parseFloat(data.totals.average_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    document.getElementById('active-budgets').textContent = data.budgets.total_budgets;
                
                    // Month-over-month change
                    const changeEl = document.getElementById('expense-change');
                    const change = data.comparison.percentage_change;
                    if (change > 0) {
                        changeEl.className = 'stat-change negative';
                        changeEl.innerHTML = `<i class="bi bi-arrow-up"></i> +${change.toFixed(1)}%`;
                    } else if (change < 0) {
                        changeEl.className = 'stat-change positive';
                        changeEl.innerHTML = `<i class="bi bi-arrow-down"></i> ${Math.abs(change).toFixed(1)}%`;
                    } else {
                        changeEl.className = 'stat-change';
                        changeEl.innerHTML = `<i class="bi bi-dash"></i> No change`;
                    }
                
                    // Budget alerts
                    const budgetAlertsEl = document.getElementById('budget-alerts');
                    if (data.budgets.exceeded_count > 0) {
                        budgetAlertsEl.innerHTML = `<span class="text-danger">${data.budgets.exceeded_count} exceeded</span>`;
                    } else {
                        budgetAlertsEl.innerHTML = `<span class="text-success">All on track</span>`;
                    }
                }
            } catch (error) {
                console.error('Error loading finance stats:', error);
                // Don't show error to user, just fail silently
                // Finance section will show default values (0)
            }
        }

        function displayRecentTasks(tasks) {
            const container = document.getElementById('recent-tasks-container');

            if (!tasks || tasks.length === 0) {
                container.innerHTML = UI.createEmptyState(
                    'list-task',
                    'No recent tasks',
                    'Create your first task to see it here',
                    'Create Task',
                    "window.location.href='tasks.html'"
                );
                return;
            }

            const tasksHTML = tasks.map(task => `
                <div class="d-flex align-items-start border-bottom border-secondary pb-3 mb-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${UI.escapeHtml(task.title)}</h6>
                        <p class="text-muted small mb-2">
                            ${task.client_name ? `<i class="bi bi-building"></i> ${UI.escapeHtml(task.client_name)} | ` : ''}
                            ${task.assigned_to_name ? `<i class="bi bi-person"></i> ${UI.escapeHtml(task.assigned_to_name)}` : 'Unassigned'}
                        </p>
                        <div>
                            ${UI.getStatusBadge(task.status)}
                            ${UI.getPriorityBadge(task.priority)}
                        </div>
                    </div>
                    <div class="text-end text-muted small">
                        ${task.due_date ? UI.formatDate(task.due_date) : 'No deadline'}
                    </div>
                </div>
            `).join('');

            container.innerHTML = tasksHTML;
        }
    </script>
</body>
</html>