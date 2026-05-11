<?php


class EvaluationModel {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    
    public function create($artisan_id, $user_id, $note, $commentaire) {

        $sql = "INSERT INTO evaluations (artisan_id, user_id, note, commentaire)
                VALUES ($artisan_id, $user_id, $note, '$commentaire')";

        $this->db->query($sql);

        return true;
    }

    
    public function getByArtisan($artisan_id) {

        $sql = "SELECT e.*, u.name AS user_name
                FROM evaluations e
                JOIN users u ON e.user_id = u.id
                WHERE e.artisan_id = $artisan_id
                ORDER BY e.created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

   
    public function alreadyEvaluated($artisan_id, $user_id) {

        $sql = "SELECT id FROM evaluations
                WHERE artisan_id = $artisan_id
                AND user_id = $user_id";

        $result = $this->db->query($sql)->fetch();

        return $result;
    }

    
    public function getAverage($artisan_id) {

        $sql = "SELECT AVG(note) as moyenne, COUNT(*) as total
                FROM evaluations
                WHERE artisan_id = $artisan_id";

        return $this->db->query($sql)->fetch();
    }
}

