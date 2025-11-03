# STS Dashboard - Team Workspace Management System

A comprehensive dashboard application for STS Software Solutions Agency to manage clients, projects, tasks, files, documents, and contact form submissions.

## Overview

STS Dashboard is a full-stack web application built with modern technologies, featuring a dark theme with glassmorphism effects, responsive design, and a complete REST API backend.

### Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript (ES6+), Bootstrap 5.3, Bootstrap Icons
- **Backend**: PHP 8.x, MySQL 8.0
- **Libraries**: Quill.js (planned), Dropzone.js (planned)
- **Environment**: XAMPP (development), Hostinger (production ready)

### Design

- Dark theme with #0A0A0A background and #45C4B0 teal accent
- Glassmorphism effects on cards
- Smooth animations and transitions
- Fully responsive (mobile, tablet, desktop)
- Professional, modern aesthetic

## Features

### ✅ Implemented

- **Authentication System**
  - Secure login with password hashing
  - Session-based authentication
  - 24-hour session timeout
  - Remember last visited page

- **Dashboard Overview**
  - Real-time statistics across 3 rows (12 stat cards)
  - Clients & Projects metrics
  - Task management metrics
  - Files & Documents metrics
  - Recent activity feed

- **Responsive Sidebar Navigation**
  - User greeting with avatar
  - Active page highlighting
  - Badge notifications
  - Mobile-friendly hamburger menu

- **API Infrastructure**
  - RESTful API endpoints
  - Prepared statements for security
  - JSON request/response format
  - Error handling with proper HTTP status codes
  - CORS support for external requests

- **Clients Management API**
  - Full CRUD operations
  - Search and filter capabilities
  - Project status tracking
  - Tech stack management (JSON array)

- **Statistics API**
  - Real-time dashboard metrics
  - Task analytics by status/priority
  - User-specific task counts

### ⏳ To Be Implemented

- Clients management UI
- Contact form submissions management
- Kanban board for tasks
- File upload system with Dropzone.js
- Document editor with Quill.js
- Additional API endpoints

## Database Schema

9 tables total:

1. **users** - Authentication and user management
2. **clients** - Client and project information
3. **contact_submissions** - Contact forms from main website
4. **files** - File upload system (Word, Excel, PDF, Images)
5. **documents** - Rich text editor documents
6. **tasks** - Task management system
7. **task_checklist** - Sub-tasks for tasks
8. **task_attachments** - Link tasks to files/documents
9. **sessions** - Login session management

## Installation

### Prerequisites

- XAMPP (or similar PHP/MySQL environment)
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Modern web browser

### Step 1: Clone or Download

```bash
git clone <repository-url> sts-dashboard
cd sts-dashboard
```

### Step 2: Database Setup

1. Start XAMPP and ensure MySQL is running

