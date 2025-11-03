/**
 * UI Utilities
 * Toast notifications, modals, loading states, and other UI helpers
 */

const UI = {
    /**
     * Show toast notification
     */
    showToast(message, type = 'info', duration = 3000) {
        // Create toast container if doesn't exist
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        // Map type to Bootstrap classes
        const typeMap = {
            success: 'bg-success text-white',
            error: 'bg-danger text-white',
            warning: 'bg-warning text-dark',
            info: 'bg-info text-white'
        };

        const toastClass = typeMap[type] || typeMap.info;

        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div id="${toastId}" class="toast ${toastClass}" role="alert">
                <div class="d-flex align-items-center p-2">
                    <div class="toast-body flex-grow-1">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: duration
        });

        toast.show();

        // Remove from DOM after hide
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    },

    /**
     * Show success toast
     */
    success(message, duration) {
        this.showToast(message, 'success', duration);
    },

    /**
     * Show error toast
     */
    error(message, duration) {
        this.showToast(message, 'error', duration);
    },

    /**
     * Show warning toast
     */
    warning(message, duration) {
        this.showToast(message, 'warning', duration);
    },

    /**
     * Show info toast
     */
    info(message, duration) {
        this.showToast(message, 'info', duration);
    },

    /**
     * Show confirmation dialog
     */
    async confirm(title, message) {
        return new Promise((resolve) => {
            // Create modal HTML
            const modalId = 'confirm-modal-' + Date.now();
            const modalHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>${message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-accent confirm-btn">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const modalElement = document.getElementById(modalId);
            const modal = new bootstrap.Modal(modalElement);

            // Handle confirm
            modalElement.querySelector('.confirm-btn').addEventListener('click', () => {
                modal.hide();
                resolve(true);
            });

            // Handle cancel/close
            modalElement.addEventListener('hidden.bs.modal', () => {
                modalElement.remove();
                resolve(false);
            });

            modal.show();
        });
    },

    /**
     * Show loading overlay
     */
    showLoading() {
        if (document.getElementById('loading-overlay')) return;

        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="spinner-border text-accent" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;

        document.body.appendChild(overlay);
    },

    /**
     * Hide loading overlay
     */
    hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    },

    /**
     * Set button loading state
     */
    setButtonLoading(btn, loading) {
        if (loading) {
            btn.disabled = true;
            btn.dataset.originalText = btn.innerHTML;
            btn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Loading...
            `;
        } else {
            btn.disabled = false;
            if (btn.dataset.originalText) {
                btn.innerHTML = btn.dataset.originalText;
            }
        }
    },

    /**
     * Format date for display
     */
    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },

    /**
     * Format datetime for display
     */
    formatDateTime(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    /**
     * Format file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    },

    /**
     * Truncate text
     */
    truncate(text, length = 50) {
        if (!text) return '';
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    },

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    /**
     * Get status badge HTML
     */
    getStatusBadge(status) {
        const statusMap = {
            'Active': 'badge-active',
            'Completed': 'badge-completed',
            'In Progress': 'badge-in-progress',
            'To Do': 'badge-pending',
            'Blocked': 'badge-blocked',
            'On Hold': 'badge-on-hold',
            'New': 'badge-new',
            'Read': 'badge-in-progress',
            'Replied': 'badge-in-progress',
            'Converted': 'badge-completed',
            'Archived': 'badge-on-hold',
            'Draft': 'badge-pending',
            'Published': 'badge-completed'
        };

        const badgeClass = statusMap[status] || 'badge-secondary';
        return `<span class="badge ${badgeClass} badge-status">${status}</span>`;
    },

    /**
     * Get priority badge HTML
     */
    getPriorityBadge(priority) {
        const priorityMap = {
            'Urgent': 'badge-urgent',
            'High': 'badge-high',
            'Medium': 'badge-medium',
            'Low': 'badge-low'
        };

        const badgeClass = priorityMap[priority] || 'badge-secondary';
        return `<span class="badge ${badgeClass} badge-status">${priority}</span>`;
    },

    /**
     * Create empty state HTML
     */
    createEmptyState(icon, title, message, buttonText, buttonAction) {
        return `
            <div class="empty-state">
                <i class="bi bi-${icon}"></i>
                <h4>${title}</h4>
                <p>${message}</p>
                ${buttonText ? `<button class="btn btn-accent" onclick="${buttonAction}">${buttonText}</button>` : ''}
            </div>
        `;
    },

    /**
     * Initialize sidebar toggle
     */
    initSidebar() {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('active');
                }
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            });
        }
    },

    /**
     * Set active nav item
     */
    setActiveNav(path) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === path) {
                link.classList.add('active');
            }
        });
    },

    /**
     * Parse tags from comma-separated string
     */
    parseTags(tagString) {
        if (!tagString) return [];
        return tagString.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
    },

    /**
     * Create tag badges HTML
     */
    createTagBadges(tags) {
        if (!tags || tags.length === 0) return '';
        if (typeof tags === 'string') {
            tags = this.parseTags(tags);
        }
        return tags.map(tag => `<span class="badge bg-secondary me-1">${this.escapeHtml(tag)}</span>`).join('');
    },

    /**
     * Initialize tooltips
     */
    initTooltips() {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(el => new bootstrap.Tooltip(el));
    },

    /**
     * Debounce function
     */
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Initialize UI components on page load
document.addEventListener('DOMContentLoaded', () => {
    UI.initSidebar();
    UI.initTooltips();

    // Set active nav based on current page
    const currentPage = window.location.pathname.split('/').pop();
    UI.setActiveNav(currentPage);
});
