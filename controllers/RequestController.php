<?php
/**
 * Request Controller
 * Sistema CRM Sindicatos
 */

class RequestController {
    private $request;
    private $worker;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->request = new Request($db);
        $this->worker = new Worker($db);
    }

    public function index() {
        $current_user = get_current_user();
        
        // Get filters from URL
        $filters = [];
        if (!empty($_GET['department'])) $filters['department'] = $_GET['department'];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        if (!empty($_GET['request_type'])) $filters['request_type'] = $_GET['request_type'];
        if (!empty($_GET['priority'])) $filters['priority'] = $_GET['priority'];
        if (!empty($_GET['assigned_to'])) $filters['assigned_to'] = $_GET['assigned_to'];
        if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
        if (!empty($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
        if (!empty($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];

        // Apply role-based filtering
        if ($current_user['role'] === 'representante' && $current_user['department']) {
            $filters['department'] = $current_user['department'];
        }

        // Pagination
        $page = $_GET['p'] ?? 1;
        $records_per_page = 20;
        $total_records = $this->request->getCount($filters);
        $pagination = paginate($total_records, $page, $records_per_page);

        // Get requests
        $stmt = $this->request->read($filters, $records_per_page, $pagination['offset']);
        $requests = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $requests[] = $row;
        }

        $page_title = 'Gestión de Solicitudes';
        include 'views/layout/header.php';
        include 'views/requests/index.php';
        include 'views/layout/footer.php';
    }

    public function create() {
        $workers = [];
        $stmt = $this->worker->read();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $workers[] = $row;
        }

        $page_title = 'Nueva Solicitud';
        include 'views/layout/header.php';
        include 'views/requests/create.php';
        include 'views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=requests');
            exit;
        }

        // Validate CSRF token
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            flash_message('danger', 'Token de seguridad inválido.');
            header('Location: index.php?page=requests&action=create');
            exit;
        }

        try {
            // Sanitize and validate input
            $worker_id = !empty($_POST['worker_id']) ? (int)$_POST['worker_id'] : null;
            $worker_name = sanitize_input($_POST['worker_name'] ?? '');
            $worker_whatsapp = sanitize_input($_POST['worker_whatsapp'] ?? '');
            $department = sanitize_input($_POST['department'] ?? '');
            $request_type = sanitize_input($_POST['request_type'] ?? '');
            $subject = sanitize_input($_POST['subject'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $priority = sanitize_input($_POST['priority'] ?? 'media');
            $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
            $is_confidential = isset($_POST['is_confidential']) ? 1 : 0;

            // Validation
            if (empty($worker_name) || empty($department) || empty($request_type) || empty($subject) || empty($description)) {
                flash_message('danger', 'Todos los campos obligatorios deben ser completados.');
                header('Location: index.php?page=requests&action=create');
                exit;
            }

            // Set request properties
            $this->request->worker_id = $worker_id;
            $this->request->worker_name = $is_anonymous ? 'Anónimo' : $worker_name;
            $this->request->worker_whatsapp = $worker_whatsapp;
            $this->request->department = $department;
            $this->request->request_type = $request_type;
            $this->request->subject = $subject;
            $this->request->description = $description;
            $this->request->priority = $priority;
            $this->request->is_anonymous = $is_anonymous;
            $this->request->is_confidential = $is_confidential;

            if ($this->request->create()) {
                // Handle file uploads
                if (!empty($_FILES['attachments']['name'][0])) {
                    $this->handleFileUploads($_FILES['attachments']);
                }

                // Log the creation
                $current_user = get_current_user();
                $this->request->logUpdate($current_user['id'], 'comment', 'Solicitud creada');

                // Auto-assign based on department (simple rule)
                $this->autoAssignRequest();

                flash_message('success', "Solicitud creada exitosamente. Folio: {$this->request->folio}");
                header('Location: index.php?page=requests&action=view&id=' . $this->request->id);
                exit;
            } else {
                flash_message('danger', 'Error al crear la solicitud.');
                header('Location: index.php?page=requests&action=create');
                exit;
            }

        } catch (Exception $e) {
            error_log("Error creating request: " . $e->getMessage());
            flash_message('danger', 'Error al procesar la solicitud.');
            header('Location: index.php?page=requests&action=create');
            exit;
        }
    }

    public function view($id) {
        if (!$id) {
            header('Location: index.php?page=requests&error=invalid_request');
            exit;
        }

        $this->request->id = $id;
        $request_data = $this->request->readOne();

        if (!$request_data) {
            header('Location: index.php?page=requests&error=not_found');
            exit;
        }

        // Check permissions
        $current_user = get_current_user();
        if ($current_user['role'] === 'representante' && 
            $current_user['department'] !== $request_data['department']) {
            header('Location: index.php?page=requests&error=access_denied');
            exit;
        }

        // Get updates/comments
        $updates_stmt = $this->request->getUpdates();
        $updates = [];
        while ($row = $updates_stmt->fetch(PDO::FETCH_ASSOC)) {
            $updates[] = $row;
        }

        // Get attachments
        $attachments_stmt = $this->request->getAttachments();
        $attachments = [];
        while ($row = $attachments_stmt->fetch(PDO::FETCH_ASSOC)) {
            $attachments[] = $row;
        }

        $page_title = 'Ver Solicitud - ' . $request_data['folio'];
        include 'views/layout/header.php';
        include 'views/requests/view.php';
        include 'views/layout/footer.php';
    }

    public function edit($id) {
        if (!$id) {
            header('Location: index.php?page=requests&error=invalid_request');
            exit;
        }

        $this->request->id = $id;
        $request_data = $this->request->readOne();

        if (!$request_data) {
            header('Location: index.php?page=requests&error=not_found');
            exit;
        }

        // Check permissions
        $current_user = get_current_user();
        if ($current_user['role'] === 'representante' && 
            $current_user['department'] !== $request_data['department']) {
            header('Location: index.php?page=requests&error=access_denied');
            exit;
        }

        // Get available users for assignment
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $users_stmt = $user->read();
        $users = [];
        while ($row = $users_stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['is_active']) {
                $users[] = $row;
            }
        }

        $page_title = 'Editar Solicitud - ' . $request_data['folio'];
        include 'views/layout/header.php';
        include 'views/requests/edit.php';
        include 'views/layout/footer.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: index.php?page=requests');
            exit;
        }

        // Validate CSRF token
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            flash_message('danger', 'Token de seguridad inválido.');
            header('Location: index.php?page=requests&action=edit&id=' . $id);
            exit;
        }

        $this->request->id = $id;
        if (!$this->request->readOne()) {
            header('Location: index.php?page=requests&error=not_found');
            exit;
        }

        try {
            $old_status = $this->request->status;
            
            // Update request properties
            $this->request->department = sanitize_input($_POST['department'] ?? '');
            $this->request->request_type = sanitize_input($_POST['request_type'] ?? '');
            $this->request->subject = sanitize_input($_POST['subject'] ?? '');
            $this->request->description = sanitize_input($_POST['description'] ?? '');
            $this->request->priority = sanitize_input($_POST['priority'] ?? '');
            $this->request->status = sanitize_input($_POST['status'] ?? '');
            $this->request->assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
            $this->request->is_confidential = isset($_POST['is_confidential']) ? 1 : 0;

            if ($this->request->update()) {
                // Log status change if it occurred
                if ($old_status !== $this->request->status) {
                    $current_user = get_current_user();
                    $this->request->logUpdate($current_user['id'], 'status_change', 
                        "Estado cambiado de '$old_status' a '{$this->request->status}'", 
                        $old_status, $this->request->status);
                }

                flash_message('success', 'Solicitud actualizada exitosamente.');
                header('Location: index.php?page=requests&action=view&id=' . $id);
                exit;
            } else {
                flash_message('danger', 'Error al actualizar la solicitud.');
                header('Location: index.php?page=requests&action=edit&id=' . $id);
                exit;
            }

        } catch (Exception $e) {
            error_log("Error updating request: " . $e->getMessage());
            flash_message('danger', 'Error al procesar la actualización.');
            header('Location: index.php?page=requests&action=edit&id=' . $id);
            exit;
        }
    }

    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: index.php?page=requests');
            exit;
        }

        $this->request->id = $id;
        if (!$this->request->readOne()) {
            header('Location: index.php?page=requests&error=not_found');
            exit;
        }

        $new_status = sanitize_input($_POST['status'] ?? '');
        $current_user = get_current_user();

        if ($this->request->updateStatus($new_status, $current_user['id'])) {
            flash_message('success', 'Estado actualizado exitosamente.');
        } else {
            flash_message('danger', 'Error al actualizar el estado.');
        }

        header('Location: index.php?page=requests&action=view&id=' . $id);
        exit;
    }

    public function addComment($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: index.php?page=requests');
            exit;
        }

        $this->request->id = $id;
        if (!$this->request->readOne()) {
            header('Location: index.php?page=requests&error=not_found');
            exit;
        }

        $comment = sanitize_input($_POST['comment'] ?? '');
        $is_internal = isset($_POST['is_internal']) ? 1 : 0;
        $current_user = get_current_user();

        if (!empty($comment)) {
            if ($this->request->logUpdate($current_user['id'], 'comment', $comment, null, null, $is_internal)) {
                flash_message('success', 'Comentario agregado exitosamente.');
            } else {
                flash_message('danger', 'Error al agregar el comentario.');
            }
        }

        header('Location: index.php?page=requests&action=view&id=' . $id);
        exit;
    }

    private function handleFileUploads($files) {
        $upload_dir = UPLOAD_PATH . 'requests/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];

                try {
                    $file_info = handle_file_upload($file, $upload_dir);
                    $this->request->addAttachment($file_info);
                } catch (Exception $e) {
                    error_log("File upload error: " . $e->getMessage());
                }
            }
        }
    }

    private function autoAssignRequest() {
        // Simple auto-assignment logic based on department
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        
        // Try to find a representative for the department
        $stmt = $user->getUsersByDepartment($this->request->department);
        if ($stmt->rowCount() > 0) {
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Assign to the first available representative
            foreach ($users as $user_data) {
                if ($user_data['role'] === 'representante') {
                    $current_user = get_current_user();
                    $this->request->assign($user_data['id'], $current_user['id']);
                    break;
                }
            }
        }
    }
}
?>