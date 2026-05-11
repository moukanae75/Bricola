<?php


require_once __DIR__ . '/app/models/UserModel.php';
require_once __DIR__ . '/app/models/Mailer.php';

class AuthController {

    public function login() {

        $APP = dirname(dirname(__DIR__));

        AuthMiddleware::guest();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email    = trim($_POST['email']    ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse email invalide.';
            } else {
                $userModel = new UserModel();
                $user      = $userModel->findByEmail($email);

                if ($user && $userModel->checkPassword($password, $user['password'])) {
                    if (!$user['email_verified']) {
                        $error = 'Veuillez vérifier votre email avant de vous connecter.';
                    } else {
                        session_regenerate_id(true);
                        $_SESSION['user_id']        = $user['id'];
                        $_SESSION['user_name']      = $user['name'];
                        $_SESSION['email_verified'] = true;

                        $redirect = $_SESSION['redirect_after_login'] ?? url('dashboard');
                        unset($_SESSION['redirect_after_login']);
                        header('Location: ' . $redirect);
                        exit;
                    }
                } else {
                    $error = 'Email ou mot de passe incorrect.';
                }
            }
        }

        $page_title = 'Connexion — Bricola';
        require $APP . '/app/views/auth/login.php';
    }

    public function register() {

        $APP = dirname(dirname(__DIR__));

        AuthMiddleware::guest();

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name     = trim($_POST['name']     ?? '');
            $email    = trim($_POST['email']    ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm']  ?? '';

            if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
                $error = 'Veuillez remplir tous les champs.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse email invalide.';
            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($password !== $confirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                $userModel = new UserModel();

                if ($userModel->findByEmail($email)) {
                    $error = 'Cet email est déjà utilisé.';
                } else {
                    $token  = bin2hex(random_bytes(32));
                    $userId = $userModel->create($name, $email, $password, $token);

                    if ($userId) {
                        $sent = Mailer::sendVerification($email, $name, $token);
                        if ($sent) {
                            $success = 'Inscription réussie ! Un email de vérification a été envoyé à <strong>' . e($email) . '</strong>.';
                        } else {
                            $success = 'Compte créé ! L\'email de vérification n\'a pas pu être envoyé. Contactez l\'administrateur.';
                        }
                    } else {
                        $error = 'Une erreur est survenue. Veuillez réessayer.';
                    }
                }
            }
        }

        $page_title = 'Inscription — Bricola';
        require $APP . '/app/views/auth/register.php';
    }

    public function verifyEmail() {

        $APP = dirname(dirname(__DIR__));

        $token   = trim($_GET['token'] ?? '');
        $error   = '';
        $success = '';

        if (empty($token)) {
            $error = 'Token de vérification manquant.';
        } else {
            $userModel = new UserModel();
            $user      = $userModel->verifyEmail($token);

            if ($user) {
                $success = 'Votre email a été vérifié ! Vous pouvez maintenant vous connecter.';
            } else {
                $error = 'Lien de vérification invalide ou déjà utilisé.';
            }
        }

        $page_title = 'Vérification email — Bricola';
        require $APP . '/app/views/auth/verify.php';
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect('');
    }
}
