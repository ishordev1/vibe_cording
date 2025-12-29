<?php
/**
 * Internship Model
 */

class Internship {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Get all internships
     */
    public function getAll() {
        $query = "SELECT * FROM internships ORDER BY created_at DESC";
        return $this->conn->query($query);
    }
    
    /**
     * Get internship by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM internships WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Get internship by slug
     */
    public function getBySlug($slug) {
        $slug = sanitize($slug);
        $query = "SELECT * FROM internships WHERE slug = '$slug' LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Create internship (admin)
     */
    public function create($title, $description, $skills, $duration, $fees, $benefits) {
        $title = sanitize($title);
        $description = sanitize($description);
        $skills = sanitize($skills);
        $duration = sanitize($duration);
        $benefits = sanitize($benefits);
        $slug = generateSlug($title);
        
        $query = "INSERT INTO internships (title, slug, description, skills, duration, fees, benefits) 
                  VALUES ('$title', '$slug', '$description', '$skills', '$duration', $fees, '$benefits')";
        
        if ($this->conn->query($query) === TRUE) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Update internship (admin)
     */
    public function update($id, $title, $description, $skills, $duration, $fees, $benefits) {
        $title = sanitize($title);
        $description = sanitize($description);
        $skills = sanitize($skills);
        $duration = sanitize($duration);
        $benefits = sanitize($benefits);
        
        $query = "UPDATE internships SET title = '$title', description = '$description', 
                  skills = '$skills', duration = '$duration', fees = $fees, benefits = '$benefits' 
                  WHERE id = $id";
        
        return $this->conn->query($query);
    }
    
    /**
     * Delete internship (admin)
     */
    public function delete($id) {
        $query = "DELETE FROM internships WHERE id = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Get internship modules
     */
    public function getModules($internshipId) {
        $query = "SELECT * FROM modules WHERE internship_id = $internshipId ORDER BY order_seq ASC";
        return $this->conn->query($query);
    }
    
    /**
     * Add module to internship
     */
    public function addModule($internshipId, $title, $description, $filePath = null) {
        $title = sanitize($title);
        $description = sanitize($description);
        $filePath = $filePath ? sanitize($filePath) : null;
        
        $query = "INSERT INTO modules (internship_id, title, description, module_file) 
                  VALUES ($internshipId, '$title', '$description', '$filePath')";
        
        if ($this->conn->query($query) === TRUE) {
            return $this->conn->insert_id;
        }
        return false;
    }
}
?>
