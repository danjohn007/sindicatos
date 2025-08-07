<?php
/**
 * API Controller
 * Sistema CRM Sindicatos
 */

class ApiController {
    private $worker;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->worker = new Worker($db);
    }

    public function handle() {
        // Set JSON header
        header('Content-Type: application/json');
        
        $action = $_GET['action'] ?? '';
        
        try {
            switch ($action) {
                case 'search_workers':
                    $this->searchWorkers();
                    break;
                
                case 'get_worker_by_whatsapp':
                    $this->getWorkerByWhatsapp();
                    break;
                
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Action not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
            error_log("API Error: " . $e->getMessage());
        }
    }

    private function searchWorkers() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Basic CSRF protection for logged-in users
        if (is_logged_in() && !validate_csrf_token($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            return;
        }

        $term = sanitize_input($_POST['term'] ?? '');
        
        if (strlen($term) < 2) {
            echo json_encode([]);
            return;
        }

        $stmt = $this->worker->searchWorkers($term);
        $workers = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $workers[] = [
                'id' => $row['id'],
                'worker_number' => $row['worker_number'],
                'full_name' => $row['full_name'],
                'department' => $row['department'],
                'whatsapp' => $row['whatsapp'],
                'email' => $row['email']
            ];
        }

        echo json_encode($workers);
    }

    private function getWorkerByWhatsapp() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $whatsapp = sanitize_input($_POST['whatsapp'] ?? '');
        
        if (empty($whatsapp)) {
            echo json_encode(['error' => 'WhatsApp number required']);
            return;
        }

        $worker = $this->worker->findByWhatsapp($whatsapp);
        
        if ($worker) {
            echo json_encode([
                'found' => true,
                'worker' => [
                    'id' => $worker['id'],
                    'worker_number' => $worker['worker_number'],
                    'full_name' => $worker['full_name'],
                    'department' => $worker['department'],
                    'email' => $worker['email']
                ]
            ]);
        } else {
            echo json_encode(['found' => false]);
        }
    }
}
?>