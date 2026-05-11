<?php
// ============================================================
//  app/views/dashboard/index.php — Espace utilisateur
// ============================================================

// Wishlist IDs pour la carte artisan
$wish_ids = array_column($wishlist, 'id');

require __DIR__ . '/../layouts/header.php';
?>

<div class="container dashboard">

    <!-- Message de bienvenue si première connexion -->
    <?php if ($welcome): ?>
    <div class="alert alert-success welcome-alert">
        <i class="fa-solid fa-party-horn"></i>
        Bienvenue sur Bricola, <strong><?= e($_SESSION['user_name']) ?></strong> !
        Votre compte est créé avec succès.
    </div>
    <?php endif; ?>

    <!-- Avertissement si email non vérifié -->
    <?php if (!$email_verified): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-envelope"></i>
        Votre email n'est pas encore vérifié. Veuillez vérifier votre boîte mail pour activer votre compte.
    </div>
    <?php endif; ?>

    <!-- En-tête dashboard avec avatar -->
    <div class="dash-header">
        <div class="dash-avatar">
            <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
        </div>
        <div class="dash-info">
            <h1>Bonjour, <?= e($_SESSION['user_name']) ?> 👋</h1>
            <p>Gérez vos demandes et vos artisans favoris</p>
        </div>
        <a href="<?= url('service-request') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nouvelle demande
        </a>
    </div>

    <!-- Statistiques rapides (KPIs) -->
    <div class="dash-kpis">
        <div class="kpi">
            <div class="kpi-icon kpi-blue"><i class="fa-solid fa-paper-plane"></i></div>
            <div>
                <strong><?= $nb_services ?></strong>
                <span>Demande<?= $nb_services > 1 ? 's' : '' ?> envoyée<?= $nb_services > 1 ? 's' : '' ?></span>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-icon kpi-red"><i class="fa-solid fa-heart"></i></div>
            <div>
                <strong><?= $nb_wish ?></strong>
                <span>Artisan<?= $nb_wish > 1 ? 's' : '' ?> favori<?= $nb_wish > 1 ? 's' : '' ?></span>
            </div>
        </div>
        <div class="kpi">
            <div class="kpi-icon kpi-green"><i class="fa-solid fa-magnifying-glass"></i></div>
            <div>
                <strong>21+</strong>
                <span>Artisans disponibles</span>
            </div>
        </div>
    </div>

    <!-- Mes demandes de service -->
    <section class="dash-section">
        <div class="dash-section-head">
            <h2><i class="fa-solid fa-clipboard-list"></i> Mes demandes de service</h2>
            <a href="<?= url('service-request') ?>" class="btn btn-outline btn-sm">+ Nouvelle demande</a>
        </div>

        <?php if (empty($services)): ?>
        <div class="empty-state small-empty">
            <i class="fa-regular fa-folder-open"></i>
            <p>Aucune demande pour le moment.</p>
            <a href="<?= url('service-request') ?>" class="btn btn-primary btn-sm">Créer ma première demande</a>
        </div>
        <?php else: ?>
        <div class="services-list">
            <?php foreach ($services as $s): ?>
            <div class="service-item">
                <div class="service-cat-badge"><?= e($s['category']) ?></div>
                <p class="service-desc"><?= nl2br(e($s['description'])) ?></p>
                <small class="service-date">
                    <i class="fa-regular fa-calendar"></i>
                    <?= date('d/m/Y à H:i', strtotime($s['created_at'])) ?>
                </small>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- Mes artisans favoris (wishlist) -->
    <section class="dash-section">
        <div class="dash-section-head">
            <h2><i class="fa-solid fa-heart"></i> Mes artisans favoris</h2>
            <a href="<?= url('artisans') ?>" class="btn btn-outline btn-sm">Parcourir les artisans</a>
        </div>

        <?php if ($nb_wish === 0): ?>
        <div class="empty-state small-empty">
            <i class="fa-regular fa-heart"></i>
            <p>Vous n'avez pas encore d'artisans favoris.</p>
            <a href="<?= url('artisans') ?>" class="btn btn-primary btn-sm">Découvrir les artisans</a>
        </div>
        <?php else: ?>
        <div class="artisans-grid">
            <?php foreach ($wishlist as $a): ?>
            <?php include __DIR__ . '/../layouts/_artisan_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
