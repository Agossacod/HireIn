<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<?php use App\Core\TextHelper; ?>
<?php $offer = $offer ?? []; ?>
<?php $related = $related ?? []; ?>

<section class="offer-detail-top">
    <div class="site-shell">
        <div class="detail-toolbar">
            <div class="chair"></div>
            <div class="toolbar-main">
                <div class="tag-row">
                    <span class="tag-pill"><?= htmlspecialchars((string) ($offer['contract_type'] ?? 'Offre'), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="tag-pill is-muted"><?= htmlspecialchars((string) ($offer['sector'] ?? 'Secteur'), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="tag-pill"><?= htmlspecialchars((string) ($offer['city'] ?? 'Ville'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </div>

        <h1 class="section-title center"><?= htmlspecialchars((string) ($offer['title'] ?? 'Offre'), ENT_QUOTES, 'UTF-8') ?></h1>

        <div class="detail-summary-grid">
            <article class="detail-box">
                <h2><i class="bi bi-building"></i> Information sur l'entreprise</h2>
                <ul>
                    <li><strong>Entreprise :</strong> <?= htmlspecialchars((string) ($offer['company_name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Secteur :</strong> <?= htmlspecialchars((string) ($offer['sector'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Ville :</strong> <?= htmlspecialchars((string) ($offer['city'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></li>
                </ul>
                <div class="doc-panel">
                    <?= TextHelper::sanitizeRichHtml((string) ($offer['company_description'] ?? '<p>Aucune description fournie.</p>')) ?>
                </div>
            </article>

            <article class="detail-box">
                <h2><i class="bi bi-briefcase"></i> Information sur l'offre</h2>
               
                <ul>
                    <li><strong>Type de contrat :</strong> <?= htmlspecialchars((string) ($offer['contract_type'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Date limite :</strong> <?= htmlspecialchars((string) ($offer['deadline'] ?: 'Non renseignée'), ENT_QUOTES, 'UTF-8') ?></li>
                    <li><strong>Localisation :</strong> <?= htmlspecialchars((string) ($offer['city'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="site-shell page-section">
    <article class="simple-card">
        <h2 class="card-title">Profil recherché</h2>
        <p>
            Ce poste est destiné à un candidat motivé, sérieux et capable de s'adapter rapidement.
            Les compétences attendues incluent l'autonomie, la rigueur et une bonne communication.
        </p>
    </article>

    <article class="simple-card">
        <h2 class="card-title">Détails du poste</h2>
    
  <div class="doc-panel">
                    <?= TextHelper::sanitizeRichHtml((string) ($offer['description'] ?? '<p>Aucun détail de poste disponible.</p>')) ?>
                </div> 
                
        <div class="detail-actions">
            <a href="/offres/candidater?id=<?= (int) ($offer['id'] ?? 0) ?>" class="btn-solid" style="display: inline-block; padding: 0.75rem 1.5rem; text-decoration: none; text-align: center;">
                Postuler à cette offre
            </a>
        </div>
    </article>

    <?php if (!empty($related)): ?>
        <h2 class="section-title">Autres offres</h2>
        <div class="related-grid">
            <?php foreach (($related ?? []) as $item): ?>
                <article class="related-card">
                    <div class="related-thumb"></div>
                    <h3><?= htmlspecialchars((string) ($item['title'] ?? 'Offre'), ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars((string) ($item['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="/offres/detail?id=<?= (int) ($item['id'] ?? 0) ?>" class="text-link">Voir l'offre</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
