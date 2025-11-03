<?php
/**
 * Clients API
 * CRUD operations for clients and projects
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();

    switch ($method) {
        case 'GET':
            // Get client(s)
            if (isset($_GET['id'])) {
                // Get single client
                $id = (int) $_GET['id'];
                $sql = "SELECT * FROM clients WHERE id = ?";
                $client = $db->fetchOne($sql, [$id]);

                if (!$client) {
                    send_error('Client not found', null, 404);
                }

                // Decode JSON fields
                if ($client['tech_stack']) {
                    $client['tech_stack'] = json_decode($client['tech_stack'], true);
                }

                send_success($client, 'Client retrieved successfully');
            } else {
                // Get all clients with optional filters
                $where = [];
                $params = [];

                if (isset($_GET['status']) && $_GET['status'] !== '') {
                    $where[] = "project_status = ?";
                    $params[] = $_GET['status'];
                }

                if (isset($_GET['search']) && $_GET['search'] !== '') {
                    $search = '%' . $_GET['search'] . '%';
                    $where[] = "(client_name LIKE ? OR company_name LIKE ? OR project_name LIKE ?)";
                    $params[] = $search;
                    $params[] = $search;
                    $params[] = $search;
                }

                $sql = "SELECT * FROM clients";
                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }
                $sql .= " ORDER BY created_at DESC";

                $clients = $db->fetchAll($sql, $params);

                // Decode JSON fields
                foreach ($clients as &$client) {
                    if ($client['tech_stack']) {
                        $client['tech_stack'] = json_decode($client['tech_stack'], true);
                    }
                }

                send_success($clients, 'Clients retrieved successfully');
            }
            break;

        case 'POST':
            // Create new client
            $data = get_json_input();

            // Validate required fields
            if (!validate_required($data['client_name'] ?? '')) {
                send_error('Client name is required', null, 400);
            }

            if (!validate_required($data['project_name'] ?? '')) {
                send_error('Project name is required', null, 400);
            }

            // Validate email if provided
            if (!empty($data['email']) && !validate_email($data['email'])) {
                send_error('Invalid email address', null, 400);
            }

            // Prepare tech stack JSON
            $techStack = null;
            if (isset($data['tech_stack'])) {
                if (is_string($data['tech_stack'])) {
                    $techStack = json_encode(array_map('trim', explode(',', $data['tech_stack'])));
                } else if (is_array($data['tech_stack'])) {
                    $techStack = json_encode($data['tech_stack']);
                }
            }

            // Insert client
            $sql = "INSERT INTO clients (
                        client_name, company_name, email, phone, project_name,
                        project_status, project_description, start_date, deadline,
                        additional_notes, tech_stack
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $db->execute($sql, [
                sanitize_string($data['client_name']),
                sanitize_string($data['company_name'] ?? ''),
                sanitize_string($data['email'] ?? ''),
                sanitize_string($data['phone'] ?? ''),
                sanitize_string($data['project_name']),
                $data['project_status'] ?? 'Active',
                $data['project_description'] ?? '',
                $data['start_date'] ?? null,
                $data['deadline'] ?? null,
                $data['additional_notes'] ?? '',
                $techStack
            ]);

            $clientId = $db->lastInsertId();

            send_success(['id' => $clientId], 'Client created successfully', 201);
            break;

        case 'PUT':
            // Update client
            $data = get_json_input();

            if (!isset($data['id'])) {
                send_error('Client ID is required', null, 400);
            }

            $id = (int) $data['id'];

            // Check if client exists
            $existing = $db->fetchOne("SELECT id FROM clients WHERE id = ?", [$id]);
            if (!$existing) {
                send_error('Client not found', null, 404);
            }

            // Validate email if provided
            if (!empty($data['email']) && !validate_email($data['email'])) {
                send_error('Invalid email address', null, 400);
            }

            // Prepare tech stack JSON
            $techStack = null;
            if (isset($data['tech_stack'])) {
                if (is_string($data['tech_stack'])) {
                    $techStack = json_encode(array_map('trim', explode(',', $data['tech_stack'])));
                } else if (is_array($data['tech_stack'])) {
                    $techStack = json_encode($data['tech_stack']);
                }
            }

            // Build update query dynamically
            $updates = [];
            $params = [];

            $fields = [
                'client_name', 'company_name', 'email', 'phone', 'project_name',
                'project_status', 'project_description', 'start_date', 'deadline',
                'additional_notes'
            ];

            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = sanitize_string($data[$field]);
                }
            }

            if ($techStack !== null) {
                $updates[] = "tech_stack = ?";
                $params[] = $techStack;
            }

            if (empty($updates)) {
                send_error('No fields to update', null, 400);
            }

            $params[] = $id;

            $sql = "UPDATE clients SET " . implode(", ", $updates) . " WHERE id = ?";
            $db->execute($sql, $params);

            send_success(['id' => $id], 'Client updated successfully');
            break;

        case 'DELETE':
            // Delete client
            $data = get_json_input();

            if (!isset($data['id'])) {
                send_error('Client ID is required', null, 400);
            }

            $id = (int) $data['id'];

            // Check if client exists
            $existing = $db->fetchOne("SELECT id FROM clients WHERE id = ?", [$id]);
            if (!$existing) {
                send_error('Client not found', null, 404);
            }

            // Delete client (will cascade to related tasks)
            $sql = "DELETE FROM clients WHERE id = ?";
            $db->execute($sql, [$id]);

            send_success([], 'Client deleted successfully');
            break;

        default:
            send_error('Method not allowed', null, 405);
    }

} catch (Exception $e) {
    error_log("Clients API error: " . $e->getMessage());
    send_error('Operation failed', $e->getMessage(), 500);
}
