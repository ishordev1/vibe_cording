<?php
/**
 * Authentication Controller
 */

require_once 'app/models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($database) {
        $this->userModel = new User($database);
    }
    
    /**
     * Show login page
     */
    public function login() {
        if (isLoggedIn()) {
            redirect('student/dashboard.php');
        }
        
        include 'app/views/auth/login.php';
    }
    
    /**
     * Handle login
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth.php?action=login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlashMessage('error', 'Please fill all fields');
            redirect('auth.php?action=login');
        }

        if (!validateEmail($email)) {
            setFlashMessage('error', 'Invalid email format');
            redirect('auth.php?action=login');
        }

        // Check if admin login
        $user = $this->userModel->getByEmail($email);

        if ($user && $user['user_type'] === 'admin') {
            if ($this->userModel->login($email, $password)) {
                redirect('admin/dashboard.php');
            }
        } elseif ($user && $user['user_type'] === 'student') {
            if ($this->userModel->login($email, $password)) {
                redirect('student/dashboard.php');
            }
        }

        setFlashMessage('error', 'Invalid email or password');
        redirect('auth.php?action=login');
    }
    
    /**
     * Show registration page
     */
    public function register() {
        if (isLoggedIn()) {
            redirect('student/dashboard.php');
        }
        
        include 'app/views/auth/register.php';
    }
    
    /**
     * Handle registration
     */
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('auth.php?action=register');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone = $_POST['phone'] ?? '';

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            setFlashMessage('error', 'Please fill all fields');
            redirect('auth.php?action=register');
        }

        if (!validateEmail($email)) {
            setFlashMessage('error', 'Invalid email format');
            redirect('auth.php?action=register');
        }

        if ($password !== $confirmPassword) {
            setFlashMessage('error', 'Passwords do not match');
            redirect('auth.php?action=register');
        }

        if (strlen($password) < 6) {
            setFlashMessage('error', 'Password must be at least 6 characters');
            redirect('auth.php?action=register');
        }

        if (emailExists($email)) {
            setFlashMessage('error', 'Email already registered');
            redirect('auth.php?action=register');
        }

        $userId = $this->userModel->register($name, $email, $password, $phone);

        if ($userId) {
            setFlashMessage('success', 'Registration successful! Please login.');
            redirect('auth.php?action=login');
        } else {
            setFlashMessage('error', 'Registration failed. Please try again.');
            redirect('auth.php?action=register');
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        setFlashMessage('success', 'Logged out successfully');
        redirect('index.php');
    }
}
?>
