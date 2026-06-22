<section class="companies-section">
    <div class="site-shell">
        <h1 class="section-title center light">Annuaire des entreprises</h1>
        <p class="section-subtitle center light">Decouvrez le catalogue des entreprises</p>

        <form action="/entreprises" method="get" class="search-wrap profile-search" style="margin-bottom: 2rem;">
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($search ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Rechercher par entreprise, secteur ou ville...">
            <button type="submit" class="search-icon"><i class="bi bi-search"></i></button>
        </form>

        <div class="company-grid">
            <?php if (empty($companies)): ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem;">
                    <h2>Aucune entreprise trouvée.</h2>
                    <p>Essayez un autre terme de recherche ou vérifiez l'orthographe.</p>
                </div>
            <?php endif; ?>

            <?php foreach (($companies ?? []) as $company): ?>
                <article class="company-logo-card">
                    <h3><?= htmlspecialchars((string) ($company['company_name'] ?? 'Entreprise'), ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php if (!empty($company['sector'])): ?>
                        <p class="muted"><?= htmlspecialchars((string) $company['sector'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php if (!empty($company['city'])): ?>
                        <p class="muted"><?= htmlspecialchars((string) $company['city'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php if (!empty($company['description'])): ?>
                        <p><?= htmlspecialchars((string) $company['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="pagination light-pagination">
            <a href="#" class="muted">← Previous</a>
            <span class="active">1</span>
            <a href="#">2</a>
            <a href="#">3</a>
            <span>...</span>
            <a href="#">67</a>
            <a href="#">68</a>
            <a href="#">Next →</a>
        </div>

        <div class="sector-title">Secteurs d'activites</div>
        <div class="sector-list">
            <?php if (empty($sectors)): ?>
                <span>Aucun secteur disponible</span>
            <?php endif; ?>
            <?php foreach (($sectors ?? []) as $sector): ?>
                <span><?= htmlspecialchars((string) $sector, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>
