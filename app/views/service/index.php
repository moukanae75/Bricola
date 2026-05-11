<?php
// ============================================================
//  app/views/service/index.php — Formulaire de demande de service
// ============================================================

// Icônes par catégorie
$cat_icons_map = [
    'Plombier'      => 'fa-faucet-drip',
    'Électricien'   => 'fa-bolt',
    'Peintre'       => 'fa-paint-roller',
    'Menuisier'     => 'fa-tree',
    'Mécanicien'    => 'fa-gear',
    'Carreleur'     => 'fa-border-all',
    'Climatisation' => 'fa-wind',
    'Maçon'         => 'fa-helmet-safety',
];

require __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><i class="fa-solid fa-paper-plane"></i> Demande de service</h1>
        <p>Décrivez votre besoin et nous trouverons l'artisan idéal</p>
    </div>
</div>

<div class="container service-request-page">
    <div class="sr-form-wrapper">

        <?php if ($success): ?>
        <!-- Message de succès -->
        <div class="success-card">
            <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h2>Demande envoyée !</h2>
            <p>Votre demande a bien été enregistrée. Un artisan vous contactera bientôt.</p>
            <div class="success-btns">
                <a href="<?= url('dashboard') ?>" class="btn btn-primary">Mon espace</a>
                <a href="<?= url('artisans') ?>" class="btn btn-outline">Trouver un artisan maintenant</a>
            </div>
        </div>

        <?php else: ?>
        <!-- Formulaire de demande -->
        <div class="sr-card">
            <h2>Nouvelle demande</h2>
            <p class="sr-sub">Remplissez ce formulaire, nous vous mettrons en relation avec les meilleurs artisans.</p>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="sr-form">

                <!-- Sélection de la catégorie par cartes cliquables -->
                <div class="form-group">
                    <label><i class="fa-solid fa-hard-hat"></i> Catégorie de service *</label>
                    <div class="cat-select-grid">
                        <?php
                        $selected_cat = $_POST['category'] ?? $pre_cat;
                        foreach ($categories as $cat):
                            $icon = $cat_icons_map[$cat] ?? 'fa-screwdriver-wrench';
                        ?>
                        <label class="cat-option <?= $selected_cat === $cat ? 'selected' : '' ?>">
                            <input type="radio" name="category" value="<?= $cat ?>"
                                   <?= $selected_cat === $cat ? 'checked' : '' ?>>
                            <i class="fa-solid <?= $icon ?>"></i>
                            <span><?= e($cat) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Description du problème -->
                <div class="form-group">
                    <label for="description"><i class="fa-solid fa-align-left"></i> Description du problème *</label>
                    <textarea id="description" name="description" rows="6"
                              placeholder="Décrivez votre problème en détail : ce qui ne fonctionne pas, depuis combien de temps, l'urgence de l'intervention…"
                              required><?= e($_POST['description'] ?? '') ?></textarea>
                    <small class="char-counter" id="charCount">0 caractères</small>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer ma demande
                </button>
            </form>
        </div>

        <!-- Conseils -->
        <div class="sr-tips">
            <h3><i class="fa-solid fa-lightbulb"></i> Conseils pour une bonne demande</h3>
            <ul>
                <li><i class="fa-solid fa-check"></i> Décrivez le problème avec précision</li>
                <li><i class="fa-solid fa-check"></i> Mentionnez l'urgence de l'intervention</li>
                <li><i class="fa-solid fa-check"></i> Indiquez votre disponibilité horaire</li>
                <li><i class="fa-solid fa-check"></i> Précisez votre ville si besoin</li>
            </ul>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
// Sélection catégorie au clic
document.querySelectorAll('.cat-option').forEach(label => {
    label.addEventListener('click', () => {
        document.querySelectorAll('.cat-option').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
    });
});

// Compteur de caractères
const desc    = document.getElementById('description');
const counter = document.getElementById('charCount');
if (desc && counter) {
    function updateCount() {
        counter.textContent = desc.value.length + ' caractères';
        counter.style.color = desc.value.length < 10 ? '#ef4444' : '#22c55e';
    }
    desc.addEventListener('input', updateCount);
    updateCount();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
