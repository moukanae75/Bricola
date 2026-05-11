<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <a href="<?= url('') ?>" class="logo">
                <span class="logo-icon"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                <span class="logo-text">Bricola</span>
            </a>
            <p>La plateforme marocaine de mise en relation entre clients et artisans de confiance.</p>
            <div class="footer-social">
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Catégories</h4>
            <ul>
                <li><a href="<?= url('artisans') ?>?category=Plombier">Plombier</a></li>
                <li><a href="<?= url('artisans') ?>?category=Électricien">Électricien</a></li>
                <li><a href="<?= url('artisans') ?>?category=Peintre">Peintre</a></li>
                <li><a href="<?= url('artisans') ?>?category=Menuisier">Menuisier</a></li>
                <li><a href="<?= url('artisans') ?>?category=Mécanicien">Mécanicien</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Liens utiles</h4>
            <ul>
                <li><a href="<?= url('artisans') ?>">Tous les artisans</a></li>
                <li><a href="<?= url('service-request') ?>">Demander un service</a></li>
                <li><a href="<?= url('register') ?>">S'inscrire</a></li>
                <li><a href="<?= url('login') ?>">Se connecter</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Contact</h4>
            <ul>
                <li><i class="fa-solid fa-envelope"></i> contact@bricola.ma</li>
                <li><i class="fa-solid fa-phone"></i> +212 5XX-XXX-XXX</li>
                <li><i class="fa-solid fa-location-dot"></i> Casablanca, Maroc</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Bricola — Tous droits réservés</p>
    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
