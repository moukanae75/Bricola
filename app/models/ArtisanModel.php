<?php

class ArtisanModel {

    private $db; 

    public function __construct() {
        $this->db = Database::getConnection();
    }

   
   public function getAll($search = '', $category = '', $city = '') {

    $sql = "SELECT * FROM artisans WHERE 1=1";

    if ($search) {
        $sql .= " AND (name LIKE '%$search%' OR category LIKE '%$search%')";
    }

    if ($category) {
        $sql .= " AND category = '$category'";
    }

    if ($city) {
        $sql .= " AND city = '$city'";
    }

    $sql .= " ORDER BY rating DESC";

    $result = $this->db->query($sql);

    return $result->fetchAll();
}

    
   public function getFeatured($limit = 8) {

    $limit = (int)$limit;

    $sql = "SELECT * FROM artisans ORDER BY rating DESC LIMIT $limit";

    $stmt = $this->db->query($sql);

    return $stmt->fetchAll();
}

    
    public function getCategories() {
        $stmt = $this->db->query(
            'SELECT category, COUNT(*) as cnt FROM artisans GROUP BY category ORDER BY cnt DESC'
        );
        return $stmt->fetchAll();
    }

    
    public function getCities() {
        $stmt = $this->db->query('SELECT DISTINCT city FROM artisans ORDER BY city');
        return $stmt->fetchAll();
    }

    
   public function findById($id) {

    $sql = "SELECT * FROM artisans WHERE id = $id";

    $result = $this->db->query($sql);

    return $result->fetch();
}

    
    public function count() {
        return $this->db->query('SELECT COUNT(*) FROM artisans')->fetchColumn();
    }
}
