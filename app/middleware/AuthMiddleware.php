<?php


class AuthMiddleware {

   
    public static function check() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            redirect('login');
        }
    }

    public static function checkVerified() {
        self::check(); 

        if (!isset($_SESSION['email_verified']) || !$_SESSION['email_verified']) {
            redirect('dashboard');
        }
    }

   
    public static function guest() {
        if (isset($_SESSION['user_id'])) {
            redirect('dashboard');
        }
    }
}
