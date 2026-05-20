<?php
require_once __DIR__ . '/app/models/ServiceModel.php';

class ServiceController {

    private $categories = [
        'Plombier', 'Électricien', 'Peintre',
        'Menuisier', 'Mécanicien', 'Carreleur',
        'Climatisation', 'Maçon'
    ];

    public function index() {
        AuthMiddleware::check();

        $error   = '';
        $success = false;
        $pre_cat = $_GET['category'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $description = trim($_POST['description'] ?? '');
            $category    = trim($_POST['category']    ?? '');
            $userId      = $_SESSION['user_id'];

            if (empty($description) || empty($category)) {
                $error = 'Veuillez remplir tous les champs.';
            } elseif (!in_array($category, $this->categories)) {
                $error = 'Catégorie invalide.';
            } elseif (strlen($description) < 10) {
                $error = 'La description doit contenir au moins 10 caractères.';
            } else {
                $serviceModel = new ServiceModel();
                if ($serviceModel->create($userId, $description, $category)) {
                    $success = true;
                } else {
                    $error = 'Une erreur est survenue. Veuillez réessayer.';
                }
            }
        }

        $page_title = 'Demande de service — Bricola';
        $categories = $this->categories;
        require __DIR__ . '/app/views/service/index.php';
    }
}
