<?php
/**
 * Payment Model
 */

class Payment {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Create payment record
     */
    public function create($applicationId, $amount, $paymentMethod) {
        $paymentMethod = sanitize($paymentMethod);
        
        $query = "INSERT INTO payments (application_id, amount, payment_method, status) 
                  VALUES ($applicationId, $amount, '$paymentMethod', 'pending')";
        
        if ($this->conn->query($query) === TRUE) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Get payment by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM payments WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Get payment by application ID
     */
    public function getByApplicationId($applicationId) {
        $query = "SELECT * FROM payments WHERE application_id = $applicationId LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Upload payment screenshot
     */
    public function uploadScreenshot($paymentId, $file) {
        if (!isValidFileUpload($file)) {
            return false;
        }
        
        $filename = generateUniqueFilename($file['name']);
        $filePath = UPLOAD_DIR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $query = "UPDATE payments SET screenshot = '$filename' WHERE id = $paymentId";
            return $this->conn->query($query) ? $filename : false;
        }
        
        return false;
    }
    
    /**
     * Get all pending payments (admin)
     */
    public function getPendingPayments() {
        $query = "SELECT p.*, a.id as application_id, u.name as user_name, u.email as user_email, 
                  i.title as internship_title, a.status as application_status
                  FROM payments p 
                  JOIN applications a ON p.application_id = a.id 
                  JOIN users u ON a.user_id = u.id 
                  JOIN internships i ON a.internship_id = i.id 
                  WHERE p.status = 'pending' 
                  ORDER BY p.created_at ASC";
        
        return $this->conn->query($query);
    }
    
    /**
     * Verify payment (admin)
     */
    public function verifyPayment($paymentId, $adminId) {
        $verifiedAt = date('Y-m-d H:i:s');
        
        $query = "UPDATE payments SET status = 'verified', verified_by = $adminId, verified_at = '$verifiedAt' 
                  WHERE id = $paymentId";
        
        if ($this->conn->query($query)) {
            // Get payment details
            $payment = $this->getById($paymentId);
            
            // Update application status to approved
            $appQuery = "UPDATE applications SET status = 'approved' WHERE id = " . $payment['application_id'];
            $this->conn->query($appQuery);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Reject payment (admin)
     */
    public function rejectPayment($paymentId, $adminId) {
        $verifiedAt = date('Y-m-d H:i:s');
        
        $query = "UPDATE payments SET status = 'rejected', verified_by = $adminId, verified_at = '$verifiedAt' 
                  WHERE id = $paymentId";
        
        if ($this->conn->query($query)) {
            // Get payment details
            $payment = $this->getById($paymentId);
            
            // Update application status to rejected
            $appQuery = "UPDATE applications SET status = 'rejected' WHERE id = " . $payment['application_id'];
            $this->conn->query($appQuery);
            
            return true;
        }
        
        return false;
    }
}
?>
