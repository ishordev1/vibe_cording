<?php
// includes/functions.php
require_once __DIR__ . '/db.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;
    static $u = null;
    if ($u === null) {
        $stmt = $pdo->prepare('SELECT id,name,email,role FROM users WHERE id=?');
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
    }
    return $u;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php'); exit;
    }
}

function require_role($role) {
    $u = current_user();
    if (!$u || $u['role'] !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Forbidden'; exit;
    }
}

function register_user($name, $email, $password) {
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
    try {
        $stmt->execute([$name,$email,$hash]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        return false;
    }
}

function login_user($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id,password FROM users WHERE email=?');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        return true;
    }
    return false;
}

function validate_pdf_upload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return 'File upload failed';
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['application/pdf'];
    if (!in_array($mime, $allowed)) return 'Only PDF files allowed';
    if ($file['size'] > 20*1024*1024) return 'File too large (max 20MB)';
    return true;
}

function save_uploaded_pdf($file, $destDir, $prefix='file') {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = $prefix . '-' . time() . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dst = rtrim($destDir, '/') . '/' . $name;
    if (move_uploaded_file($file['tmp_name'], $dst)) return $name;
    return false;
}

function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

/**
 * Generate a UUID v4
 * This creates a unique identifier that is non-guessable
 * Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
 */
function generate_uuid() {
    // Use native UUID function if MySQL 8.0+
    global $pdo;
    try {
        $stmt = $pdo->query('SELECT UUID() as uuid');
        $result = $stmt->fetch();
        return $result['uuid'];
    } catch (Exception $e) {
        // Fallback to PHP-generated UUID v4
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

/**
 * Validate UUID format
 * Accepts both UUID v1 (MySQL UUID()) and v4 formats
 */
function is_valid_uuid($uuid) {
    // Accept standard UUID format (both v1 and v4)
    return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $uuid) === 1;
}

?>