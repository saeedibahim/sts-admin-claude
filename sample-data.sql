-- STS Dashboard Sample Data
-- Run this AFTER database.sql
-- Database: sts_dashboard

USE sts_dashboard;

-- ==========================================
-- Sample Users (passwords are hashed)
-- ==========================================
-- Password for 'admin' user: Admin@123
-- Password for 'partner' user: Partner@123
-- Hashes generated using password_hash() with PASSWORD_DEFAULT
INSERT INTO users (username, email, password_hash, full_name, role, is_active) VALUES
('admin', 'admin@sts-agency.com', '$2y$12$wYNS0qdG3vu/Oaeiz4yVSuzNEIhSfE6MiRGHMD4jsLubmxPNOaN1C', 'Admin User', 'Admin', TRUE),
('partner', 'partner@sts-agency.com', '$2y$12$ZG2Ei6LW46mFElReGGB/SuA7eH7t/uOsi0869gjuRkwqj30ef4zjq', 'Partner User', 'Admin', TRUE);

-- ==========================================
-- Sample Clients (5 clients with projects)
-- ==========================================
INSERT INTO clients (client_name, company_name, email, phone, project_name, project_status, project_description, start_date, deadline, tech_stack, additional_notes) VALUES
('John Smith', 'TechCorp Inc', 'john@techcorp.com', '+1-555-0101', 'E-commerce Website', 'Active', 'Build a modern e-commerce platform with payment integration and inventory management', '2025-01-15', '2025-04-30', '["React", "Node.js", "MongoDB", "Stripe"]', 'Client prefers weekly updates on Fridays'),

('Sarah Johnson', 'StartupHub', 'sarah@startuphub.io', '+1-555-0102', 'Mobile App Development', 'In Progress', 'Cross-platform mobile app for startup management and networking', '2025-02-01', '2025-06-15', '["React Native", "Firebase", "AWS"]', 'Beta testing planned for May'),

('Michael Chen', 'DataAnalytics Pro', 'michael@dataanalytics.com', '+1-555-0103', 'Data Dashboard', 'Active', 'Interactive data visualization dashboard with real-time analytics', '2024-12-01', '2025-03-31', '["Vue.js", "Python", "PostgreSQL", "D3.js"]', 'Requires high security compliance'),

('Emily Rodriguez', 'EduLearn Platform', 'emily@edulearn.com', '+1-555-0104', 'Learning Management System', 'Completed', 'Full-featured LMS with course management, student tracking, and assessments', '2024-09-01', '2025-01-15', '["Next.js", "MySQL", "Redis", "AWS S3"]', 'Successfully launched January 2025'),

('David Thompson', 'HealthTech Solutions', 'david@healthtech.com', '+1-555-0105', 'Patient Portal', 'On Hold', 'HIPAA-compliant patient portal with appointment scheduling and records', '2025-01-10', '2025-05-30', '["Angular", "Node.js", "PostgreSQL", "Docker"]', 'On hold pending regulatory approval');

-- ==========================================
-- Sample Contact Submissions (5 contacts)
-- ==========================================
INSERT INTO contact_submissions (name, email, phone, company, project_type, budget, message, status, priority, source, ip_address) VALUES
('Alex Martinez', 'alex@innovatetech.com', '+1-555-0201', 'InnovateTech', 'Web Development', '$10k-$25k', 'We need a custom web application for our internal project management. Looking for a team with experience in modern frameworks.', 'New', 'High', 'website', '192.168.1.100'),

('Lisa Wang', 'lisa.wang@globalcorp.com', '+1-555-0202', 'Global Corp', 'Automation', '$25k-$50k', 'Interested in automating our data processing workflows. We handle large datasets daily and need efficient solutions.', 'Read', 'Medium', 'website', '192.168.1.101'),

('Robert Brown', 'rob@smallbiz.com', '+1-555-0203', 'Small Business Co', 'Website Redesign', '$5k-$10k', 'Our company website needs a modern redesign. Current site is outdated and not mobile-friendly.', 'Replied', 'Low', 'website', '192.168.1.102'),

('Jennifer Lee', 'jennifer@techstartup.io', '+1-555-0204', 'TechStartup', 'Mobile App', '$50k+', 'Looking for a development partner to build our MVP mobile app. We have detailed specs and design mockups ready.', 'Converted', 'High', 'website', '192.168.1.103'),

('Thomas Anderson', 'thomas@enterprise.com', '+1-555-0205', 'Enterprise Solutions', 'Custom Software', '$25k-$50k', 'Need custom software development for our supply chain management system. Integration with existing ERP required.', 'New', 'High', 'website', '192.168.1.104');

-- ==========================================
-- Sample Tasks (10 tasks)
-- ==========================================
INSERT INTO tasks (title, description, status, priority, assigned_to, due_date, client_id, project_name, tags, created_by) VALUES
('Design homepage mockup', 'Create initial design mockups for TechCorp e-commerce homepage including hero section, featured products, and navigation', 'Completed', 'High', 1, '2025-02-01', 1, 'E-commerce Website', '["design", "frontend"]', 1),

('Setup database schema', 'Design and implement database schema for user authentication, products, orders, and inventory', 'Completed', 'Urgent', 2, '2025-02-05', 1, 'E-commerce Website', '["backend", "database"]', 1),

