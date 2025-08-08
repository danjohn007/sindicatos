<?php
/**
 * Application Configuration
 * Sistema CRM Sindicatos
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'fix360_sindicato');      // Updated to match requirements
define('DB_USER', 'fix360_sindicato');      // Updated to match requirements  
define('DB_PASS', 'Danjohn007');            // Updated to match requirements

// Application settings
define('APP_NAME', 'Sistema CRM Sindicatos');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'https://fix360.app/sindicato/');  // Updated base URL as per requirements

// Upload settings
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif']);

// Email settings (for notifications)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Security
define('HASH_ALGORITHM', 'sha256');
define('SESSION_LIFETIME', 3600); // 1 hour

// Request types
define('REQUEST_TYPES', [
    'queja' => 'Queja',
    'sugerencia' => 'Sugerencia', 
    'peticion' => 'Petición',
    'reconocimiento' => 'Reconocimiento',
    'otro' => 'Otro'
]);

// Request status
define('REQUEST_STATUS', [
    'recibido' => 'Recibido',
    'en_revision' => 'En revisión',
    'escalado' => 'Escalado',
    'resuelto' => 'Resuelto',
    'cerrado' => 'Cerrado con seguimiento'
]);

// User roles
define('USER_ROLES', [
    'admin' => 'Administrador General',
    'representante' => 'Representante de Departamento',
    'asesor_legal' => 'Asesor Legal',
    'psicologo' => 'Psicólogo Sindical',
    'observador' => 'Observador Externo'
]);

// Departments
define('DEPARTMENTS', [
    'rh' => 'Recursos Humanos',
    'operaciones' => 'Operaciones',
    'mantenimiento' => 'Mantenimiento',
    'seguridad' => 'Seguridad',
    'legal' => 'Legal',
    'administrativo' => 'Administrativo'
]);

// Timezone
date_default_timezone_set('America/Mexico_City');

// Error reporting
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>