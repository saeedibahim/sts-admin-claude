/**
 * Finance Module JavaScript
 * Handles finance-specific functionality (expenses, budgets, categories)
 */

// ==========================================
// API Helper Functions - FIXED PATHS
// ==========================================

/**
 * Fetch all categories
 */
async function fetchCategories() {
    try {
        const response = await fetch('../api/categories.php');
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        }
        return [];
    } catch (error) {
        console.error('Error fetching categories:', error);
        return [];
    }
}

/**
 * Fetch all budgets
 */
async function fetchBudgets(filters = {}) {
    try {
        const params = new URLSearchParams(filters);
        const response = await fetch(`../api/budgets.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        }
        return [];
    } catch (error) {
        console.error('Error fetching budgets:', error);
        return [];
    }
}

/**
 * Fetch all expenses
 */
async function fetchExpenses(filters = {}) {
    try {
        const params = new URLSearchParams(filters);
        const response = await fetch(`../api/expenses.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        }
        return [];
    } catch (error) {
        console.error('Error fetching expenses:', error);
        return [];
    }
}

/**
 * Fetch single expense
 */
async function fetchExpense(id) {
    try {
        const response = await fetch(`../api/expenses.php?action=get&id=${id}`);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        }
        return null;
    } catch (error) {
        console.error('Error fetching expense:', error);
        return null;
    }
}

/**
 * Create new expense
 */
async function createExpense(expenseData) {
    try {
        const response = await fetch('../api/expenses.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(expenseData)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to create expense');
        }
        
        showNotification('Expense created successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error creating expense:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Update expense
 */
async function updateExpense(id, expenseData) {
    try {
        const response = await fetch('../api/expenses.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, ...expenseData })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to update expense');
        }
        
        showNotification('Expense updated successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error updating expense:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Delete expense
 */
async function deleteExpense(id) {
    try {
        const response = await fetch('../api/expenses.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to delete expense');
        }
        
        showNotification('Expense deleted successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error deleting expense:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Create new budget
 */
async function createBudget(budgetData) {
    try {
        const response = await fetch('../api/budgets.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(budgetData)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to create budget');
        }
        
        showNotification('Budget created successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error creating budget:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Update budget
 */
async function updateBudget(id, budgetData) {
    try {
        const response = await fetch('../api/budgets.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, ...budgetData })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to update budget');
        }
        
        showNotification('Budget updated successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error updating budget:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Delete budget
 */
async function deleteBudget(id) {
    try {
        const response = await fetch('../api/budgets.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to delete budget');
        }
        
        showNotification('Budget deleted successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error deleting budget:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Create new category
 */
async function createCategory(categoryData) {
    try {
        const response = await fetch('../api/categories.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(categoryData)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to create category');
        }
        
        showNotification('Category created successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error creating category:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Update category
 */
async function updateCategory(id, categoryData) {
    try {
        const response = await fetch('../api/categories.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, ...categoryData })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to update category');
        }
        
        showNotification('Category updated successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error updating category:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

/**
 * Delete category
 */
async function deleteCategory(id) {
    try {
        const response = await fetch('../api/categories.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to delete category');
        }
        
        showNotification('Category deleted successfully', 'success');
        return result;
    } catch (error) {
        console.error('Error deleting category:', error);
        showNotification(error.message, 'error');
        return null;
    }
}

// ==========================================
// UI Helper Functions
// ==========================================

/**
 * Format currency
 */
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    const badges = {
        'Paid': '<span class="badge badge-status badge-completed">Paid</span>',
        'Pending': '<span class="badge badge-status badge-pending">Pending</span>',
        'Reimbursed': '<span class="badge badge-status badge-active">Reimbursed</span>',
        'Cancelled': '<span class="badge badge-status badge-blocked">Cancelled</span>'
    };
    return badges[status] || `<span class="badge badge-status">${status}</span>`;
}

/**
 * Get category badge HTML
 */
function getCategoryBadge(category) {
    if (!category || !category.name) return '-';
    
    const color = category.color || '#45C4B0';
    const icon = category.icon || 'tag';
    
    return `
        <span class="category-badge" style="background: ${color}20; color: ${color}; border: 1px solid ${color}40;">
            <i class="bi bi-${icon}"></i>
            ${category.name}
        </span>
    `;
}

/**
 * Show notification/toast
 */
function showNotification(message, type = 'info') {
    let toastContainer = document.getElementById('toastContainer');
    
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '11';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : 
                   type === 'error' ? 'bg-danger' : 
                   type === 'warning' ? 'bg-warning' : 'bg-info';
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

/**
 * Show loading spinner
 */
function showLoading(element) {
    element.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border spinner-border-accent" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
}

/**
 * Show empty state
 */
function showEmptyState(element, icon, title, message, actionButton = null) {
    let html = `
        <div class="finance-empty-state">
            <i class="bi bi-${icon}"></i>
            <h3>${title}</h3>
            <p>${message}</p>
    `;
    
    if (actionButton) {
        html += actionButton;
    }
    
    html += `</div>`;
    element.innerHTML = html;
}

/**
 * Confirm delete action
 */
function confirmDelete(itemName) {
    return confirm(`Are you sure you want to delete "${itemName}"?\n\nThis action cannot be undone.`);
}

/**
 * Get budget status info
 */
function getBudgetStatus(percentage, isExceeded) {
    if (isExceeded || percentage > 100) {
        return {
            class: 'exceeded',
            label: 'Exceeded',
            color: '#ff6b6b'
        };
    } else if (percentage >= 80) {
        return {
            class: 'warning',
            label: 'Warning',
            color: '#ffa500'
        };
    } else {
        return {
            class: 'on-track',
            label: 'On Track',
            color: '#45C4B0'
        };
    }
}

/**
 * Export to CSV
 */
function exportToCSV(data, filename = 'export.csv') {
    if (!data || data.length === 0) {
        showNotification('No data to export', 'warning');
        return;
    }
    
    const headers = Object.keys(data[0]);
    const rows = data.map(item => headers.map(header => item[header] || ''));
    
    let csv = headers.join(',') + '\n';
    rows.forEach(row => {
        csv += row.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Export successful', 'success');
}

console.log('Finance module loaded');