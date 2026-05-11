<?php
$APP = dirname(dirname(__DIR__));

require_once $APP . '/app/models/ArtisanModel.php';
require_once $APP . '/app/models/ServiceModel.php';
require_once $APP . '/app/models/UserModel.php';

class HomeController {

    public function index() {

        $APP = dirname(dirname(__DIR__));

        $artisanModel = new ArtisanModel();
        $serviceModel = new ServiceModel();

        $total_artisans  = $artisanModel->count();
        $total_services  = $serviceModel->count();
        $featured        = $artisanModel->getFeatured(8);
        $categories_data = $artisanModel->getCategories();

        require $APP . '/app/views/home/index.php';
    }
}
