<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - STS Dashboard</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card glass-card">
            <div class="login-header text-center mb-4">
                <div class="logo-container mb-3">
                    <i class="bi bi-shield-lock-fill text-accent"></i>
                </div>
                <h2 class="mb-2">STS Dashboard</h2>
                <p class="text-muted">Sign in to access your workspace</p>
            </div>

            <!-- Alert messages -->
            <div id="alert-container"></div>

            <form id="login-form">
                <div class="mb-3">
                    <label for="username" class="form-label">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary">
                            <i class="bi bi-person-fill text-accent"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control bg-dark border-secondary text-white"
                            id="username"
                            name="username"
                            placeholder="Enter your username or email"
                            required
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary">
                            <i class="bi bi-lock-fill text-accent"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control bg-dark border-secondary text-white"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button
                            class="btn btn-outline-secondary"
                            type="button"
                            id="toggle-password"
                            tabindex="-1"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-accent w-100 mb-3" id="login-btn">
                    <span class="btn-text">Sign In</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <div class="login-footer text-center">
                <p class="text-muted small mb-2">
                    Default credentials for testing:
                </p>
                <p class="text-muted small">
                    <code>admin / Admin@123</code> or <code>partner / Partner@123</code>
                </p>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small">
                &copy; 2025 STS Software Solutions Agency. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="js/auth.js"></script>
</body>
</html>
