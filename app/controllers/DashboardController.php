<?php

require_once __DIR__ . '/app/models/ServiceModel.php';
require_once __DIR__ . '/app/models/WishModel.php';
require_once __DIR__ . '/app/models/ArtisanModel.php';

class DashboardController {

    public function index() {       
        AuthMiddleware::check();

        $userId = $_SESSION['user_id'];

        $serviceModel = new ServiceModel();
        $wishModel    = new WishModel();

        $services = $serviceModel->getByUser($userId);
        $wishlist  = $wishModel->getByUser($userId);

        $nb_services    = count($services);
        $nb_wish        = count($wishlist);
        $welcome        = isset($_GET['welcome']);
        $email_verified = $_SESSION['email_verified'] ?? false;

        $page_title = 'Mon espace — Bricola';
        require __DIR__ . '/app/views/dashboard/index.php';
    }
}
