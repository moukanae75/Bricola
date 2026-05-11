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
    <!-- Côté gauche -->
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
                <blockquote>"Rejoignez des milliers de clients satisfaits."</blockquote>
                <ul class="auth-benefits">
                    <li><i class="fa-solid fa-check"></i> Inscription gratuite</li>
                    <li><i class="fa-solid fa-check"></i> Artisans vérifiés</li>
                    <li><i class="fa-solid fa-check"></i> Service rapide</li>
                    <li><i class="fa-solid fa-check"></i> Partout au Maroc</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Côté droit (formulaire) -->
    <div class="auth-right">
        <div class="auth-card">
            <h1>Créer un compte</h1>
            <p class="auth-sub">Rejoignez Bricola gratuitement dès maintenant</p>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= $success ?>
            </div>
            <?php endif; ?>

            <?php if (!$success): // Cacher le formulaire après succès ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name"><i class="fa-solid fa-user"></i> Nom complet</label>
                    <input type="text" id="name" name="name"
                           value="<?= e($_POST['name'] ?? '') ?>"
                           placeholder="Votre nom et prénom" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Mot de passe</label>
                    <div class="pass-wrap">
                        <input type="password" id="password" name="password" placeholder="Min. 6 caractères" required>
                        <button type="button" class="eye-btn" onclick="togglePass('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <!-- Indicateur de force du mot de passe -->
                    <div class="pass-strength" id="passStrength">
                        <div class="strength-bar"><span id="strengthBar"></span></div>
                        <small id="strengthText"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm"><i class="fa-solid fa-lock-open"></i> Confirmer le mot de passe</label>
                    <div class="pass-wrap">
                        <input type="password" id="confirm" name="confirm" placeholder="Répétez le mot de passe" required>
                        <button type="button" class="eye-btn" onclick="togglePass('confirm', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-user-plus"></i> Créer mon compte
                </button>
            </form>
            <?php endif; ?>

            <p class="auth-switch">
                Vous avez déjà un compte ? <a href="<?= url('login') ?>">Se connecter</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
}

// Indicateur de force du mot de passe
document.getElementById('password').addEventListener('input', function() {
    const val  = this.value;
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let score  = 0;
    if (val.length >= 6)          score++;
    if (val.length >= 10)         score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { label: '',           color: '',        w: '0%' },
        { label: 'Très faible', color: '#ef4444', w: '20%' },
        { label: 'Faible',      color: '#f97316', w: '40%' },
        { label: 'Moyen',       color: '#f59e0b', w: '60%' },
        { label: 'Fort',        color: '#22c55e', w: '80%' },
        { label: 'Très fort',   color: '#16a34a', w: '100%' },
    ];
    const l = levels[score] || levels[0];
    bar.style.width      = l.w;
    bar.style.background = l.color;
    text.textContent     = l.label;
    text.style.color     = l.color;
});
</script>
</body>
</html>
