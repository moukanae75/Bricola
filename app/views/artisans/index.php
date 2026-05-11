<?php
require __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>
            <?php if ($category): ?>
                <i class="fa-solid fa-hard-hat"></i> <?= $category ?>s
            <?php elseif ($search): ?>
                Résultats pour « <?= $search ?> »
            <?php else: ?>
                Tous nos artisans
            <?php endif; ?>
        </h1>
        <p><?= $count ?> artisan<?= $count > 1 ? 's' : '' ?> trouvé<?= $count > 1 ? 's' : '' ?></p>
    </div>
</div>

<div class="container artisans-page">

    <!-- Sidebar filtres -->
    <aside class="filters-sidebar">
        <h3><i class="fa-solid fa-sliders"></i> Filtres</h3>

        <form method="GET" action="<?= url('artisans') ?>">
            <div class="filter-group">
                <label>Recherche</label>
                <input type="text" name="search" value="<?= $search ?>" placeholder="Nom, catégorie…">
            </div>

            <div class="filter-group">
                <label>Catégorie</label>
                <div class="filter-chips">
                    <a href="<?= url('artisans') ?><?= $city ? '?city='.urlencode($city) : '' ?>"
                       class="chip <?= !$category ? 'active' : '' ?>">Toutes</a>
                    <?php foreach ($all_cats as $c): ?>
                    <a href="<?= url('artisans') ?>?category=<?= urlencode($c) ?><?= $city ? '&city='.urlencode($city) : '' ?>"
                       class="chip <?= $category === $c ? 'active' : '' ?>">
                       <?= $c ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-group">
                <label>Ville</label>
                <select name="city">
                    <option value="">Toutes les villes</option>
                    <?php foreach ($all_cities as $c): ?>
                    <option value="<?= $c ?>" <?= $city === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($category): ?>
            <input type="hidden" name="category" value="<?= e($category) ?>">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary" style="width:100%"
                <i class="fa-solid fa-filter"></i> Appliquer
            </button>

            <?php if ($search || $category || $city): ?>
            <a href="<?= url('artisans') ?>" class="btn btn-ghost" style="width:100%;margin-top:.5rem;text-align:center">
                <i class="fa-solid fa-rotate-left"></i> Réinitialiser
            </a>
            <?php endif; ?>
        </form>
    </aside>

    <!-- Résultats -->
    <main class="artisans-results">
        <?php if ($count === 0): ?>
        <div class="empty-state">
            <i class="fa-solid fa-face-sad-tear"></i>
            <h3>Aucun artisan trouvé</h3>
            <p>Essayez d'autres critères de recherche.</p>
            <a href="<?= url('artisans') ?>" class="btn btn-primary">Voir tous les artisans</a>
        </div>
        <?php else: ?>
        <div class="artisans-grid"
            <?php foreach ($artisans as $a): ?>
            <?php include __DIR__ . '/../layouts/_artisan_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
