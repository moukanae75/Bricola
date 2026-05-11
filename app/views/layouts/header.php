<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'Bricola — Trouvez votre artisan') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php
// Liste des catégories pour la navbar
$nav_categories = ['Plombier','Électricien','Peintre','Menuisier','Mécanicien','Carreleur','Climatisation','Maçon'];
$logged_in  = isLoggedIn();
$user_name  = $logged_in ? e($_SESSION['user_name']) : '';
?>

<!-- ===== NAVBAR ===== -->
<header class="navbar" id="navbar">
    <div class="nav-container">

        <!-- Logo -->
        <a href="<?= url('') ?>" class="logo">
            <span class="logo-icon"><i class="fa-solid fa-screwdriver-wrench"></i></span>
            <span class="logo-text">Bricola</span>
        </a>

        <!-- Barre de recherche -->
        <form class="nav-search" action="<?= url('artisans') ?>" method="GET">
            <input type="text" name="search"
                   placeholder="Rechercher un artisan, une catégorie…"
                   value="<?= e($_GET['search'] ?? '') ?>">
            <select name="city">
                <option value="">Toutes les villes</option>
                <?php
                $cities = ['Casablanca','Rabat','Marrakech','Fès','Tanger','Agadir','Meknès'];
                foreach ($cities as $city) {
                    $sel = (isset($_GET['city']) && $_GET['city'] === $city) ? 'selected' : '';
                    echo '<option value="' . $city . '" ' . $sel . '>' . $city . '</option>';
                }
                ?>
            </select>
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <!-- Liens catégories -->
        <nav class="nav-links">
            <?php foreach ($nav_categories as $cat): ?>
            <a href="<?= url('artisans') ?>?category=<?= urlencode($cat) ?>"
               class="nav-cat<?= (isset($_GET['category']) && $_GET['category'] === $cat) ? ' active' : '' ?>">
               <?= e($cat) ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Boutons connexion/déconnexion -->
        <div class="nav-auth">
            <?php if ($logged_in): ?>
                <span class="nav-user"><i class="fa-solid fa-circle-user"></i> <?= $user_name ?></span>
                <a href="<?= url('dashboard') ?>" class="btn btn-ghost btn-sm">Dashboard</a>
                <a href="<?= url('logout') ?>" class="btn btn-outline btn-sm">Déconnexion</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="btn btn-ghost btn-sm">Connexion</a>
                <a href="<?= url('register') ?>" class="btn btn-primary btn-sm">S'inscrire</a>
            <?php endif; ?>
        </div>

        <!-- Bouton menu mobile -->
        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Menu mobile -->
    <div class="mobile-menu" id="mobileMenu">
        <form class="nav-search mobile-search" action="<?= url('artisans') ?>" method="GET">
            <input type="text" name="search" placeholder="Rechercher…">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <div class="mobile-cats">
            <?php foreach ($nav_categories as $cat): ?>
            <a href="<?= url('artisans') ?>?category=<?= urlencode($cat) ?>"><?= e($cat) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="mobile-auth">
            <?php if ($logged_in): ?>
                <a href="<?= url('dashboard') ?>" class="btn btn-ghost">Dashboard</a>
                <a href="<?= url('logout') ?>" class="btn btn-outline">Déconnexion</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="btn btn-ghost">Connexion</a>
                <a href="<?= url('register') ?>" class="btn btn-primary">S'inscrire</a>
            <?php endif; ?>
        </div>
    </div>
</header>
