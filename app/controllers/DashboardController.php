<?php


$APP = dirname(dirname(__DIR__));

require_once $APP . '/app/models/ServiceModel.php';
require_once $APP . '/app/models/WishModel.php';
require_once $APP . '/app/models/ArtisanModel.php';

class DashboardController {

    public function index() {

        $APP = dirname(dirname(__DIR__));

       
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
        require $APP . '/app/views/dashboard/index.php';
    }
}
