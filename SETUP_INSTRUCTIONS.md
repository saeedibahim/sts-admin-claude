# STS Dashboard - Setup Instructions

## Project Status

This STS Dashboard application has been partially built with the following components completed:

### ✅ Completed Components

1. **Database Schema** (`database.sql`)
   - All 9 tables created
   - Proper indexes and foreign keys
   - Sample data structure ready

2. **Backend Infrastructure**
   - Database connection class (`includes/db.php`)
   - Authentication middleware (`includes/auth_check.php`)
   - Helper functions (`includes/functions.php`)
   - API configuration (`api/config.php`)

3. **Authentication System**
   - Login API (`api/auth.php`)
   - Login page (`login.php` + `css/login.css` + `js/auth.js`)
   - Logout handler (`logout.php`)
   - Session management

4. **Core Styles**
   - Global styles with dark theme (`css/style.css`)
   - Dashboard layout (`css/dashboard.css`)
   - Glassmorphism effects
   - Responsive design

5. **API Endpoints**
   - Statistics API (`api/stats.php`)
   - Clients CRUD API (`api/clients.php`)

6. **Dashboard Pages**
   - Overview/Home page (`index.html`)
   - Sidebar navigation
   - Statistics cards

7. **JavaScript Utilities**
   - API helper (`js/api.js`)
   - UI utilities (`js/ui.js`)
   - Toast notifications
   - Modals and loading states

### ⏳ Remaining Components to Build

The following components need to be completed for a fully functional system:

1. **Clients Module**
   - `clients.html` - Client management interface
   - `js/app.js` - Client management logic
   - Forms for add/edit clients
   - Client listing with search and filters

2. **Contacts Module**
   - `api/contact.php` - External contact form API
   - `api/contact-list.php` - Contact management API
   - `contacts.html` - Contact submissions interface
   - `js/contacts.js` - Contact management logic

3. **Tasks Module**
   - `api/tasks.php` - Tasks CRUD API
   - `tasks.html` - Kanban board interface
   - `css/tasks.css` - Kanban board styles
   - `js/tasks.js` - Task management and drag-drop

4. **Files Module**
   - `api/files.php` - File upload/download API
   - `files.html` - File library interface
   - `css/files.css` - File grid styles
   - `js/files.js` - File upload with Dropzone.js

5. **Documents Module**
   - `api/documents.php` - Documents CRUD API
   - `documents.html` - Document editor interface
   - `css/editor.css` - Quill editor customization
   - `js/editor.js` - Document editor with Quill.js

6. **Configuration Files**
   - `.htaccess` - Apache security and rewrites
   - `.gitignore` - Git ignore patterns
   - `README.md` - Complete documentation

## Quick Setup for XAMPP (Current State)

### Step 1: Database Setup

1. Start XAMPP and ensure MySQL is running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Import the database:
   ```sql
   source /path/to/sts-admin-claude/database.sql
   ```
4. **IMPORTANT**: Update sample-data.sql with proper password hashes:
   ```php
   // Generate hashes in PHP:
   echo password_hash('Admin@123', PASSWORD_DEFAULT);
   echo password_hash('Partner@123', PASSWORD_DEFAULT);
   ```
5. Import sample data:
   ```sql
   source /path/to/sts-admin-claude/sample-data.sql
   ```

### Step 2: File Setup

1. Copy the project to XAMPP htdocs:
   ```bash
   cp -r /path/to/sts-admin-claude /xampp/htdocs/
   ```

2. Set permissions for upload directories:
   ```bash
   chmod 755 uploads/documents
   chmod 755 uploads/images
   chmod 755 uploads/temp
   ```

### Step 3: Configuration

1. The `api/config.php` is already configured for XAMPP defaults:
   - DB_HOST: localhost
   - DB_USER: root
   - DB_PASS: (empty)
   - DB_NAME: sts_dashboard

2. Adjust if your XAMPP has different settings

### Step 4: Access the Application

1. Open browser and navigate to:
   ```
   http://localhost/sts-admin-claude/login.php
   ```

2. Login with default credentials:
   - **Admin**: `admin` / `Admin@123`
   - **Partner**: `partner` / `Partner@123`

## What Works Currently

✅ Login system with session management
✅ Dashboard overview with live statistics
✅ User authentication and authorization
✅ Database connection and queries
✅ API infrastructure
✅ Responsive sidebar navigation
✅ Toast notifications and UI helpers

## Next Development Steps

To complete the application, implement the modules in this order:

1. **Clients Module** (Foundation for other modules)
2. **Tasks Module** (Core functionality)
3. **Contacts Module** (Lead management)
4. **Files Module** (Asset management)
5. **Documents Module** (Content creation)

## File Structure Reference

```
sts-admin-claude/
├── api/
│   ├── config.php ✅
│   ├── auth.php ✅
│   ├── stats.php ✅
│   ├── clients.php ✅
│   ├── contact.php ⏳
│   ├── contact-list.php ⏳
│   ├── tasks.php ⏳
│   ├── files.php ⏳
│   └── documents.php ⏳
├── css/
│   ├── style.css ✅
│   ├── dashboard.css ✅
│   ├── login.css ✅
│   ├── tasks.css ⏳
│   ├── files.css ⏳
│   └── editor.css ⏳
├── js/
│   ├── api.js ✅
│   ├── ui.js ✅
│   ├── auth.js ✅
│   ├── app.js ⏳
│   ├── contacts.js ⏳
│   ├── tasks.js ⏳
│   ├── files.js ⏳
│   └── editor.js ⏳
├── includes/
│   ├── db.php ✅
│   ├── auth_check.php ✅
│   └── functions.php ✅
├── uploads/
│   ├── documents/
│   ├── images/
│   └── temp/
├── assets/
│   └── images/
├── index.html ✅
├── login.php ✅
├── logout.php ✅
├── clients.html ⏳
├── contacts.html ⏳
├── tasks.html ⏳
├── files.html ⏳
├── documents.html ⏳
├── database.sql ✅
├── sample-data.sql ✅
├── .htaccess ⏳
├── .gitignore ⏳
└── README.md ⏳
```

## Deployment to Hostinger (When Complete)

1. Export database from XAMPP
2. Update `api/config.php` with production credentials
3. Upload files via FTP/File Manager
4. Import database to Hostinger MySQL
5. Set environment to 'production'
6. Configure `.htaccess` for security
7. Test all functionality

## Security Checklist

- ✅ Password hashing with password_hash()
- ✅ Prepared statements for SQL queries
- ✅ XSS prevention with htmlspecialchars()
- ✅ Session security and validation
- ✅ Input validation and sanitization
- ⏳ File upload validation
- ⏳ Rate limiting on contact forms
- ⏳ CORS headers configured
- ⏳ .htaccess security rules

## Support

For issues or questions:
1. Check database connection in `api/config.php`
2. Verify MySQL service is running
3. Check PHP error logs
4. Ensure proper file permissions on uploads/

## Development Notes

- All dates use YYYY-MM-DD format (MySQL DATE)
- JSON fields properly encoded/decoded
- snake_case for database, camelCase for JavaScript
- Dark theme: #0A0A0A background, #45C4B0 accent
- Bootstrap 5.3 for UI components
