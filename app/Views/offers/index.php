<section class="offer-layout">
    <aside class="offer-sidebar">
        <h2>Filtres</h2>
        <form action="/offres" method="get" class="filter-form">
            <label>Recherche
                <input type="text" name="q" value="<?= htmlspecialchars((string) ($search['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Stage, CDD, entreprise, ville">
            </label>

            <label>Type de contrat
                <select name="type">
                    <option value="">Tous</option>
                    <option value="stage" <?= (isset($search['type']) && $search['type'] === 'stage') ? 'selected' : '' ?>>Stage</option>
                    <option value="cdd" <?= (isset($search['type']) && $search['type'] === 'cdd') ? 'selected' : '' ?>>CDD</option>
                    <option value="job_etudiant" <?= (isset($search['type']) && $search['type'] === 'job_etudiant') ? 'selected' : '' ?>>Job étudiant</option>
                </select>
            </label>

            <label>Ville
                <input type="text" name="city" value="<?= htmlspecialchars((string) ($search['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Cotonou, Parakou..."></label>

            <label>Secteur
                <select name="sector">
                    <option value="">Tous</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option value="<?= htmlspecialchars((string) $category, ENT_QUOTES, 'UTF-8') ?>" <?= (isset($search['sector']) && $search['sector'] === $category) ? 'selected' : '' ?>><?= htmlspecialchars((string) $category, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button class="btn-solid" type="submit">Appliquer</button>
        </form>
    </aside>

    <div class="offer-content">
        <section class="offer-hero">
            <div class="section-intro">
                <h1 class="section-title">Offres disponibles</h1>
                <p class="section-subtitle1">Trouvez le stage ou job étudiant qui correspond à votre profil.</p>
            </div>
        </section>

        <section class="offers-list-wrapper">
            <?php if (empty($offers)): ?>
                <div class="empty-state">
                    <h2>Aucune offre ne correspond à votre recherche.</h2>
                    <p>Essayez un autre mot-clé, une autre ville ou un autre secteur.</p>
                </div>
            <?php endif; ?>

            <?php
                $typeLabels = [
                    'stage' => 'Stage',
                    'cdd' => 'CDD',
                    'job_etudiant' => 'Job étudiant',
                ];
            ?>

            <?php foreach (($offers ?? []) as $offer): ?>
                <article class="offer-card-large">
                    <div>
                        <h2><?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="muted"><?= htmlspecialchars((string) ($offer['company_name'] ?? 'Entreprise inconnue'), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars((string) ($offer['sector'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="small-meta">
                            <?= htmlspecialchars($typeLabels[(string) ($offer['contract_type'] ?? '')] ?? 'Contrat', ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars((string) ($offer['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <!-- <p class="small-meta"><?= nl2br(htmlspecialchars((string) ($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p> -->
                        <p class="small-meta">Date limite: <?= htmlspecialchars((string) ($offer['deadline'] ?? 'Non définie'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <div class="offer-card-actions">
                        <?php
                        $logoPath = '';
                        if (!empty($offer['logo'])) {
                            $logoPath = htmlspecialchars((string) $offer['logo'], ENT_QUOTES, 'UTF-8');
                        }
                        $logoUrl = $logoPath !== '' ? $logoPath : '/assets/images/portrait-handsome-man.jpg';
                        ?>
                        <div class="placeholder-thumb" style="background-image: url('<?= $logoUrl ?>'); background-size: cover; background-position: center;"></div>
                        <?php $offerId = (int) ($offer['id'] ?? 0); ?>
                        <a class="btn-solid" href="<?= $offerId > 0 ? '/offres/candidater?id=' . $offerId : '#' ?>">Postuler</a>
                        <a class="text-link" href="<?= $offerId > 0 ? '/offres/detail?id=' . $offerId : '/offres/detail' ?>">Plus d'info</a>
                    </div>
                </article>
            <?php endforeach; ?>

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
    </div>
</section>
