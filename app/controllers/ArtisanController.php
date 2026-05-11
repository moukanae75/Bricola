<?php

require_once __DIR__ . '/../models/ArtisanModel.php';
require_once __DIR__ . '/../models/WishModel.php';

class ArtisanController {

    public function index() {

        $artisanModel = new ArtisanModel();

        $search   = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $city     = $_GET['city'] ?? '';

        $artisans = $artisanModel->getAll($search, $category, $city);

        $all_cats   = array_column($artisanModel->getCategories(), 'category');
        $all_cities = $artisanModel->getCities();

        $wish_ids = [];

        if (isset($_SESSION['user_id'])) {
            $wishModel = new WishModel();
            $wished = $wishModel->getByUser($_SESSION['user_id']);
            $wish_ids = array_column($wished, 'id');
        }

        require __DIR__ . '/../views/artisans/index.php';
    }
}