('Implement payment integration', 'Integrate Stripe payment gateway with checkout process', 'In Progress', 'Urgent', 1, '2025-03-15', 1, 'E-commerce Website', '["backend", "payment"]', 1),

('Mobile app user authentication', 'Implement Firebase authentication for iOS and Android apps', 'In Progress', 'High', 2, '2025-03-20', 2, 'Mobile App Development', '["mobile", "auth"]', 2),

('Create onboarding flow', 'Design and develop user onboarding screens for StartupHub mobile app', 'To Do', 'Medium', 1, '2025-03-25', 2, 'Mobile App Development', '["mobile", "ux"]', 1),

('Data visualization components', 'Build reusable chart components using D3.js for analytics dashboard', 'In Progress', 'High', 1, '2025-03-10', 3, 'Data Dashboard', '["frontend", "charts"]', 1),

('API endpoint optimization', 'Optimize database queries and add caching for dashboard API endpoints', 'To Do', 'Medium', 2, '2025-03-18', 3, 'Data Dashboard', '["backend", "performance"]', 1),

('Course upload feature', 'Implement course content upload with video processing and storage', 'Completed', 'Medium', 1, '2025-01-10', 4, 'Learning Management System', '["backend", "media"]', 2),

('Security audit', 'Conduct security audit and implement HIPAA compliance measures', 'Blocked', 'Urgent', 2, '2025-03-05', 5, 'Patient Portal', '["security", "compliance"]', 1),

('Documentation update', 'Update API documentation and create developer guides for all projects', 'To Do', 'Low', 1, '2025-03-30', NULL, NULL, '["documentation"]', 1);

-- ==========================================
-- Sample Task Checklist Items
-- ==========================================
INSERT INTO task_checklist (task_id, item_text, is_completed, item_order) VALUES
(3, 'Research Stripe API documentation', TRUE, 1),
(3, 'Setup Stripe test account', TRUE, 2),
(3, 'Implement checkout form', TRUE, 3),
(3, 'Add payment processing logic', FALSE, 4),
(3, 'Test with test cards', FALSE, 5),
(3, 'Add error handling', FALSE, 6),

(6, 'Setup D3.js library', TRUE, 1),
(6, 'Create bar chart component', TRUE, 2),
(6, 'Create line chart component', FALSE, 3),
(6, 'Create pie chart component', FALSE, 4),
(6, 'Add responsive design', FALSE, 5);

-- ==========================================
-- Sample Documents (3 documents)
-- ==========================================
INSERT INTO documents (title, slug, content, category, tags, client_id, project_name, status, author_id, published_at) VALUES
('TechCorp Project Kickoff Notes', 'techcorp-project-kickoff-notes', '<h1>TechCorp E-commerce Project Kickoff</h1><p><strong>Date:</strong> January 15, 2025</p><p><strong>Attendees:</strong> John Smith (Client), Admin User, Partner User</p><h2>Key Discussion Points</h2><ul><li>Project timeline: 3.5 months</li><li>Weekly Friday updates required</li><li>Payment gateway: Stripe integration</li><li>Inventory management system needed</li></ul><h2>Next Steps</h2><ol><li>Finalize design mockups by Feb 1</li><li>Database schema by Feb 5</li><li>Begin frontend development Feb 10</li></ol>', 'Meeting Notes', '["kickoff", "e-commerce"]', 1, 'E-commerce Website', 'Published', 1, '2025-01-15 14:30:00'),

('Mobile App Feature Specifications', 'mobile-app-feature-specs', '<h1>StartupHub Mobile App Features</h1><h2>Core Features</h2><ul><li><strong>User Authentication</strong> - Firebase Auth with email/social login</li><li><strong>Profile Management</strong> - Company profiles with logo, description, team</li><li><strong>Networking</strong> - Connect with other startups</li><li><strong>Events</strong> - Discover and RSVP to startup events</li><li><strong>Resources</strong> - Access to guides, templates, tools</li></ul><h2>Technical Requirements</h2><ul><li>React Native for cross-platform</li><li>Firebase for backend services</li><li>AWS S3 for media storage</li><li>Push notifications</li></ul>', 'Proposals', '["mobile", "features"]', 2, 'Mobile App Development', 'Draft', 2, NULL),

('General Development Best Practices', 'development-best-practices', '<h1>Development Best Practices</h1><h2>Code Quality</h2><ul><li>Write clean, readable code</li><li>Follow consistent naming conventions</li><li>Comment complex logic</li><li>Use meaningful variable names</li></ul><h2>Version Control</h2><ul><li>Commit frequently with clear messages</li><li>Use feature branches</li><li>Code review before merging</li><li>Keep main branch stable</li></ul><h2>Testing</h2><ul><li>Write unit tests for critical functions</li><li>Perform integration testing</li><li>Test on multiple devices/browsers</li><li>User acceptance testing before deployment</li></ul>', 'Ideas', '["development", "guidelines"]', NULL, NULL, 'Published', 1, '2025-01-20 10:00:00');

-- ==========================================
-- Success Message
-- ==========================================
SELECT 'Sample data inserted successfully!' AS status;
SELECT '2 Users, 5 Clients, 5 Contact Submissions, 10 Tasks, 3 Documents created' AS summary;
