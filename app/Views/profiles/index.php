<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<section class="profiles-hero">
    <div class="site-shell">
        <h1 class="section-title center light">Profils etudiants</h1>
        <p class="section-subtitle center light">Decouvrez des profils talentueux</p>

        <form action="/profils" method="get" class="search-wrap profile-search">
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($search ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Rechercher par compétence, ville ou université...">
            <span class="search-icon"><i class="bi bi-search"></i></span>
        </div>
        </form>

        <p class="recruiter-note">Vous êtes recruteur ? Créez un compte pour accéder au CV et contactez les étudiants !</p>
    </div>
</section>

<section class="site-shell page-section">
    <div class="profile-grid">
        <?php if (empty($profiles)): ?>
            <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem;">
                <h2>Aucun profil correspondant.</h2>
                <p>Essayez une autre recherche ou revenez plus tard pour consulter de nouveaux talents.</p>
            </div>
        <?php endif; ?>

        <?php foreach (($profiles ?? []) as $profile): ?>
            <article class="profile-card">
                <div class="profile-header">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <div class="avatar-placeholder"><img src="<?= htmlspecialchars((string) $profile['profile_photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo de profil"></div>
                    <?php else: ?>
                        <div class="avatar-placeholder"></div>
                    <?php endif; ?>

                    <div>
                        <h2><?= htmlspecialchars((string) $profile['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="muted uppercase"><?= htmlspecialchars((string) $profile['job'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <div>
                        <p class="small-title">Compétences:</p>
                        <p class="muted"><?= htmlspecialchars((string) $profile['skills'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="muted city"><?= htmlspecialchars((string) $profile['city'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <?php if (!empty($profile['university'])): ?>
                    <p class="small-meta">Université : <?= htmlspecialchars((string) $profile['university'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
                <?php if (!empty($profile['phone'])): ?>
                    <p class="small-meta">Téléphone : <?= htmlspecialchars((string) $profile['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <div class="profile-footer">
                    <?php if (!empty($profile['cv'])): ?>
                        <a href="<?= htmlspecialchars((string) $profile['cv'], ENT_QUOTES, 'UTF-8') ?>" class="text-link accent" target="_blank">Voir le CV</a>
                    <?php else: ?>
                        <span class="text-link muted">CV non disponible</span>
                    <?php endif; ?>
                    <span class="stars"><i class="bi bi-star"></i> <i class="bi bi-star"></i> <i class="bi bi-star"></i> <i class="bi bi-star"></i> <i class="bi bi-star"></i></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <a href="#" class="muted">← Précédent</a>
        <span class="active">1</span>
        <a href="#">2</a>
        <a href="#">3</a>
        <span>...</span>
        <a href="#">67</a>
        <a href="#">68</a>
        <a href="#">Suivant →</a>
    </div>
</section>
