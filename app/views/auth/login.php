<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <!-- Côté gauche (illustration) -->
    <div class="auth-left">
        <div class="auth-brand">
            <a href="<?= url('') ?>" class="logo">
                <span class="logo-icon"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                <span class="logo-text">Bricola</span>
            </a>
        </div>
        <div class="auth-illustration">
            <div class="auth-shapes">
                <div class="a-shape s1"></div>
                <div class="a-shape s2"></div>
                <div class="a-shape s3"></div>
            </div>
            <div class="auth-quote">
                <blockquote>"Trouvez le bon artisan, au bon moment."</blockquote>
                <div class="auth-stats-mini">
                    <div><strong>21+</strong><small>Artisans</small></div>
                    <div><strong>7</strong><small>Villes</small></div>
                    <div><strong>4.7★</strong><small>Moyenne</small></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Côté droit (formulaire) -->
    <div class="auth-right">
        <div class="auth-card">
            <h1>Bon retour !</h1>
            <p class="auth-sub">Connectez-vous à votre compte Bricola</p>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Mot de passe</label>
                    <div class="pass-wrap">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="eye-btn" onclick="togglePass('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-right-to-bracket"></i> Se connecter
                </button>
            </form>

            <p class="auth-switch">
                Pas encore de compte ? <a href="<?= url('register') ?>">S'inscrire gratuitement</a>
            </p>

            <!-- Compte de test -->
            <div class="auth-demo">
                <p><small>Compte de test :</small></p>
                <code>demo@bricola.ma / Demo1234</code>
            </div>
        </div>
    </div>
</div>

<script>
// Afficher/masquer le mot de passe
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}
</script>
</body>
</html>
