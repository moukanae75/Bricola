<?php
// ============================================================
//  app/views/home/index.php — Page d'accueil
// ============================================================

// Icônes par catégorie
$cat_icons = [
    'Plombier'      => 'fa-faucet-drip',
    'Électricien'   => 'fa-bolt',
    'Peintre'       => 'fa-paint-roller',
    'Menuisier'     => 'fa-tree',
    'Mécanicien'    => 'fa-gear',
    'Carreleur'     => 'fa-border-all',
    'Climatisation' => 'fa-wind',
    'Maçon'         => 'fa-helmet-safety',
];

// Wishlist vide sur la page d'accueil (pas de bouton cœur ici)
$wish_ids = [];

// Charger le header (navbar)
require __DIR__ . '/../layouts/header.php';
?>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg">
        <div class="hero-shape s1"></div>
        <div class="hero-shape s2"></div>
        <div class="hero-shape s3"></div>
    </div>
    <div class="hero-content">
        <div class="hero-badge"><i class="fa-solid fa-shield-check"></i> Artisans vérifiés au Maroc</div>
        <h1>Trouvez l'artisan <span class="highlight">parfait</span> en quelques clics</h1>
        <p>Plombiers, électriciens, peintres, menuisiers… Bricola connecte les particuliers avec les meilleurs artisans de leur ville.</p>

        <form class="hero-search" action="<?= url('artisans') ?>" method="GET">
            <div class="hero-search-inner">
                <div class="hs-field">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Quel type d'artisan cherchez-vous ?">
                </div>
                <div class="hs-field">
                    <i class="fa-solid fa-location-dot"></i>
                    <select name="city">
                        <option value="">Toutes les villes</option>
                        <?php foreach (['Casablanca','Rabat','Marrakech','Fès','Tanger','Agadir','Meknès'] as $c): ?>
                        <option value="<?= $c ?>"><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>

        <div class="hero-stats">
            <div class="stat"><strong><?= $total_artisans ?>+</strong><span>Artisans</span></div>
            <div class="stat-divider"></div>
            <div class="stat"><strong>7</strong><span>Villes</span></div>
            <div class="stat-divider"></div>
            <div class="stat"><strong>4.7★</strong><span>Note moyenne</span></div>
        </div>
    </div>
    <div class="hero-image-side">
        <div class="hero-card-float hc1">
            <i class="fa-solid fa-bolt"></i>
            <div><strong>Électricien disponible</strong><span>Rabat</span></div>
        </div>
        <div class="hero-card-float hc2">
            <i class="fa-solid fa-faucet-drip"></i>
            <div><strong>Plombier urgent</strong><span>Casablanca</span></div>
        </div>
        <div class="hero-card-float hc3">
            <i class="fa-solid fa-star"></i>
            <div><strong>4.9/5</strong><span>Satisfaction</span></div>
        </div>
    </div>
</section>

<!-- ===== CATÉGORIES ===== -->
<section class="section categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Nos catégories</h2>
            <p>Des experts qualifiés dans chaque domaine</p>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories_data as $cat):
                $icon = $cat_icons[$cat['category']] ?? 'fa-screwdriver-wrench';
            ?>
            <a href="<?= url('artisans') ?>?category=<?= urlencode($cat['category']) ?>" class="cat-card">
                <div class="cat-icon"><i class="fa-solid <?= $icon ?>"></i></div>
                <span class="cat-name"><?= e($cat['category']) ?></span>
                <span class="cat-count"><?= $cat['cnt'] ?> artisan<?= $cat['cnt'] > 1 ? 's' : '' ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== ARTISANS VEDETTES ===== -->
<section class="section featured-section">
    <div class="container">
        <div class="section-header">
            <h2>Artisans les mieux notés</h2>
            <p>Sélectionnés pour leur expertise et leur fiabilité</p>
            <a href="<?= url('artisans') ?>" class="btn btn-outline">Voir tous les artisans</a>
        </div>
        <div class="artisans-grid">
            <?php foreach ($featured as $a): ?>
            <?php include __DIR__ . '/../layouts/_artisan_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== COMMENT ÇA MARCHE ===== -->
<section class="section how-section">
    <div class="container">
        <div class="section-header">
            <h2>Comment ça marche ?</h2>
            <p>Simple, rapide et fiable</p>
        </div>
        <div class="how-grid">
            <div class="how-step">
                <div class="how-num">01</div>
                <div class="how-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3>Recherchez</h3>
                <p>Trouvez l'artisan qu'il vous faut par catégorie ou par ville en quelques secondes.</p>
            </div>
            <div class="how-step">
                <div class="how-num">02</div>
                <div class="how-icon"><i class="fa-solid fa-phone"></i></div>
                <h3>Contactez</h3>
                <p>Appelez directement l'artisan ou envoyez-lui une demande de service.</p>
            </div>
            <div class="how-step">
                <div class="how-num">03</div>
                <div class="how-icon"><i class="fa-solid fa-circle-check"></i></div>
                <h3>Profitez</h3>
                <p>Travail effectué par un professionnel vérifié et notez son intervention.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <h2>Besoin d'un artisan rapidement ?</h2>
            <p>Décrivez votre problème et recevez des propositions d'artisans disponibles.</p>
            <div class="cta-btns">
                <a href="<?= url('service-request') ?>" class="btn btn-white">Faire une demande</a>
                <a href="<?= url('artisans') ?>" class="btn btn-outline-white">Parcourir les artisans</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
