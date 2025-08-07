<?php
/**
 * Application Initializer
 * Sistema CRM Sindicatos
 */

// Start session
session_start();

// Include configuration
require_once 'config/config.php';
require_once 'config/database.php';

/**
 * Simple autoloader for classes
 */
spl_autoload_register(function ($className) {
    $directories = ['models', 'controllers', 'includes'];
    
    foreach ($directories as $directory) {
        $file = __DIR__ . '/../' . $directory . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Security helper functions
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Authentication helper functions
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php?page=login');
        exit;
    }
}

function require_role($required_roles) {
    require_login();
    if (!in_array($_SESSION['user_role'], (array)$required_roles)) {
        header('Location: index.php?page=dashboard&error=access_denied');
        exit;
    }
}

function get_logged_user() {
    if (is_logged_in()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['user_role'],
            'department' => $_SESSION['user_department'] ?? null
        ];
    }
    return null;
}

/**
 * Utility functions
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

function flash_message($type, $message) {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash_messages() {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function format_date($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    $time = ($time < 1) ? 1 : $time;
    $tokens = array (
        31536000 => 'año',
        2592000 => 'mes',
        604800 => 'semana',
        86400 => 'día',
        3600 => 'hora',
        60 => 'minuto',
        1 => 'segundo'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
}

/**
 * File upload helper
 */
function handle_file_upload($file, $upload_dir = UPLOAD_PATH) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $extension = array_search($mimeType, [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ]);

    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        throw new RuntimeException('Invalid file format.');
    }

    $fileName = sprintf('%s.%s', sha1_file($file['tmp_name']), $extension);
    $uploadPath = $upload_dir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    return [
        'file_name' => $file['name'],
        'file_path' => $uploadPath,
        'file_size' => $file['size'],
        'file_type' => $mimeType
    ];
}

/**
 * Database helper functions
 */
function get_db() {
    static $db = null;
    if ($db === null) {
        $database = new Database();
        $db = $database->getConnection();
    }
    return $db;
}

/**
 * Pagination helper
 */
function paginate($total_records, $page = 1, $records_per_page = 10) {
    $total_pages = ceil($total_records / $records_per_page);
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * $records_per_page;
    
    return [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records,
        'records_per_page' => $records_per_page,
        'offset' => $offset,
        'has_previous' => $page > 1,
        'has_next' => $page < $total_pages,
        'previous_page' => $page - 1,
        'next_page' => $page + 1
    ];
}
?>