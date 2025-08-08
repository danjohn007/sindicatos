<?php
/**
 * Dashboard Controller
 * Sistema CRM Sindicatos
 */

class DashboardController {
    private $request;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->request = new Request($db);
    }

    public function index() {
        // Initialize current user for the dashboard view to prevent undefined variable warnings
        $current_user = get_logged_user();
        if (!$current_user) {
            // Fallback if user data is not available - redirect to login
            header('Location: index.php?page=login');
            exit;
        }
        
        // Get dashboard statistics
        $stats = $this->request->getDashboardStats();
        
        // Get stats by department
        $dept_stats = $this->request->getStatsByDepartment();
        $department_data = [];
        while ($row = $dept_stats->fetch(PDO::FETCH_ASSOC)) {
            $department_data[] = $row;
        }
        
        // Get stats by request type
        $type_stats = $this->request->getStatsByType();
        $type_data = [];
        while ($row = $type_stats->fetch(PDO::FETCH_ASSOC)) {
            $type_data[] = $row;
        }
        
        // Get recent requests
        $recent_requests = $this->request->getRecentRequests(10);
        $recent_data = [];
        while ($row = $recent_requests->fetch(PDO::FETCH_ASSOC)) {
            $recent_data[] = $row;
        }
        
        // Get my assigned requests (for non-admin users)
        $my_requests = [];
        if ($current_user['role'] !== 'admin') {
            $filters = ['assigned_to' => $current_user['id']];
            $my_requests_result = $this->request->read($filters, 5);
            while ($row = $my_requests_result->fetch(PDO::FETCH_ASSOC)) {
                $my_requests[] = $row;
            }
        }
        
        // Filter data based on user role and department
        if ($current_user['role'] === 'representante' && $current_user['department']) {
            // Department representatives only see their department data
            $department_data = array_filter($department_data, function($dept) use ($current_user) {
                return $dept['department'] === $current_user['department'];
            });
        }
        
        $page_title = 'Panel de Control';
        include 'views/layout/header.php';
        include 'views/dashboard/index.php';
        include 'views/layout/footer.php';
    }

    private function getAlerts() {
        $alerts = [];
        
        // Check for critical requests without assignment
        $critical_unassigned = $this->request->read([
            'priority' => 'critica',
            'assigned_to' => ''
        ]);
        
        if ($critical_unassigned->rowCount() > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Hay ' . $critical_unassigned->rowCount() . ' solicitudes críticas sin asignar'
            ];
        }
        
        // Check for requests older than 48 hours without response
        $old_requests = $this->request->read([
            'status' => 'recibido'
        ]);
        
        $old_count = 0;
        while ($row = $old_requests->fetch(PDO::FETCH_ASSOC)) {
            $created = strtotime($row['created_at']);
            if ((time() - $created) > (48 * 60 * 60)) {
                $old_count++;
            }
        }
        
        if ($old_count > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Hay $old_count solicitudes con más de 48 horas sin respuesta"
            ];
        }
        
        return $alerts;
    }
}
?>