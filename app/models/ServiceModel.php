<?php
class ServiceModel {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

  
    public function create($userId, $description, $category) {

        $sql = "INSERT INTO services (user_id, description, category)
                VALUES ($userId, '$description', '$category')";

        return $this->db->query($sql);
    }

    
    public function getByUser($userId) {

        $sql = "SELECT * FROM services
                WHERE user_id = $userId
                ORDER BY created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    
    public function count() {

        return $this->db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    }
}