<?php
require_once __DIR__ . '/app/models/ArtisanModel.php';
require_once __DIR__ . '/app/models/ServiceModel.php';
require_once __DIR__ . '/app/models/UserModel.php';

class HomeController {

    public function index() {
        $artisanModel = new ArtisanModel();
        $serviceModel = new ServiceModel();

        $total_artisans  = $artisanModel->count();
        $total_services  = $serviceModel->count();
        $featured        = $artisanModel->getFeatured(8);
        $categories_data = $artisanModel->getCategories();

        require __DIR__. '/app/views/home/index.php';
    }
}
