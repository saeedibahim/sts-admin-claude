/**
 * Clients Management JavaScript
 * Works with existing table structure (client + project data)
 */

// Global variables
let allClients = [];
let currentClientId = null;
let clientModal, viewClientModal, deleteModal;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    clientModal = new bootstrap.Modal(document.getElementById('clientModal'));
    viewClientModal = new bootstrap.Modal(document.getElementById('viewClientModal'));
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    loadClients();
    
    document.getElementById('addClientBtn').addEventListener('click', openAddModal);
    document.getElementById('saveClientBtn').addEventListener('click', saveClient);
    document.getElementById('searchInput').addEventListener('input', debounce(filterClients, 300));
    document.getElementById('statusFilter').addEventListener('change', filterClients);
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    document.getElementById('confirmDeleteBtn').addEventListener('click', deleteClient);
    document.getElementById('editFromViewBtn').addEventListener('click', editFromView);
});

async function loadClients(filters = {}) {
    try {
        showLoading(true);
        let url = 'api/clients.php';
        const params = new URLSearchParams();
        if (filters.search) params.append('search', filters.search);
        if (filters.status) params.append('status', filters.status);
        if (params.toString()) url += '?' + params.toString();
        
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            allClients = result.data;
            renderClients(allClients);
            updateStats(allClients);
        } else {
            showToast('Error loading clients', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to load clients', 'error');
    } finally {
        showLoading(false);
    }
}

function renderClients(clients) {
    const tbody = document.getElementById('clientsTableBody');
    const noResults = document.getElementById('noResults');
    
    if (clients.length === 0) {
        tbody.innerHTML = '';
        noResults.style.display = 'block';
        return;
    }
    
    noResults.style.display = 'none';
    tbody.innerHTML = clients.map(c => `
        <tr>
            <td>
                <strong>${escapeHtml(c.client_name)}</strong>
                ${c.company_name ? `<br><small class="text-muted">${escapeHtml(c.company_name)}</small>` : ''}
            </td>
            <td>${escapeHtml(c.project_name)}</td>
            <td>
                ${c.email ? `<a href="mailto:${escapeHtml(c.email)}">${escapeHtml(c.email)}</a><br>` : ''}
                ${c.phone ? `<small class="text-muted">${escapeHtml(c.phone)}</small>` : ''}
            </td>
            <td><span class="badge bg-${getStatusColor(c.project_status)}">${escapeHtml(c.project_status)}</span></td>
            <td>${c.deadline ? formatDate(c.deadline) : '-'}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewClient(${c.id})"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-outline-secondary" onclick="editClient(${c.id})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-danger" onclick="confirmDelete(${c.id}, '${escapeHtml(c.client_name)}')"><i class="bi bi-trash"></i></button>
                </div>
            </td>
        </tr>
    `).join('');
}

function updateStats(clients) {
    document.getElementById('totalClients').textContent = clients.length;
    document.getElementById('activeProjects').textContent = clients.filter(c => c.project_status === 'Active').length;
    document.getElementById('completedProjects').textContent = clients.filter(c => c.project_status === 'Completed').length;
    document.getElementById('onHoldProjects').textContent = clients.filter(c => c.project_status === 'On Hold').length;
}

function filterClients() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const filters = {};
    if (search) filters.search = search;
    if (status && status !== 'all') filters.status = status;
    loadClients(filters);
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = 'all';
    loadClients();
}

function openAddModal() {
    currentClientId = null;
    document.getElementById('clientModalTitle').textContent = 'Add Client & Project';
    document.getElementById('clientForm').reset();
    document.getElementById('clientId').value = '';
    clientModal.show();
}

async function viewClient(id) {
    try {
        const response = await fetch(`api/clients.php?id=${id}`);
        const result = await response.json();
        if (!result.success) {
            showToast('Error loading client', 'error');
            return;
        }
        
        const c = result.data;
        currentClientId = id;
        const tech = c.tech_stack && c.tech_stack.length > 0 
            ? c.tech_stack.map(t => `<span class="badge bg-secondary me-1">${escapeHtml(t)}</span>`).join('')
            : '<span class="text-muted">Not specified</span>';
        
        document.getElementById('clientDetailsBody').innerHTML = `
            <div class="row g-4">
                <div class="col-md-12">
                    <h6 class="text-primary mb-3"><i class="bi bi-person-circle"></i> Client Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="text-muted small">Client Name</label><p><strong>${escapeHtml(c.client_name)}</strong></p></div>
                        <div class="col-md-6"><label class="text-muted small">Company</label><p>${c.company_name ? escapeHtml(c.company_name) : '<span class="text-muted">N/A</span>'}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Email</label><p>${c.email ? `<a href="mailto:${escapeHtml(c.email)}">${escapeHtml(c.email)}</a>` : '<span class="text-muted">N/A</span>'}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Phone</label><p>${c.phone ? escapeHtml(c.phone) : '<span class="text-muted">N/A</span>'}</p></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h6 class="text-primary mb-3"><i class="bi bi-briefcase-fill"></i> Project Information</h6>
                    <div class="row g-3">
                        <div class="col-md-8"><label class="text-muted small">Project Name</label><p><strong>${escapeHtml(c.project_name)}</strong></p></div>
                        <div class="col-md-4"><label class="text-muted small">Status</label><p><span class="badge bg-${getStatusColor(c.project_status)}">${escapeHtml(c.project_status)}</span></p></div>
                        ${c.project_description ? `<div class="col-md-12"><label class="text-muted small">Description</label><p>${escapeHtml(c.project_description)}</p></div>` : ''}
                        <div class="col-md-6"><label class="text-muted small">Start Date</label><p>${c.start_date ? formatDate(c.start_date) : '<span class="text-muted">Not set</span>'}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Deadline</label><p>${c.deadline ? formatDate(c.deadline) : '<span class="text-muted">Not set</span>'}</p></div>
                        <div class="col-md-12"><label class="text-muted small">Tech Stack</label><p>${tech}</p></div>
                    </div>
                </div>
                ${c.additional_notes ? `<div class="col-md-12"><h6 class="text-primary mb-3"><i class="bi bi-file-text"></i> Notes</h6><p>${escapeHtml(c.additional_notes)}</p></div>` : ''}
                <div class="col-md-12"><div class="text-muted small">Created: ${formatDateTime(c.created_at)} | Updated: ${formatDateTime(c.updated_at)}</div></div>
            </div>
        `;
        viewClientModal.show();
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to load client', 'error');
    }
}

