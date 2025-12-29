<?php
/**
 * User Registration
 * Allows new interns to register on the platform
 */

require_once '../includes/config.php';
require_once '../includes/validation.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and validate form data
    $data = [
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'full_name' => sanitizeInput($_POST['full_name'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'date_of_birth' => sanitizeInput($_POST['date_of_birth'] ?? ''),
        'gender' => sanitizeInput($_POST['gender'] ?? ''),
        'address' => sanitizeInput($_POST['address'] ?? ''),
        'city' => sanitizeInput($_POST['city'] ?? ''),
        'state' => sanitizeInput($_POST['state'] ?? ''),
        'pincode' => sanitizeInput($_POST['pincode'] ?? ''),
        'college_name' => sanitizeInput($_POST['college_name'] ?? ''),
        'degree' => sanitizeInput($_POST['degree'] ?? ''),
        'year_of_study' => intval($_POST['year_of_study'] ?? 0),
        'cgpa' => floatval($_POST['cgpa'] ?? 0)
    ];
    
    // Validate registration data
    $validation = validateRegistration($data);
    if (!$validation['valid']) {
        $error = implode('<br>', $validation['errors']);
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $data['email']);
        $check_email->execute();
        $result = $check_email->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $insert_user = $conn->prepare("INSERT INTO users (email, password, full_name, phone, date_of_birth, gender, address, city, state, pincode, college_name, degree, year_of_study, cgpa, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $role = 'intern';
            $year_of_study = (int)$data['year_of_study'];
            $cgpa = (float)$data['cgpa'];
            $insert_user->bind_param("ssssssssssssdis", 
                $data['email'], $hashed_password, $data['full_name'], $data['phone'],
                $data['date_of_birth'], $data['gender'], $data['address'], $data['city'],
                $data['state'], $data['pincode'], $data['college_name'], $data['degree'],
                $cgpa, $year_of_study, $role
            );
            
            if ($insert_user->execute()) {
                $success = 'Registration successful! Please login.';
                // Clear form data
                $data = array_map(fn($x) => '', $data);
            } else {
                $error = 'Registration failed: ' . $insert_user->error;
            }
            $insert_user->close();
        }
        $check_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Internship Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .register-container {
            max-width: 600px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .form-section {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .form-section h6 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .form-section:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h2 class="text-center mb-4">Create Your Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success ?>
                    <a href="login.php" class="btn btn-sm btn-primary ms-2">Go to Login</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" novalidate>
                <!-- Account Information -->
                <div class="form-section">
                    <h6>Account Information</h6>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= $data['email'] ?? '' ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                            <small class="text-muted">Min 8 chars, uppercase, lowercase, number</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                    </div>
                </div>
                
                <!-- Personal Information -->
                <div class="form-section">
                    <h6>Personal Information</h6>
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="full_name" value="<?= $data['full_name'] ?? '' ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" value="<?= $data['phone'] ?? '' ?>" placeholder="10-digit number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="date_of_birth" value="<?= $data['date_of_birth'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-control" name="gender">
                                <option value="">Select</option>
                                <option value="Male" <?= ($data['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($data['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($data['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Address Information -->
                <div class="form-section">
                    <h6>Address</h6>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"><?= $data['address'] ?? '' ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="<?= $data['city'] ?? '' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" value="<?= $data['state'] ?? '' ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" name="pincode" value="<?= $data['pincode'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Education Information -->
                <div class="form-section">
                    <h6>Education</h6>
                    <div class="mb-3">
                        <label class="form-label">College Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="college_name" value="<?= $data['college_name'] ?? '' ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Degree <span class="text-danger">*</span></label>
                            <select class="form-control" name="degree" required>
                                <option value="">Select Degree</option>
                                <option value="B.Tech" <?= ($data['degree'] ?? '') === 'B.Tech' ? 'selected' : '' ?>>B.Tech</option>
                                <option value="BCA" <?= ($data['degree'] ?? '') === 'BCA' ? 'selected' : '' ?>>BCA</option>
                                <option value="B.Sc" <?= ($data['degree'] ?? '') === 'B.Sc' ? 'selected' : '' ?>>B.Sc</option>
                                <option value="B.A" <?= ($data['degree'] ?? '') === 'B.A' ? 'selected' : '' ?>>B.A</option>
                                <option value="B.Com" <?= ($data['degree'] ?? '') === 'B.Com' ? 'selected' : '' ?>>B.Com</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Year of Study</label>
                            <select class="form-control" name="year_of_study">
                                <option value="">Select</option>
                                <option value="1" <?= ($data['year_of_study'] ?? '') == 1 ? 'selected' : '' ?>>1st Year</option>
                                <option value="2" <?= ($data['year_of_study'] ?? '') == 2 ? 'selected' : '' ?>>2nd Year</option>
                                <option value="3" <?= ($data['year_of_study'] ?? '') == 3 ? 'selected' : '' ?>>3rd Year</option>
                                <option value="4" <?= ($data['year_of_study'] ?? '') == 4 ? 'selected' : '' ?>>4th Year</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CGPA</label>
                            <input type="number" class="form-control" name="cgpa" step="0.01" min="0" max="10" value="<?= $data['cgpa'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 btn-lg">Create Account</button>
            </form>
            
            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
