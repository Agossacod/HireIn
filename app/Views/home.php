
<section class="home-top">
    <div class="site-shell home-intro">
        <h1 class="home-logo">HireIn</h1>
        <p class="muted">La plateforme qui connecte talents étudiants et recruteurs.</p>

        <div class="chip-row">
            <?php foreach (($heroTags ?? []) as $tag): ?>
                <span class="chip"><?= htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
        </div>

        <div class="search-wrap">
            <input type="text" placeholder="Rechercher un stage, CDD, etc.">
            <span class="search-icon"><i class="bi bi-search"></i></span>
        </div>

        <div class="hero-photo">
            <img src="/assets/images/hero-photo.jpg" alt="Espace de recrutement HireIn">
        </div>

        <h2 class="section-title center">Trouvez vos futurs talents parmi les meilleurs étudiants</h2>
    </div>
</section>

<section class="network-bg">
    <canvas id="network-canvas" class="network-canvas"></canvas>
    <div class="site-shell">
        <div class="talent-grid">
            <?php foreach (($talents ?? []) as $talent): ?>
                <a class="talent-card tone-<?= htmlspecialchars((string) $talent['tone'], ENT_QUOTES, 'UTF-8') ?>" href="/profils" aria-label="Voir les profils etudiants">
                    <p class="stars"><?= str_repeat('★', (int) $talent['stars']) ?><?= str_repeat('☆', max(0, 5 - (int) $talent['stars'])) ?></p>
                    <h3><?= htmlspecialchars((string) $talent['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars((string) $talent['role'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="carousel-section">

    <div class="slider-row">

        <button class="arrow prev" type="button">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </button>

        <div class="poster-wrapper">

            <div class="poster-grid">

                <a class="poster-card" href="/offres">
                    <img src="/assets/images/cdd1.jpeg" alt="">
                </a>

                <a class="poster-card is-main" href="/offres">
                    <img src="/assets/images/cdd2.jpeg" alt="">
                </a>

                <a class="poster-card" href="/offres">
                    <img src="/assets/images/cdd4.jpeg" alt="">
                </a>

            </div>

        </div>

        <button class="arrow next" type="button">
            <i class="bi bi-arrow-right-circle-fill"></i>
        </button>

    </div>

</section>

<section class="site-shell stacked-section">

    <h2 class="section-title">
        Stages professionnels et académiques
    </h2>

    <p class="section-subtitle">
        Découvrez des profils qualifiés et des postes adaptés aux jeunes talents.
    </p>

    <div class="highlight-strip">

        <a class="mini-offer" href="/offres">
            <img src="/assets/images/mini-offer1.jpeg" alt="Stage développeur">

            <div class="offer-info">
                <h3>Développement Web</h3>
                <span>Stage</span>
            </div>
        </a>

        <a class="mini-offer" href="/offres">
            <img src="/assets/images/mini-offer2.jpg" alt="Stage marketing">

            <div class="offer-info">
                <h3>Marketing Digital</h3>
                <span>Disponible</span>
            </div>
        </a>

        <a class="mini-offer" href="/offres">
            <img src="/assets/images/mini-offer3.jpeg" alt="Stage graphisme">

            <div class="offer-info">
                <h3>Graphisme UI/UX</h3>
                <span>Stage</span>
            </div>
        </a>

        <a class="mini-offer" href="/offres">
            <img src="/assets/images/mini-offer4.jpeg" alt="Stage communication">

            <div class="offer-info">
                <h3>Communication</h3>
                <span>Alternance</span>
            </div>
        </a>

    </div>

</section>

<section class="site-shell stacked-section">
    <div class="split-headline">
        <div>
            <h2 class="section-title">Jobs à temps partiel pour étudiant(e)s</h2>
            <p class="section-subtitle left">Une section dediée aux jobs flexibles pour étudiants et jeunes diplômés.</p>
        </div>
    </div>

    <div class="highlight-strip wide">
        <a class="mini-offer" href="/offres" aria-label="Voir les jobs etudiants 1">
            <img src="/assets/images/mini-offer8.jpg" alt="Job à temps partiel 4"></a>
          
        <a class="mini-offer" href="/offres" aria-label="Voir les jobs etudiants 2">
            <img src="/assets/images/poster-card-tall is-main.jpeg" alt="Job à temps partiel 2"></a>
        <a class="mini-offer" href="/offres" aria-label="Voir les jobs etudiants 3">
            <img src="/assets/images/mini-offer7.jpg" alt="Job à temps partiel 3"></a>
        <a class="mini-offer" href="/offres" aria-label="Voir les jobs etudiants 4">
             <img src="/assets/images/Image 15.09.48.jpeg" alt="Job à temps partiel 1"></a>
            
    </div>
</section>

<section class="ero-section">

  <div class="ero-left">
    <h1>PROFESSIONAL NETWORK <br></h1>
    <p>Développe ton image professionnelle et construis ta carrière.</p>
    <a href="/get-started" class="btn-hero">Get Started</a>
  </div>

  <div class="ero-right">
    <img src="/assets/images/Team.jpg" alt="Brand illustration">
  </div>

</section>

<section class="site-shell stacked-section recruiters-section">
    <h2 class="section-title center">Entreprises qui recrutent</h2>

    <div class="slider-row">


    <div class="recruiter-wrapper">

        <div class="recruiter-grid">

            <a href="/entreprises">
                <img src="/assets/images/GDIZ-Benin-1200x600.jpg" alt="Entreprise A">
            </a>

            <a href="/entreprises">
                <img src="/assets/images/GDIZ-Benin-1200x600.jpg" alt="Entreprise B">
            </a>

            <a href="/entreprises">
                <img src="/assets/images/GDIZ-Benin-1200x600.jpg" alt="Entreprise C">
            </a>

        </div>

    </div>
  

</div>


    <div class="city-grid">
        <?php foreach (($cities ?? []) as $city): ?>
            <a href="/offres"><span><?= htmlspecialchars((string) $city, ENT_QUOTES, 'UTF-8') ?></span></a>
        <?php endforeach; ?>
    </div>

    <div class="benefit-grid">
        <article>
            <h3><i class="bi bi-building"></i> Recherche ciblée</h3>
            <p>Utilisez nos filtres avancés pour trouver rapidement les profils adaptés.</p>
        </article>
        <article>
            <h3><i class="bi bi-kanban"></i>Gestion simplifiée</h3>
            <p>Interface simple et fluide pour publier et suivre les candidatures.</p>
        </article>
        <article>
            <h3><i class="bi bi-clock"></i> Gain de temps</h3>
            <p>Centralisez vos besoins et prenez de meilleures décisions.</p>
        </article>
    </div>
</section>

<section class="site-shell home-stats-section">
    <div class="stats-top">
        <h2 class="section-title center">Statistiques</h2>
        <p class="section-subtitle center"></p>
    </div>
    <div class="stats-grid">
        <article class="stat-card">
            <span class="stat-value"><?= count($talents ?? []) ?></span>
            <p>Profils</p>
        </article>
        <article class="stat-card">
            <span class="stat-value"><?= count($cities ?? []) ?></span>
            <p>Villes couvertes</p>
        </article>
        <article class="stat-card">
            <span class="stat-value"><?= count($heroTags ?? []) ?></span>
            <p>Modes de recherche</p>
        </article>
    </div>
</section>




<script>

const cards = document.querySelectorAll('.poster-card');
const nextBtn = document.querySelector('.next');
const prevBtn = document.querySelector('.prev');

let current = 1;

function updateSlider(){

    cards.forEach(card=>{
        card.classList.remove('active');
    });

    cards[current].classList.add('active');

    const activeCard = cards[current];

    activeCard.scrollIntoView({
        behavior:'smooth',
        inline:'center',
        block:'nearest'
    });
}

nextBtn.addEventListener('click',()=>{

    current++;

    if(current >= cards.length){
        current = 0;
    }

    updateSlider();

});

prevBtn.addEventListener('click',()=>{

    current--;

    if(current < 0){
        current = cards.length - 1;
    }

    updateSlider();

});

updateSlider();

</script>





