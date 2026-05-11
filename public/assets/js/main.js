// ============================================================
//  main.js — Bricola frontend interactions
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

    // ── Navbar scroll effect ─────────────────────────────────
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 10);
        });
    }

    // ── Mobile menu toggle ───────────────────────────────────
    const toggle = document.getElementById('mobileToggle');
    const mMenu  = document.getElementById('mobileMenu');
    if (toggle && mMenu) {
        toggle.addEventListener('click', () => {
            mMenu.classList.toggle('open');
            const spans = toggle.querySelectorAll('span');
            const open  = mMenu.classList.contains('open');
            if (open) {
                spans[0].style.cssText = 'transform:rotate(45deg) translate(5px,5px)';
                spans[1].style.opacity = '0';
                spans[2].style.cssText = 'transform:rotate(-45deg) translate(5px,-5px)';
            } else {
                spans.forEach(s => (s.style.cssText = ''));
            }
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!toggle.contains(e.target) && !mMenu.contains(e.target)) {
                mMenu.classList.remove('open');
                toggle.querySelectorAll('span').forEach(s => (s.style.cssText = ''));
            }
        });
    }

    // ── Wishlist heart animation ─────────────────────────────
    document.querySelectorAll('.wish-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            btn.style.transform = 'scale(1.4)';
            setTimeout(() => (btn.style.transform = ''), 300);
        });
    });

    // ── Auto-dismiss alerts ──────────────────────────────────
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .6s, max-height .6s';
            alert.style.opacity    = '0';
            alert.style.maxHeight  = '0';
            alert.style.overflow   = 'hidden';
            setTimeout(() => alert.remove(), 700);
        }, 5000);
    });

    // ── Card hover tilt effect ───────────────────────────────
    document.querySelectorAll('.artisan-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width  - .5) * 6;
            const y = ((e.clientY - rect.top)  / rect.height - .5) * 6;
            card.style.transform = `translateY(-4px) rotateX(${-y}deg) rotateY(${x}deg)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    // ── Smooth entrance animation ────────────────────────────
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity  = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.artisan-card, .cat-card, .how-step, .kpi').forEach(el => {
        el.style.opacity   = '0';
        el.style.transform = 'translateY(24px)';
        el.style.transition = 'opacity .5s ease, transform .5s ease';
        observer.observe(el);
    });

});
