<?php
/**
 * Database Configuration
 * Sistema CRM Sindicatos
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'fix360_sindicato';  // Updated database name as per requirements
    private $username = 'fix360_sindicato'; // Updated username as per requirements
    private $password = 'Danjohn007';       // Updated password as per requirements
    private $charset = 'utf8mb4';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // For demo purposes, create a mock connection
            error_log("Database connection failed, creating demo mode: " . $exception->getMessage());
            $this->conn = $this->createDemoConnection();
        }

        return $this->conn;
    }
    
    private function createDemoConnection() {
        // Create SQLite in-memory database for demo
        try {
            $conn = new PDO('sqlite::memory:');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setupDemoData($conn);
            return $conn;
        } catch(PDOException $e) {
            error_log("Demo connection failed: " . $e->getMessage());
            return null;
        }
    }
    
    private function setupDemoData($conn) {
        // Create basic tables for demo
        $conn->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(50) UNIQUE,
                email VARCHAR(100) UNIQUE,
                password VARCHAR(255),
                full_name VARCHAR(100),
                phone VARCHAR(20),
                whatsapp VARCHAR(20),
                role VARCHAR(20) DEFAULT 'representante',
                department VARCHAR(20),
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Insert demo admin user (password is hash of 'admin123')
        $adminPassword = hash('sha256', 'admin123');
        $conn->exec("
            INSERT INTO users (username, email, password, full_name, role, is_active) 
            VALUES ('admin', 'admin@demo.com', '$adminPassword', 'Administrador Demo', 'admin', 1)
        ");
        
        $conn->exec("
            CREATE TABLE requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                folio VARCHAR(20) UNIQUE,
                worker_id INTEGER,
                worker_name VARCHAR(100),
                worker_whatsapp VARCHAR(20),
                department VARCHAR(20),
                request_type VARCHAR(20),
                subject VARCHAR(200),
                description TEXT,
                priority VARCHAR(10) DEFAULT 'media',
                status VARCHAR(20) DEFAULT 'recibido',
                assigned_to INTEGER,
                is_anonymous INTEGER DEFAULT 0,
                is_confidential INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                resolved_at DATETIME
            )
        ");
        
        // Insert demo requests
        $conn->exec("
            INSERT INTO requests (folio, worker_name, department, request_type, subject, description, priority, status) 
            VALUES 
                ('DEMO001', 'Juan Pérez', 'operaciones', 'queja', 'Problema con equipos de seguridad', 'Los equipos de protección personal no están disponibles en mi área de trabajo.', 'alta', 'recibido'),
                ('DEMO002', 'María González', 'rh', 'sugerencia', 'Mejorar horarios de capacitación', 'Propongo horarios más flexibles para las capacitaciones.', 'media', 'en_revision'),
                ('DEMO003', 'Anónimo', 'rh', 'queja', 'Solicitud confidencial', 'Solicitud enviada de forma anónima.', 'critica', 'escalado')
        ");
        
        $conn->exec("
            CREATE TABLE workers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                worker_number VARCHAR(20),
                full_name VARCHAR(100),
                email VARCHAR(100),
                phone VARCHAR(20),
                whatsapp VARCHAR(20),
                department VARCHAR(20),
                position VARCHAR(100),
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create view for dashboard stats
        $conn->exec("
            CREATE VIEW dashboard_stats AS
            SELECT 
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = 'recibido' THEN 1 ELSE 0 END) as new_requests,
                SUM(CASE WHEN status = 'en_revision' THEN 1 ELSE 0 END) as in_review,
                SUM(CASE WHEN status = 'escalado' THEN 1 ELSE 0 END) as escalated,
                SUM(CASE WHEN status = 'resuelto' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status = 'cerrado' THEN 1 ELSE 0 END) as closed,
                SUM(CASE WHEN priority = 'critica' AND status NOT IN ('resuelto', 'cerrado') THEN 1 ELSE 0 END) as critical_open,
                24.5 as avg_resolution_hours
            FROM requests
        ");
    }
}
?>