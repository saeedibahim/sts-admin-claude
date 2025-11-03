/**
 * API Helper
 * Centralized API calls with error handling
 */

const API = {
    baseURL: 'api/',

    /**
     * Make API request
     */
    async request(endpoint, options = {}) {
        const url = this.baseURL + endpoint;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json'
            }
        };

        const config = { ...defaultOptions, ...options };

        // Merge headers
        if (options.headers) {
            config.headers = { ...defaultOptions.headers, ...options.headers };
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            // Check for authentication errors
            if (response.status === 401) {
                window.location.href = 'login.php?error=unauthorized';
                return null;
            }

            return {
                success: response.ok && data.success,
                data: data.data || null,
                message: data.message || '',
                error: data.error || null,
                status: response.status
            };

        } catch (error) {
            console.error('API request failed:', error);
            return {
                success: false,
                data: null,
                message: 'Network error occurred',
                error: error.message,
                status: 0
            };
        }
    },

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;

        return this.request(url, {
            method: 'GET'
        });
    },

    /**
     * POST request
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * PUT request
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    /**
     * DELETE request
     */
    async delete(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'DELETE',
            body: JSON.stringify(data)
        });
    },

    // ==========================================
    // Statistics API
    // ==========================================
    async getStats() {
        return this.get('stats.php');
    },

    // ==========================================
    // Clients API
    // ==========================================
    async getClients(params = {}) {
        return this.get('clients.php', params);
    },

    async getClient(id) {
        return this.get(`clients.php?id=${id}`);
    },

    async createClient(data) {
        return this.post('clients.php', data);
    },

    async updateClient(id, data) {
        return this.put('clients.php', { id, ...data });
    },

    async deleteClient(id) {
        return this.delete('clients.php', { id });
    },

    // ==========================================
    // Contacts API
    // ==========================================
    async getContacts(params = {}) {
        return this.get('contact-list.php', params);
    },

    async getContact(id) {
        return this.get(`contact-list.php?id=${id}`);
    },

    async updateContact(id, data) {
        return this.put('contact-list.php', { id, ...data });
    },

    async deleteContact(id) {
        return this.delete('contact-list.php', { id });
    },

    // ==========================================
    // Tasks API
    // ==========================================
    async getTasks(params = {}) {
        return this.get('tasks.php', params);
    },

    async getTask(id) {
        return this.get(`tasks.php?id=${id}`);
    },

    async createTask(data) {
        return this.post('tasks.php', data);
    },

    async updateTask(id, data) {
        return this.put('tasks.php', { id, ...data });
    },

    async deleteTask(id) {
        return this.delete('tasks.php', { id });
    },

    async updateTaskStatus(id, status) {
        return this.put('tasks.php', { id, status });
    },

    // ==========================================
    // Files API
    // ==========================================
    async getFiles(params = {}) {
        return this.get('files.php', params);
    },

    async uploadFile(formData) {
        return this.request('files.php', {
            method: 'POST',
            headers: {}, // Let browser set Content-Type for FormData
            body: formData
        });
    },

    async deleteFile(id) {
        return this.delete('files.php', { id });
    },

    async downloadFile(id) {
        window.open(`api/files.php?action=download&id=${id}`, '_blank');
    },

    // ==========================================
    // Documents API
    // ==========================================
    async getDocuments(params = {}) {
        return this.get('documents.php', params);
    },

    async getDocument(id) {
        return this.get(`documents.php?id=${id}`);
    },

    async createDocument(data) {
        return this.post('documents.php', data);
    },

    async updateDocument(id, data) {
        return this.put('documents.php', { id, ...data });
    },

    async deleteDocument(id) {
        return this.delete('documents.php', { id });
    },

    async publishDocument(id) {
        return this.put('documents.php', { id, action: 'publish' });
    }
};
