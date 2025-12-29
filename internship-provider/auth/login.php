<?php
/**
 * User/Admin Login
 * Handles login for both admin and intern users
 */

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/validation.php';

// If already logged in, redirect
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: " . APP_URL . "/admin/dashboard.php");
    } else {
        header("Location: " . APP_URL . "/user/dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? 'intern');
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        // Get user from database
        $stmt = $conn->prepare("SELECT id, email, password, full_name, role, status FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                $error = 'Your account is ' . $user['status'];
            }
            // Verify password
            elseif (password_verify($password, $user['password'])) {
                // Password correct - create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                // Update last login timestamp
                $update_login = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_login->bind_param("i", $user['id']);
                $update_login->execute();
                $update_login->close();
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: " . APP_URL . "/admin/dashboard.php");
                } else {
                    header("Location: " . APP_URL . "/user/dashboard.php");
                }
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Internship Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            max-width: 450px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #667eea;
            font-weight: 700;
        }
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .role-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        .role-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>🎓 Internship Platform</h1>
                <p class="text-muted">Professional Internship Management</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="mb-4">
                    <label class="form-label">Login As</label>
                    <div class="role-selector">
                        <label class="role-btn active" data-role="intern">
                            <input type="radio" name="role" value="intern" checked style="display:none;">
                            👨‍🎓 Intern
                        </label>
                        <label class="role-btn" data-role="admin">
                            <input type="radio" name="role" value="admin" style="display:none;">
                            👨‍💼 Admin
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-lg" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">Login</button>
            </form>
            
            <div class="alert alert-info">
                <strong>Demo Credentials:</strong><br>
                Admin: admin@internship.com / admin123<br>
                <small class="text-muted">Register as new intern to test user login</small>
            </div>
            
            <p class="text-center mt-4">
                Don't have an account? <a href="register.php" class="fw-bold">Register here</a>
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Role selector functionality
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>
