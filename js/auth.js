/**
 * Authentication JavaScript
 * Handles login/logout functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize login form
    if (document.getElementById('login-form')) {
        initLoginForm();
    }

    // Check for error/success messages in URL
    checkUrlMessages();
});

/**
 * Initialize login form
 */
function initLoginForm() {
    const form = document.getElementById('login-form');
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    // Toggle password visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        // Validate inputs
        if (!username || !password) {
            showAlert('Please enter both username and password', 'danger');
            return;
        }

        // Show loading state
        const btn = document.getElementById('login-btn');
        setButtonLoading(btn, true);

        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message || 'Login successful! Redirecting...', 'success');

                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.data.redirect || 'index.html';
                }, 500);
            } else {
                showAlert(data.message || 'Login failed. Please try again.', 'danger');
                setButtonLoading(btn, false);
            }

        } catch (error) {
            console.error('Login error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
            setButtonLoading(btn, false);
        }
    });
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const container = document.getElementById('alert-container');
    if (!container) return;

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show mb-3`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    `;

    container.innerHTML = '';
    container.appendChild(alertDiv);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 5000);
}

/**
 * Set button loading state
 */
function setButtonLoading(btn, loading) {
    if (loading) {
        btn.disabled = true;
        btn.classList.add('loading');
        btn.querySelector('.btn-text').style.opacity = '0';
        btn.querySelector('.spinner-border').classList.remove('d-none');
    } else {
        btn.disabled = false;
        btn.classList.remove('loading');
        btn.querySelector('.btn-text').style.opacity = '1';
        btn.querySelector('.spinner-border').classList.add('d-none');
    }
}

/**
 * Check for error/success messages in URL parameters
 */
function checkUrlMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const success = urlParams.get('success');

    if (error) {
        let message = 'An error occurred';
        switch (error) {
            case 'session_expired':
                message = 'Your session has expired. Please login again.';
                break;
            case 'account_disabled':
                message = 'Your account has been disabled. Please contact support.';
                break;
            case 'system_error':
                message = 'A system error occurred. Please try again later.';
                break;
            case 'unauthorized':
                message = 'Please login to access this page.';
                break;
        }
        showAlert(message, 'danger');
    }

    if (success) {
        let message = '';
        switch (success) {
            case 'logout':
                message = 'You have been logged out successfully.';
                break;
        }
        if (message) {
            showAlert(message, 'success');
        }
    }

    // Clean URL
    if (error || success) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

/**
 * Logout function (can be called from anywhere)
 */
async function logout() {
    try {
        const response = await fetch('api/auth.php', {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = 'login.php?success=logout';
        } else {
            console.error('Logout failed:', data.message);
            window.location.href = 'login.php';
        }

    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'login.php';
    }
}
