<?php
/**
 * Authentication Controller
 * Sistema CRM Sindicatos
 */

class AuthController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }

    public function showLogin() {
        // If already logged in, redirect to dashboard
        if (is_logged_in()) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $error = $_GET['error'] ?? '';
        include 'views/auth/login.php';
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=login');
            exit;
        }

        $username = sanitize_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($username) || empty($password)) {
            header('Location: index.php?page=login&error=missing_fields');
            exit;
        }

        $user_data = $this->user->authenticate($username, $password);

        if ($user_data) {
            // Set session variables
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['full_name'] = $user_data['full_name'];
            $_SESSION['user_role'] = $user_data['role'];
            $_SESSION['user_department'] = $user_data['department'];
            $_SESSION['login_time'] = time();

            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                // In a real application, you'd store this token in the database
            }

            // Log successful login
            error_log("User {$user_data['username']} logged in successfully");

            // Redirect to dashboard
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            // Log failed login attempt
            error_log("Failed login attempt for username: $username");
            
            header('Location: index.php?page=login&error=invalid_credentials');
            exit;
        }
    }

    public function logout() {
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Log logout
        if (isset($_SESSION['username'])) {
            error_log("User {$_SESSION['username']} logged out");
        }

        // Destroy session
        session_destroy();
        
        header('Location: index.php?page=login&message=logged_out');
        exit;
    }
}
?>