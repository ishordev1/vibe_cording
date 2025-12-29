<?php
/**
 * Form Validation Functions
 * Server-side validation for all forms
 */

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * Requirements: min 8 chars, uppercase, lowercase, number
 */
function validatePassword($password) {
    if (strlen($password) < 8) {
        return ['valid' => false, 'error' => 'Password must be at least 8 characters'];
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain uppercase letter'];
    }
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain lowercase letter'];
    }
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain number'];
    }
    return ['valid' => true];
}

/**
 * Validate phone number (Indian format)
 */
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 10 && preg_match('/^[6-9]/', $phone)) {
        return true;
    }
    return false;
}

/**
 * Validate file upload
 */
function validateFile($file, $allowed_types = ['pdf', 'jpg', 'jpeg', 'png']) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['valid' => false, 'error' => 'No file uploaded'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'File upload error'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['valid' => false, 'error' => 'File size exceeds 5MB limit'];
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ['valid' => false, 'error' => 'File type not allowed'];
    }
    
    return ['valid' => true];
}

/**
 * Sanitize input string
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate date format (YYYY-MM-DD)
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Validate registration form
 */
function validateRegistration($data) {
    $errors = [];
    
    if (empty($data['email']) || !validateEmail($data['email'])) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($data['password'])) {
        $errors[] = 'Password is required';
    } else {
        $pwd_validation = validatePassword($data['password']);
        if (!$pwd_validation['valid']) {
            $errors[] = $pwd_validation['error'];
        }
    }
    
    if (empty($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($data['full_name'])) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($data['phone']) || !validatePhone($data['phone'])) {
        $errors[] = 'Valid phone number is required';
    }
    
    if (empty($data['college_name'])) {
        $errors[] = 'College name is required';
    }
    
    if (empty($data['degree'])) {
        $errors[] = 'Degree is required';
    }
    
    return [
        'valid' => count($errors) === 0,
        'errors' => $errors
    ];
}

/**
 * Generate unique file name
 */
function generateFileName($original_name) {
    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
    $name = pathinfo($original_name, PATHINFO_FILENAME);
    return $name . '_' . time() . '_' . uniqid() . '.' . $ext;
}

?>
