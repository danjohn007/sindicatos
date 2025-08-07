<?php
/**
 * Sistema CRM Sindicatos
 * Main Application Entry Point
 */

// Include the application initializer
require_once 'includes/functions.php';

// Get the requested page
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

// Handle logout
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

// Public pages that don't require login
$public_pages = ['login', 'public_form', 'faq'];

// Check authentication for protected pages
if (!in_array($page, $public_pages) && !is_logged_in()) {
    header('Location: index.php?page=login');
    exit;
}

// Route to appropriate controller
try {
    switch ($page) {
        case 'login':
            require_once 'controllers/AuthController.php';
            $controller = new AuthController();
            if ($action === 'authenticate') {
                $controller->authenticate();
            } else {
                $controller->showLogin();
            }
            break;

        case 'dashboard':
            require_login();
            require_once 'controllers/DashboardController.php';
            $controller = new DashboardController();
            $controller->index();
            break;

        case 'requests':
            require_login();
            require_once 'controllers/RequestController.php';
            $controller = new RequestController();
            
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'store':
                    $controller->store();
                    break;
                case 'view':
                    $controller->view($_GET['id'] ?? null);
                    break;
                case 'edit':
                    require_role(['admin', 'representante', 'asesor_legal']);
                    $controller->edit($_GET['id'] ?? null);
                    break;
                case 'update':
                    require_role(['admin', 'representante', 'asesor_legal']);
                    $controller->update($_GET['id'] ?? null);
                    break;
                case 'assign':
                    require_role(['admin', 'representante']);
                    $controller->assign($_GET['id'] ?? null);
                    break;
                case 'update_status':
                    require_role(['admin', 'representante', 'asesor_legal']);
                    $controller->updateStatus($_GET['id'] ?? null);
                    break;
                case 'add_comment':
                    $controller->addComment($_GET['id'] ?? null);
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        case 'public_form':
            require_once 'controllers/PublicController.php';
            $controller = new PublicController();
            if ($action === 'submit') {
                $controller->submitRequest();
            } else {
                $controller->showForm();
            }
            break;

        case 'users':
            require_role(['admin']);
            require_once 'controllers/UserController.php';
            $controller = new UserController();
            
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'store':
                    $controller->store();
                    break;
                case 'edit':
                    $controller->edit($_GET['id'] ?? null);
                    break;
                case 'update':
                    $controller->update($_GET['id'] ?? null);
                    break;
                case 'delete':
                    $controller->delete($_GET['id'] ?? null);
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        case 'workers':
            require_role(['admin', 'representante']);
            require_once 'controllers/WorkerController.php';
            $controller = new WorkerController();
            
            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'store':
                    $controller->store();
                    break;
                case 'edit':
                    $controller->edit($_GET['id'] ?? null);
                    break;
                case 'update':
                    $controller->update($_GET['id'] ?? null);
                    break;
                case 'search':
                    $controller->search();
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        case 'reports':
            require_role(['admin', 'representante']);
            require_once 'controllers/ReportController.php';
            $controller = new ReportController();
            
            switch ($action) {
                case 'generate':
                    $controller->generate();
                    break;
                case 'export':
                    $controller->export();
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        case 'faq':
            require_once 'controllers/FaqController.php';
            $controller = new FaqController();
            
            switch ($action) {
                case 'create':
                    require_role(['admin', 'representante']);
                    $controller->create();
                    break;
                case 'store':
                    require_role(['admin', 'representante']);
                    $controller->store();
                    break;
                case 'edit':
                    require_role(['admin', 'representante']);
                    $controller->edit($_GET['id'] ?? null);
                    break;
                case 'update':
                    require_role(['admin', 'representante']);
                    $controller->update($_GET['id'] ?? null);
                    break;
                default:
                    $controller->index();
                    break;
            }
            break;

        case 'profile':
            require_login();
            require_once 'controllers/ProfileController.php';
            $controller = new ProfileController();
            
            if ($action === 'update') {
                $controller->update();
            } else {
                $controller->index();
            }
            break;

        case 'api':
            require_once 'controllers/ApiController.php';
            $controller = new ApiController();
            $controller->handle();
            break;

        default:
            if (is_logged_in()) {
                header('Location: index.php?page=dashboard');
            } else {
                header('Location: index.php?page=login');
            }
            exit;
    }

} catch (Exception $e) {
    error_log("Application Error: " . $e->getMessage());
    
    if ($_SERVER['SERVER_NAME'] === 'localhost') {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    } else {
        echo "<div class='alert alert-danger'>Ha ocurrido un error. Por favor contacte al administrador.</div>";
    }
}
?>