2. Open phpMyAdmin (http://localhost/phpmyadmin)

3. Import the database schema:
   - Click "Import" tab
   - Choose file: `database.sql`
   - Click "Go"

4. **IMPORTANT**: Generate password hashes before importing sample data

   Create a temporary PHP file to generate hashes:
   ```php
   <?php
   // generate_hashes.php
   echo "Admin password hash: " . password_hash('Admin@123', PASSWORD_DEFAULT) . "\n";
   echo "Partner password hash: " . password_hash('Partner@123', PASSWORD_DEFAULT) . "\n";
   ```

5. Update `sample-data.sql` with the generated hashes:
   - Replace the placeholder hashes in the INSERT INTO users statement
   - Save the file

6. Import sample data:
   - Click "Import" tab
   - Choose file: `sample-data.sql`
   - Click "Go"

### Step 3: File Setup

1. Copy project to XAMPP htdocs:
   ```bash
   # Windows
   copy /path/to/sts-dashboard C:\xampp\htdocs\

   # Mac/Linux
   cp -r /path/to/sts-dashboard /Applications/XAMPP/htdocs/
   ```

2. Set permissions for upload directories (Mac/Linux):
   ```bash
   chmod 755 uploads/documents
   chmod 755 uploads/images
   chmod 755 uploads/temp
   ```

### Step 4: Configuration

The `api/config.php` is pre-configured for XAMPP defaults:
- Database: localhost
- Username: root
- Password: (empty)
- Database name: sts_dashboard

Adjust if your XAMPP has different settings.

### Step 5: Access Application

1. Open browser: `http://localhost/sts-dashboard/login.php`

2. Login with default credentials:
   - Username: `admin` Password: `Admin@123`
   - Username: `partner` Password: `Partner@123`

## File Structure

```
sts-dashboard/
├── api/                    # Backend API endpoints
│   ├── config.php         # Database and app configuration
│   ├── auth.php           # Authentication endpoints
│   ├── clients.php        # Clients CRUD API
│   ├── stats.php          # Dashboard statistics
│   └── ...                # Other API endpoints (to be built)
├── css/                    # Stylesheets
│   ├── style.css          # Global styles & theme
│   ├── dashboard.css      # Dashboard layout & sidebar
│   ├── login.css          # Login page styles
│   └── ...                # Module-specific styles
├── js/                     # JavaScript files
│   ├── api.js             # Centralized API calls
│   ├── ui.js              # UI utilities (toasts, modals, etc.)
│   ├── auth.js            # Authentication logic
│   └── ...                # Module-specific logic
├── includes/               # PHP includes
│   ├── db.php             # Database connection class
│   ├── auth_check.php     # Session validation middleware
│   └── functions.php      # Helper functions
├── uploads/                # File uploads (git ignored)
│   ├── documents/         # Word, Excel, PDF files
│   ├── images/            # Uploaded images
│   └── temp/              # Temporary uploads
├── assets/                 # Static assets
│   └── images/            # Logo, icons
├── index.html             # Dashboard overview page
├── login.php              # Login page
├── logout.php             # Logout handler
├── database.sql           # Database schema
├── sample-data.sql        # Sample data
├── .htaccess              # Apache configuration
├── .gitignore             # Git ignore patterns
└── README.md              # This file
```

## Usage

### Dashboard Overview

The main dashboard (`index.html`) displays:
- 12 real-time statistics cards organized in 3 rows
- Recent tasks activity feed
- Quick navigation to all modules

### Authentication

- Session-based with 24-hour timeout
- Passwords hashed with bcrypt
- Session tokens stored in database
- Automatic redirect to last visited page after login

### API Usage

All API endpoints follow REST conventions:

```javascript
// Example: Get all clients
const response = await API.getClients();
if (response.success) {
    const clients = response.data;
}

// Example: Create client
const newClient = await API.createClient({
    client_name: 'John Doe',
    project_name: 'Website Redesign',
    email: 'john@example.com',
    tech_stack: ['React', 'Node.js']
});
```

### Response Format

Success:
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

Error:
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error (development only)"
}
```

## Security Features

- ✅ Password hashing with `password_hash()`
- ✅ Prepared statements for SQL queries
- ✅ XSS prevention with `htmlspecialchars()`
- ✅ Session token validation
- ✅ Input sanitization
- ✅ CSRF protection ready
- ✅ Secure HTTP headers via .htaccess

## Deployment to Production

1. Update `api/config.php`:
   ```php
   define('ENV', 'production');
   define('DB_HOST', 'your-db-host');
   define('DB_USER', 'your-db-user');
   define('DB_PASS', 'your-db-password');
   ```

2. Set error reporting for production:
   - Errors hidden from users
   - All errors logged to file

3. Enable HTTPS redirect in `.htaccess`

4. Configure proper file permissions

5. Update API_KEY for production

6. Test all functionality thoroughly

## Development Roadmap

### Phase 1: Core Foundation (Completed)
- ✅ Database schema
- ✅ Authentication system
- ✅ Dashboard layout
- ✅ API infrastructure
- ✅ Clients API

### Phase 2: Module Completion
- ⏳ Clients UI
- ⏳ Contact submissions
- ⏳ Task management (Kanban)
- ⏳ File uploads
- ⏳ Document editor

### Phase 3: Enhancements
- ⏳ User roles & permissions
- ⏳ Email notifications
- ⏳ Activity logs
- ⏳ Reports & analytics
- ⏳ Advanced search

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Troubleshooting

### Cannot connect to database
- Verify MySQL is running in XAMPP
- Check credentials in `api/config.php`
- Ensure database `sts_dashboard` exists

### Login fails
- Verify password hashes in `users` table match actual passwords
- Check session configuration in PHP
- Clear browser cookies/cache

### File upload fails
- Check folder permissions on `uploads/` directories
- Verify `MAX_FILE_SIZE` in config
- Check PHP `upload_max_filesize` setting

### API returns 500 error
- Check PHP error log
- Verify database connection
- Check ENV setting in config.php

## Contributing

This is a custom application for STS Software Solutions Agency. For modifications or enhancements, contact the development team.

## License

Proprietary - © 2025 STS Software Solutions Agency

## Support

For technical support or questions:
- Email: admin@sts-agency.com
- Check `SETUP_INSTRUCTIONS.md` for detailed setup guide

## Changelog

### Version 0.5.0 (Current - In Development)
- ✅ Complete authentication system
- ✅ Dashboard with live statistics
- ✅ Clients API (CRUD operations)
- ✅ Statistics API
- ✅ Responsive sidebar navigation
- ✅ Toast notifications & UI utilities
- ⏳ Pending: UI for all modules

### Future Versions
- 1.0.0 - All modules completed
- 1.1.0 - User roles & permissions
- 1.2.0 - Email notifications
- 2.0.0 - Advanced features & analytics

---

**Built with ❤️ for STS Software Solutions Agency**
