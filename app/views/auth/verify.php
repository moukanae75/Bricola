<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Style simple pour la page de vérification */
        .verify-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 2rem;
        }
        .verify-card {
            background: #fff;
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .verify-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .verify-icon.success { color: #22c55e; }
        .verify-icon.error   { color: #ef4444; }
        .verify-card h1 { color: #1e293b; margin-bottom: .5rem; }
        .verify-card p  { color: #64748b; margin-bottom: 2rem; }
    </style>
</head>
<body>

<div class="verify-wrapper">
    <div class="verify-card">

        <?php if ($success): ?>
            <div class="verify-icon success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1>Email vérifié !</h1>
            <p><?= e($success) ?></p>
            <a href="<?= url('login') ?>" class="btn btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Se connecter
            </a>

        <?php else: ?>
            <div class="verify-icon error">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <h1>Lien invalide</h1>
            <p><?= e($error) ?></p>
            <a href="<?= url('register') ?>" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Retour à l'inscription
            </a>
        <?php endif; ?>

        <!-- Logo Bricola -->
        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid #e2e8f0;">
            <a href="<?= url('') ?>" class="logo" style="text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;color:#6366f1;font-weight:700;">
                <i class="fa-solid fa-screwdriver-wrench"></i> Bricola
            </a>
        </div>
    </div>
</div>

</body>
</html>
