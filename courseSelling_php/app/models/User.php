<?php
/**
 * User Model
 */

class User {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Register a new user
     */
    public function register($name, $email, $password, $phone) {
        $name = sanitize($name);
        $email = sanitize($email);
        $password = hashPassword($password);
        $phone = sanitize($phone);
        
        $query = "INSERT INTO users (name, email, password, phone, user_type) 
                  VALUES ('$name', '$email', '$password', '$phone', 'student')";
        
        if ($this->conn->query($query) === TRUE) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        $email = sanitize($email);
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $email = sanitize($email);
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM users WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($id, $name, $phone) {
        $name = sanitize($name);
        $phone = sanitize($phone);
        
        $query = "UPDATE users SET name = '$name', phone = '$phone' WHERE id = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Change password
     */
    public function changePassword($id, $oldPassword, $newPassword) {
        $user = $this->getById($id);
        
        if (!verifyPassword($oldPassword, $user['password'])) {
            return false;
        }
        
        $newPassword = hashPassword($newPassword);
        $query = "UPDATE users SET password = '$newPassword' WHERE id = $id";
        return $this->conn->query($query);
    }
}
?>
