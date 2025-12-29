<?php
/**
 * Application Model
 */

class Application {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Create application
     */
    public function create($userId, $internshipId) {
        // Check if already applied with pending or approved status
        $checkQuery = "SELECT id FROM applications WHERE user_id = $userId AND internship_id = $internshipId AND status IN ('pending', 'approved') LIMIT 1";
        $checkResult = $this->conn->query($checkQuery);
        
        if ($checkResult->num_rows > 0) {
            return false; // Already applied with active status
        }
        
        $query = "INSERT INTO applications (user_id, internship_id, status) 
                  VALUES ($userId, $internshipId, 'pending')";
        
        if ($this->conn->query($query) === TRUE) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Get application by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM applications WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Get applications for a user
     */
    public function getByUserId($userId) {
        $query = "SELECT a.*, i.title as internship_title, i.slug as internship_slug, i.fees 
                  FROM applications a 
                  JOIN internships i ON a.internship_id = i.id 
                  WHERE a.user_id = $userId 
                  ORDER BY a.created_at DESC";
        
        return $this->conn->query($query);
    }
    
    /**
     * Get all applications (admin)
     */
    public function getAll() {
        $query = "SELECT a.*, u.name as user_name, u.email as user_email, i.title as internship_title, p.status as payment_status 
                  FROM applications a 
                  JOIN users u ON a.user_id = u.id 
                  JOIN internships i ON a.internship_id = i.id 
                  LEFT JOIN payments p ON a.id = p.application_id
                  ORDER BY a.created_at DESC";
        
        return $this->conn->query($query);
    }
    
    /**
     * Update application status (admin)
     */
    public function updateStatus($id, $status) {
        $status = sanitize($status);
        $query = "UPDATE applications SET status = '$status' WHERE id = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Get application with payment info
     */
    public function getWithPayment($applicationId) {
        $query = "SELECT a.*, u.name as user_name, u.email as user_email, i.title as internship_title, 
                  p.id as payment_id, p.amount, p.payment_method, p.screenshot, p.status as payment_status
                  FROM applications a 
                  JOIN users u ON a.user_id = u.id 
                  JOIN internships i ON a.internship_id = i.id 
                  LEFT JOIN payments p ON a.id = p.application_id
                  WHERE a.id = $applicationId 
                  LIMIT 1";
        
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
}
?>