async function editClient(id) {
    try {
        const response = await fetch(`api/clients.php?id=${id}`);
        const result = await response.json();
        if (!result.success) {
            showToast('Error loading client', 'error');
            return;
        }
        
        const c = result.data;
        currentClientId = id;
        document.getElementById('clientModalTitle').textContent = 'Edit Client & Project';
        document.getElementById('clientId').value = c.id;
        document.getElementById('clientName').value = c.client_name;
        document.getElementById('companyName').value = c.company_name || '';
        document.getElementById('email').value = c.email || '';
        document.getElementById('phone').value = c.phone || '';
        document.getElementById('projectName').value = c.project_name;
        document.getElementById('projectStatus').value = c.project_status;
        document.getElementById('projectDescription').value = c.project_description || '';
        document.getElementById('startDate').value = c.start_date || '';
        document.getElementById('deadline').value = c.deadline || '';
        document.getElementById('techStack').value = c.tech_stack ? c.tech_stack.join(', ') : '';
        document.getElementById('additionalNotes').value = c.additional_notes || '';
        clientModal.show();
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to load client', 'error');
    }
}

function editFromView() {
    viewClientModal.hide();
    if (currentClientId) editClient(currentClientId);
}

async function saveClient() {
    const form = document.getElementById('clientForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const id = document.getElementById('clientId').value;
    const data = {
        client_name: document.getElementById('clientName').value.trim(),
        company_name: document.getElementById('companyName').value.trim(),
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        project_name: document.getElementById('projectName').value.trim(),
        project_status: document.getElementById('projectStatus').value,
        project_description: document.getElementById('projectDescription').value.trim(),
        start_date: document.getElementById('startDate').value || null,
        deadline: document.getElementById('deadline').value || null,
        tech_stack: document.getElementById('techStack').value.trim(),
        additional_notes: document.getElementById('additionalNotes').value.trim()
    };
    if (id) data.id = parseInt(id);
    
    try {
        setButtonLoading('saveClientBtn', true);
        const response = await fetch('api/clients.php', {
            method: id ? 'PUT' : 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            showToast(result.message, 'success');
            clientModal.hide();
            loadClients();
            form.reset();
        } else {
            showToast(result.message || 'Error saving client', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to save client', 'error');
    } finally {
        setButtonLoading('saveClientBtn', false);
    }
}

function confirmDelete(id, name) {
    currentClientId = id;
    document.getElementById('deleteClientName').textContent = name;
    deleteModal.show();
}

async function deleteClient() {
    if (!currentClientId) return;
    try {
        setButtonLoading('confirmDeleteBtn', true);
        const response = await fetch('api/clients.php', {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: currentClientId})
        });
        const result = await response.json();
        if (result.success) {
            showToast(result.message, 'success');
            deleteModal.hide();
            loadClients();
        } else {
            showToast(result.message || 'Error deleting client', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to delete client', 'error');
    } finally {
        setButtonLoading('confirmDeleteBtn', false);
    }
}

function getStatusColor(status) {
    return {'Active': 'success', 'Completed': 'info', 'On Hold': 'warning'}[status] || 'secondary';
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'});
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showLoading(show) {
    const row = document.getElementById('loadingRow');
    if (row) row.style.display = show ? 'table-row' : 'none';
}

function setButtonLoading(buttonId, isLoading) {
    const btn = document.getElementById(buttonId);
    const text = btn.querySelector('.btn-text');
    const spinner = btn.querySelector('.btn-spinner');
    if (isLoading) {
        text.classList.add('d-none');
        spinner.classList.remove('d-none');
        btn.disabled = true;
    } else {
        text.classList.remove('d-none');
        spinner.classList.add('d-none');
        btn.disabled = false;
    }
}

function showToast(message, type = 'info') {
    const color = {success: '#28a745', error: '#dc3545', info: '#17a2b8'}[type];
    const toast = document.createElement('div');
    toast.style.cssText = `position:fixed;top:20px;right:20px;background:${color};color:white;padding:15px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.3);z-index:9999;animation:slideIn 0.3s ease`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(69, 196, 176, 0.1); color: var(--accent-color); font-size: 1.5rem; }
`;
document.head.appendChild(style);