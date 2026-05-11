<?php


$APP = dirname(dirname(__DIR__));

require_once $APP . '/app/models/WishModel.php';
require_once $APP . '/app/models/ArtisanModel.php';

class WishController {

    public function toggle() {

        AuthMiddleware::check();

        $userId    = $_SESSION['user_id'];
        $artisanId = (int)($_POST['artisan_id'] ?? 0);
        $redirect  = $_POST['redirect'] ?? url('artisans');

        if ($artisanId > 0) {
            $artisanModel = new ArtisanModel();
            $wishModel    = new WishModel();

            if ($artisanModel->findById($artisanId)) {
                if ($wishModel->exists($userId, $artisanId)) {
                    $wishModel->remove($userId, $artisanId);
                } else {
                    $wishModel->add($userId, $artisanId);
                }
            }
        }

        header('Location: ' . $redirect);
        exit;
    }
}
