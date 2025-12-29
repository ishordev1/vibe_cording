<?php
/**
 * Blog Model
 */

class Blog {
    private $conn;
    
    public function __construct($database) {
        $this->conn = $database;
    }
    
    /**
     * Get all published blogs
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT * FROM blogs WHERE status = 'published' 
                  ORDER BY created_at DESC 
                  LIMIT $limit OFFSET $offset";
        
        return $this->conn->query($query);
    }
    
    /**
     * Get blog by ID or slug
     */
    public function getByIdOrSlug($identifier) {
        $identifier = sanitize($identifier);
        
        // Increment views
        $viewQuery = "UPDATE blogs SET views = views + 1 
                     WHERE id = '$identifier' OR slug = '$identifier'";
        $this->conn->query($viewQuery);
        
        $query = "SELECT b.*, u.name as author_name FROM blogs b 
                  JOIN users u ON b.author_id = u.id 
                  WHERE (b.id = '$identifier' OR b.slug = '$identifier') AND b.status = 'published' 
                  LIMIT 1";
        
        $result = $this->conn->query($query);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    /**
     * Get total count of published blogs
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM blogs WHERE status = 'published'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    /**
     * Create blog (admin)
     */
    public function create($title, $content, $authorId, $category = 'General') {
        $title = sanitize($title);
        $content = sanitize($content);
        $category = sanitize($category);
        $slug = generateSlug($title);
        
        $query = "INSERT INTO blogs (title, slug, content, author_id, category, status) 
                  VALUES ('$title', '$slug', '$content', $authorId, '$category', 'draft')";
        
        if ($this->conn->query($query) === TRUE) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Update blog (admin)
     */
    public function update($id, $title, $content, $category, $status) {
        $title = sanitize($title);
        $content = sanitize($content);
        $category = sanitize($category);
        $status = sanitize($status);
        
        $query = "UPDATE blogs SET title = '$title', content = '$content', 
                  category = '$category', status = '$status' 
                  WHERE id = $id";
        
        return $this->conn->query($query);
    }
    
    /**
     * Delete blog (admin)
     */
    public function delete($id) {
        $query = "DELETE FROM blogs WHERE id = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Get blogs by category
     */
    public function getByCategory($category, $limit = 10) {
        $category = sanitize($category);
        
        $query = "SELECT * FROM blogs WHERE status = 'published' AND category = '$category' 
                  ORDER BY created_at DESC 
                  LIMIT $limit";
        
        return $this->conn->query($query);
    }
}
?>
