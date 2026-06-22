<!doctype html>
<html lang="fr">
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'HireIn', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/site.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
<?php $activeNav = $activeNav ?? ''; ?>
<?php $authUser = $_SESSION['auth'] ?? null; ?>

<header class="site-header">
    <div class="site-shell header-inner">
        <a class="brand" href="/" aria-label="Retour a l'accueil">
            <span class="brand-icon" aria-hidden="true"><i class="bi bi-sliders2-vertical"></i> </span>
            <span class="brand-text"><strong>Hire</strong><em>In</em></span>
        </a>

        <nav class="main-nav" aria-label="Navigation principale">
            <a class="<?= $activeNav === 'home' ? 'is-active' : '' ?>" href="/">Accueil</a>
            <a class="<?= $activeNav === 'offers' ? 'is-active' : '' ?>" href="/offres">Offres</a>
            <a class="<?= $activeNav === 'profiles' ? 'is-active' : '' ?>" href="/profils">Profils</a>
            <a class="<?= $activeNav === 'companies' ? 'is-active' : '' ?>" href="/entreprises">Entreprises</a>
            <a class="<?= $activeNav === 'about' ? 'is-active' : '' ?>" href="/a-propos">A propos</a>
        </nav>

        <div class="auth-links">
            <?php if (is_array($authUser)): ?>
                <?php if (($authUser['role'] ?? '') === 'etudiant'): ?>
                    <a class="btn-outline" href="/profil">Mon espace</a>
                <?php elseif (($authUser['role'] ?? '') === 'entreprise'): ?>
                    <a class="btn-outline" href="/entreprise/candidatures">Candidatures</a>
                <?php elseif (($authUser['role'] ?? '') === 'admin'): ?>
                    <a class="btn-outline" href="/admin">Administration</a>
                <?php endif; ?>
                <form action="/deconnexion" method="post" style="display: inline;">
                    <button class="btn-solid" type="submit">Se deconnecter</button>
                </form>
            <?php else: ?>
                <a class="btn-outline" href="/inscription">S'inscrire</a>
                <a class="btn-solid" href="/espace-entreprise">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <?php $content = $content ?? ''; ?>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="site-shell footer-grid">
        <div>
            <p class="footer-icons"><i class="bi bi-linkedin"></i> &nbsp; <i class="bi bi-facebook"></i> &nbsp; <i class="bi bi-instagram"></i> &nbsp; <i class="bi bi-twitter-x"></i></p>
            <p><i class="bi bi-c-circle"></i> 2026 HireIn. Tous droits réserves. | Cotonou.Benin</p>
        </div>
        <div>
            
        </div>
        <div>
            <p class="footer-title">Politique de confidentialité</p>
            <p class="footer-title">Conditions d'utilisation</p>
            <p>Email : contact@gmail.com</p>
        </div>
        <div class="footer-right">
            <p>Tel : +229 00 00 00 00</p>
            <p class="footer-brand"><i class="bi bi-sliders2-vertical"></i> HireIn</p>
        </div>
    </div>
</footer>
<script>
    document.querySelectorAll('.slider-row').forEach(function (slider) {
        const container = slider.querySelector('.poster-grid, .recruiter-grid');
        if (!container) {
            return;
        }

        const leftButton = slider.querySelector('.arrow:first-of-type');
        const rightButton = slider.querySelector('.arrow:last-of-type');
        const scrollAmount = Math.max(container.clientWidth * 0.75, 240);

        if (leftButton) {
            leftButton.addEventListener('click', function () {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }
        if (rightButton) {
            rightButton.addEventListener('click', function () {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }
    });

    // Network Canvas Animation
    const canvas = document.getElementById('network-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        const particleCount = 50;
        const connectionDistance = 150;
        
        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        
        function createParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    vx: (Math.random() - 0.5) * 1.5,
                    vy: (Math.random() - 0.5) * 1.5,
                    radius: Math.random() * 2 + 1
                });
            }
        }
        
        function drawParticles() {
            ctx.fillStyle = 'rgba(81, 100, 214, 0.6)';
            particles.forEach(particle => {
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
                ctx.fill();
            });
        }
        
        function drawConnections() {
            ctx.strokeStyle = 'rgba(81, 100, 214, 0.2)';
            ctx.lineWidth = 1;
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < connectionDistance) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
        }
        
        function updateParticles() {
            particles.forEach(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                
                if (particle.x < 0 || particle.x > canvas.width) particle.vx *= -1;
                if (particle.y < 0 || particle.y > canvas.height) particle.vy *= -1;
                
                particle.x = Math.max(0, Math.min(canvas.width, particle.x));
                particle.y = Math.max(0, Math.min(canvas.height, particle.y));
            });
        }
        
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            updateParticles();
            drawConnections();
            drawParticles();
            requestAnimationFrame(animate);
        }
        
        resizeCanvas();
        createParticles();
        animate();
        
        window.addEventListener('resize', () => {
            resizeCanvas();
            createParticles();
        });
    }
</script>
</body>
</html>
