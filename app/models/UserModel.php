<?php
class UserModel {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Email
    public function findByEmail($email) {

        $sql = "SELECT * FROM users WHERE email = '$email'";

        return $this->db->query($sql)->fetch();
    }

    // ID
    public function findById($id) {

        $sql = "SELECT * FROM users WHERE id = $id";

        return $this->db->query($sql)->fetch();
    }

    // Create user
    public function create($name, $email, $password, $verifyToken) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, email_verify_token, email_verified)
                VALUES ('$name', '$email', '$hashedPassword', '$verifyToken', 0)";

        $this->db->query($sql);

        return $this->db->lastInsertId();
    }

    // Verify email
    public function verifyEmail($token) {

        $sql = "SELECT * FROM users WHERE email_verify_token = '$token' AND email_verified = 0";

        $user = $this->db->query($sql)->fetch();

        if ($user) {

            $update = "UPDATE users
                       SET email_verified = 1,
                           email_verify_token = NULL
                       WHERE id = " . $user['id'];

            $this->db->query($update);

            return $user;
        }

        return false;
    }

    // Password check
    public function checkPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}