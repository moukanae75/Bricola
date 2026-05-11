<?php
// ============================================================
//  app/views/evaluations/index.php — Page évaluation artisan
// ============================================================
require __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="max-width:800px;margin:3rem auto;padding:0 1rem;">

    <!-- En-tête artisan -->
    <div style="background:#fff;border-radius:16px;padding:2rem;box-shadow:0 2px 12px rgba(0,0,0,0.08);margin-bottom:2rem;">
        <h1 style="margin:0 0 .5rem;font-size:1.6rem;">
            <i class="fa-solid fa-star" style="color:#f59e0b;"></i>
            Évaluer <?= e($artisan['name']) ?>
        </h1>
        <p style="color:#64748b;margin:0;">
            <i class="fa-solid fa-tag"></i> <?= e($artisan['category']) ?>
            &nbsp;·&nbsp;
            <i class="fa-solid fa-location-dot"></i> <?= e($artisan['city']) ?>
        </p>

        <?php if ($stats['total'] > 0): ?>
        <div style="margin-top:1rem;display:flex;align-items:center;gap:.75rem;">
            <span style="font-size:2rem;font-weight:700;color:#f59e0b;">
                <?= number_format($stats['moyenne'], 1) ?>
            </span>
            <div>
                <?php
                $avg = round($stats['moyenne']);
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $avg
                        ? '<i class="fa-solid fa-star" style="color:#f59e0b;"></i>'
                        : '<i class="fa-regular fa-star" style="color:#f59e0b;"></i>';
                }
                ?>
                <br><small style="color:#94a3b8;"><?= $stats['total'] ?> avis</small>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Formulaire d'évaluation -->
    <div style="background:#fff;border-radius:16px;padding:2rem;box-shadow:0 2px 12px rgba(0,0,0,0.08);margin-bottom:2rem;">
        <h2 style="margin:0 0 1.5rem;font-size:1.2rem;">
            <i class="fa-solid fa-pen-to-square"></i> Laisser un avis
        </h2>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">
            <i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <i class="fa-solid fa-circle-check"></i> <?= e($success) ?>
        </div>
        <?php else: ?>
        <form method="POST" action="<?= url('evaluer') ?>?artisan_id=<?= $artisan['id'] ?>">

            <!-- Sélection de la note -->
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-weight:600;margin-bottom:.75rem;color:#1e293b;">
                    Votre note <span style="color:#ef4444;">*</span>
                </label>
                <div style="display:flex;gap:.5rem;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label style="cursor:pointer;text-align:center;">
                        <input type="radio" name="note" value="<?= $i ?>" required
                               style="display:none;"
                               id="star<?= $i ?>">
                        <span class="star-label" data-val="<?= $i ?>"
                              style="font-size:2rem;color:#d1d5db;transition:color .15s;">★</span>
                    </label>
                    <?php endfor; ?>
                </div>
                <small style="color:#94a3b8;">Cliquez sur une étoile pour noter</small>
            </div>

            <!-- Commentaire -->
            <div style="margin-bottom:1.5rem;">
                <label for="commentaire" style="display:block;font-weight:600;margin-bottom:.5rem;color:#1e293b;">
                    Commentaire <span style="color:#ef4444;">*</span>
                </label>
                <textarea id="commentaire" name="commentaire" rows="4" required
                          placeholder="Décrivez votre expérience avec cet artisan…"
                          style="width:100%;padding:.75rem 1rem;border:1px solid #e2e8f0;border-radius:8px;font-size:1rem;resize:vertical;font-family:inherit;box-sizing:border-box;"><?= e($_POST['commentaire'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:1rem;align-items:center;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Publier mon avis
                </button>
                <a href="<?= url('artisans') ?>" class="btn btn-ghost">Annuler</a>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <!-- Liste des évaluations existantes -->
    <?php if (!empty($evaluations)): ?>
    <div style="background:#fff;border-radius:16px;padding:2rem;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <h2 style="margin:0 0 1.5rem;font-size:1.2rem;">
            <i class="fa-solid fa-comments"></i> Avis des clients (<?= count($evaluations) ?>)
        </h2>
        <?php foreach ($evaluations as $ev): ?>
        <div style="border-top:1px solid #f1f5f9;padding:1.25rem 0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:38px;height:38px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                        <?= strtoupper(substr($ev['user_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <strong style="color:#1e293b;"><?= e($ev['user_name']) ?></strong>
                        <div>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $ev['note']): ?>
                                <i class="fa-solid fa-star" style="color:#f59e0b;font-size:.85rem;"></i>
                                <?php else: ?>
                                <i class="fa-regular fa-star" style="color:#f59e0b;font-size:.85rem;"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <small style="color:#94a3b8;">
                    <?= date('d/m/Y', strtotime($ev['created_at'])) ?>
                </small>
            </div>
            <p style="margin:0;color:#475569;line-height:1.6;"><?= nl2br(e($ev['commentaire'])) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<script>
// Interaction étoiles
const stars = document.querySelectorAll('.star-label');
stars.forEach(star => {
    star.addEventListener('click', function() {
        const val = parseInt(this.dataset.val);
        document.getElementById('star' + val).checked = true;
        stars.forEach((s, i) => {
            s.style.color = (i < val) ? '#f59e0b' : '#d1d5db';
        });
    });
    star.addEventListener('mouseover', function() {
        const val = parseInt(this.dataset.val);
        stars.forEach((s, i) => {
            s.style.color = (i < val) ? '#f59e0b' : '#d1d5db';
        });
    });
    star.addEventListener('mouseout', function() {
        const checked = document.querySelector('input[name="note"]:checked');
        const val = checked ? parseInt(checked.value) : 0;
        stars.forEach((s, i) => {
            s.style.color = (i < val) ? '#f59e0b' : '#d1d5db';
        });
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
