<?php
class WishModel {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    
    public function exists($userId, $artisanId) {

        $sql = "SELECT id FROM wishlist
                WHERE user_id = $userId
                AND artisan_id = $artisanId";

        $result = $this->db->query($sql)->fetch();

        return (bool) $result;
    }

    
    public function add($userId, $artisanId) {

        $sql = "INSERT INTO wishlist (user_id, artisan_id)
                VALUES ($userId, $artisanId)";

        return $this->db->query($sql);
    }

    
    public function remove($userId, $artisanId) {

        $sql = "DELETE FROM wishlist
                WHERE user_id = $userId
                AND artisan_id = $artisanId";

        return $this->db->query($sql);
    }

    
    public function getByUser($userId) {

        $sql = "SELECT a.*
                FROM artisans a
                JOIN wishlist w ON w.artisan_id = a.id
                WHERE w.user_id = $userId
                ORDER BY w.created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }
}