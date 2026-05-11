<?php
// ============================================================
//  app/views/layouts/_artisan_card.php — Carte artisan
// ============================================================

$in_wish = in_array($a['id'], $wish_ids ?? []);

$cat_colors = [
    'Plombier'      => '#3b82f6',
    'Électricien'   => '#f59e0b',
    'Peintre'       => '#ec4899',
    'Menuisier'     => '#84cc16',
    'Mécanicien'    => '#6b7280',
    'Carreleur'     => '#8b5cf6',
    'Climatisation' => '#06b6d4',
    'Maçon'         => '#d97706',
];
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

$color = $cat_colors[$a['category']] ?? '#3b82f6';
$icon  = $cat_icons_map[$a['category']] ?? 'fa-screwdriver-wrench';

$stars_full  = floor($a['rating']);
$stars_half  = ($a['rating'] - $stars_full) >= 0.5 ? 1 : 0;
$stars_empty = 5 - $stars_full - $stars_half;

$parts    = explode(' ', $a['name']);
$initials = strtoupper(substr($parts[0], 0, 1)) . (isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : '');
?>
<div class="artisan-card">

    <?php if (isLoggedIn()): ?>
    <form class="wish-form" method="POST" action="<?= url('toggle-wish') ?>">
        <input type="hidden" name="artisan_id" value="<?= $a['id'] ?>">
        <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI']) ?>">
        <button type="submit" class="wish-btn <?= $in_wish ? 'wished' : '' ?>" title="Wishlist">
            <i class="fa-<?= $in_wish ? 'solid' : 'regular' ?> fa-heart"></i>
        </button>
    </form>
    <?php endif; ?>

    <div class="card-avatar" style="background:<?= $color ?>22; border:2px solid <?= $color ?>44;">
        <span class="card-initials" style="color:<?= $color ?>"><?= $initials ?></span>
        <div class="card-cat-badge" style="background:<?= $color ?>">
            <i class="fa-solid <?= $icon ?>"></i>
        </div>
    </div>

    <div class="card-body">
        <h3 class="card-name"><?= e($a['name']) ?></h3>
        <span class="card-category" style="color:<?= $color ?>"><?= e($a['category']) ?></span>

        <div class="card-meta">
            <span><i class="fa-solid fa-location-dot"></i> <?= e($a['city']) ?></span>
            <span><i class="fa-solid fa-briefcase"></i> <?= $a['experience'] ?> ans</span>
        </div>

        <div class="card-rating">
            <?php for ($i = 0; $i < $stars_full;  $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
            <?php if ($stars_half) echo '<i class="fa-solid fa-star-half-stroke"></i>'; ?>
            <?php for ($i = 0; $i < $stars_empty; $i++) echo '<i class="fa-regular fa-star"></i>'; ?>
            <strong><?= number_format($a['rating'], 1) ?></strong>
        </div>

        <div class="card-actions">
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $a['phone']) ?>" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-phone"></i> Contacter
            </a>
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('evaluer') ?>?artisan_id=<?= $a['id'] ?>" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-star"></i> Évaluer
            </a>
            <?php else: ?>
            <a href="<?= url('service-request') ?>?category=<?= urlencode($a['category']) ?>&artisan=<?= $a['id'] ?>"
               class="btn btn-outline btn-sm">
                <i class="fa-solid fa-paper-plane"></i> Demander
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
