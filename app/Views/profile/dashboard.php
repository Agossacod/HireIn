<section class="site-shell page-section profile-dashboard">
    <?php $profile = $profile ?? []; ?>
    <?php $applications = $applications ?? []; ?>
    <?php $messages = $messages ?? []; ?>

    <div class="profile-hero-card">
        <div class="profile-hero-left">
            <div class="profile-avatar">
                <?php if (!empty($profile['profile_photo'])): ?>
                    <img src="<?= htmlspecialchars((string) $profile['profile_photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo de profil">
                <?php else: ?>
                    <span><?= strtoupper(substr((string) ($profile['fullname'] ?? 'U'), 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div>
                <p class="muted">Espace étudiant</p>
                <h1><?= htmlspecialchars((string) ($profile['fullname'] ?? 'Etudiant'), ENT_QUOTES, 'UTF-8') ?></h1>
                <p><?= htmlspecialchars((string) ($profile['university'] ?? 'Profil'), ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars((string) ($profile['city'] ?? 'Non renseigné'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        <div class="profile-hero-actions">
            <a href="/profil/edition" class="btn-outline">Modifier mon profil</a>
            <a href="/profil/messagerie" class="btn-solid">Ouvrir la messagerie</a>
        </div>
    </div>

    <div class="dashboard-grid">
        <article class="dashboard-stat-card">
            <h2><?= count($applications) ?></h2>
            <p>Candidatures récentes</p>
        </article>

        <article class="dashboard-stat-card">
            <h2><?= count($messages) ?></h2>
            <p>Messages récents</p>
        </article>

        <article class="dashboard-stat-card">
            <h2><?= htmlspecialchars((string) ($profile['search_sector'] ?: 'Tous')) ?></h2>
            <p>Secteur recherché</p>
        </article>
    </div>

    <div class="dashboard-panel-row">
        <section class="dashboard-panel">
            <h2>Dernières candidatures</h2>
            <?php if (empty($applications)): ?>
                <p class="muted">Aucune candidature récente.</p>
            <?php else: ?>
                <ul class="dashboard-list">
                    <?php foreach ($applications as $item): ?>
                        <li>
                            <strong><?= htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= htmlspecialchars((string) ($item['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            <small><?= htmlspecialchars((string) ($item['status'] ?? 'sent'), ENT_QUOTES, 'UTF-8') ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="dashboard-panel">
            <h2>Derniers messages</h2>
            <?php if (empty($messages)): ?>
                <p class="muted">Aucun message pour l’instant.</p>
            <?php else: ?>
                <ul class="message-preview-list">
                    <?php foreach ($messages as $message): ?>
                        <li>
                            <p class="message-meta">
                                <strong><?= htmlspecialchars((string) ($message['subject'] ?? 'Sans objet'), ENT_QUOTES, 'UTF-8') ?></strong>
                                <span>de <?= htmlspecialchars((string) ($message['sender_user_id'] === ($profile['id'] ?? 0) ? $message['recipient_name'] : $message['sender_name']), ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                            <p class="muted"><?= htmlspecialchars(mb_substr((string) ($message['body'] ?? ''), 0, 72), ENT_QUOTES, 'UTF-8') ?>…</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</section>
