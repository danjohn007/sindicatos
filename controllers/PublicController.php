<?php
/**
 * Public Controller
 * Sistema CRM Sindicatos
 */

class PublicController {
    private $request;
    private $worker;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->request = new Request($db);
        $this->worker = new Worker($db);
    }

    public function showForm() {
        $page_title = 'Enviar Solicitud - ' . APP_NAME;
        include 'views/public/form.php';
    }

    public function submitRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=public_form');
            exit;
        }

        // Basic rate limiting check (simple implementation)
        $this->checkRateLimit();

        try {
            // Sanitize and validate input
            $worker_name = sanitize_input($_POST['worker_name'] ?? '');
            $worker_whatsapp = sanitize_input($_POST['worker_whatsapp'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            $department = sanitize_input($_POST['department'] ?? '');
            $request_type = sanitize_input($_POST['request_type'] ?? '');
            $subject = sanitize_input($_POST['subject'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

            // Validation
            if (empty($worker_name) || empty($department) || empty($request_type) || empty($subject) || empty($description)) {
                $this->showFormWithError('Todos los campos obligatorios deben ser completados.');
                return;
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->showFormWithError('El formato del email no es válido.');
                return;
            }

            // Check if worker exists by WhatsApp
            $worker_id = null;
            if (!empty($worker_whatsapp) && !$is_anonymous) {
                $worker_data = $this->worker->findByWhatsapp($worker_whatsapp);
                if ($worker_data) {
                    $worker_id = $worker_data['id'];
                    $worker_name = $worker_data['full_name'];
                    $department = $worker_data['department'];
                }
            }

            // Set request properties
            $this->request->worker_id = $worker_id;
            $this->request->worker_name = $is_anonymous ? 'Anónimo' : $worker_name;
            $this->request->worker_whatsapp = $worker_whatsapp;
            $this->request->department = $department;
            $this->request->request_type = $request_type;
            $this->request->subject = $subject;
            $this->request->description = $description;
            $this->request->priority = 'media'; // Default priority for public requests
            $this->request->is_anonymous = $is_anonymous;
            $this->request->is_confidential = 0; // Public requests are not confidential by default

            if ($this->request->create()) {
                // Handle file uploads
                if (!empty($_FILES['attachments']['name'][0])) {
                    $this->handleFileUploads($_FILES['attachments']);
                }

                // Log the creation
                $this->request->logUpdate(1, 'comment', 'Solicitud enviada desde formulario público');

                // Show success page
                $this->showSuccess($this->request->folio);
            } else {
                $this->showFormWithError('Error al enviar la solicitud. Por favor intente nuevamente.');
            }

        } catch (Exception $e) {
            error_log("Error in public form submission: " . $e->getMessage());
            $this->showFormWithError('Error al procesar la solicitud. Por favor intente nuevamente.');
        }
    }

    private function showFormWithError($error) {
        $error_message = $error;
        $page_title = 'Enviar Solicitud - ' . APP_NAME;
        include 'views/public/form.php';
    }

    private function showSuccess($folio) {
        $success_folio = $folio;
        include 'views/public/success.php';
    }

    private function handleFileUploads($files) {
        $upload_dir = UPLOAD_PATH . 'public_requests/';
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
                    error_log("Public file upload error: " . $e->getMessage());
                }
            }
        }
    }

    private function checkRateLimit() {
        // Simple rate limiting: max 5 submissions per IP per hour
        $ip = $_SERVER['REMOTE_ADDR'];
        $cache_file = sys_get_temp_dir() . "/rate_limit_" . md5($ip);
        
        $submissions = [];
        if (file_exists($cache_file)) {
            $submissions = json_decode(file_get_contents($cache_file), true) ?: [];
        }
        
        // Clean old submissions (older than 1 hour)
        $one_hour_ago = time() - 3600;
        $submissions = array_filter($submissions, function($timestamp) use ($one_hour_ago) {
            return $timestamp > $one_hour_ago;
        });
        
        if (count($submissions) >= 5) {
            $this->showFormWithError('Ha superado el límite de envíos por hora. Por favor intente más tarde.');
            exit;
        }
        
        // Add current submission
        $submissions[] = time();
        file_put_contents($cache_file, json_encode($submissions));
    }
}
?>