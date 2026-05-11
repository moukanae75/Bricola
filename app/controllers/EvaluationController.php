<?php
 
$APP = dirname(dirname(__DIR__));

require_once $APP . '/app/models/ArtisanModel.php';
require_once $APP . '/app/models/EvaluationModel.php';

class EvaluationController {

    public function index() {

        $APP = dirname(dirname(__DIR__));

        AuthMiddleware::check();

        $artisan_id = (int)($_GET['artisan_id'] ?? 0);
        $error      = '';
        $success    = '';

        $artisanModel    = new ArtisanModel();
        $evaluationModel = new EvaluationModel();

        $artisan = $artisanModel->findById($artisan_id);
        if (!$artisan) {
            http_response_code(404);
            echo '<p style="text-align:center;padding:4rem;">Artisan introuvable. <a href="' . url('artisans') . '">Retour</a></p>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $note       = (int)($_POST['note']       ?? 0);
            $commentaire = trim($_POST['commentaire'] ?? '');

            if ($note < 1 || $note > 5) {
                $error = 'Veuillez sélectionner une note entre 1 et 5.';
            } elseif (empty($commentaire)) {
                $error = 'Le commentaire est obligatoire.';
            } elseif ($evaluationModel->alreadyEvaluated($artisan_id, $_SESSION['user_id'])) {
                $error = 'Vous avez déjà évalué cet artisan.';
            } else {
                $evaluationModel->create($artisan_id, $_SESSION['user_id'], $note, $commentaire);
                $success = 'Votre évaluation a été enregistrée. Merci !';
            }
        }

        $evaluations = $evaluationModel->getByArtisan($artisan_id);
        $stats       = $evaluationModel->getAverage($artisan_id);
        $page_title  = 'Évaluer ' . $artisan['name'] . ' — Bricola';

        require $APP . '/app/views/evaluations/index.php';
    }
}
