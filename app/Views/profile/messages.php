<section class="site-shell page-section profile-messages-page">
    <?php $messages = $messages ?? []; ?>
    <?php $companies = $companies ?? []; ?>
    <?php $flash = $flash ?? null; ?>

    <?php if (is_array($flash)): ?>
        <div class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="messages-grid">
        <section class="messages-panel">
            <div class="panel-header">
                <h1>Messagerie</h1>
                <p class="muted">Suivez vos échanges et contactez les entreprises.</p>
            </div>

            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <p>Aucun message disponible. Envoyez votre premier message à une entreprise.</p>
                </div>
            <?php else: ?>
                <ul class="message-list">
                    <?php foreach ($messages as $message): ?>
                        <li class="message-card">
                            <div class="message-card-header">
                                <strong><?= htmlspecialchars((string) ($message['subject'] ?? 'Sans objet'), ENT_QUOTES, 'UTF-8') ?></strong>
                                <span><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $message['created_at'] ?? 'now')), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <p class="message-meta">
                                <small>De : <?= htmlspecialchars((string) $message['sender_name'], ENT_QUOTES, 'UTF-8') ?></small>
                                <small>À : <?= htmlspecialchars((string) $message['recipient_name'], ENT_QUOTES, 'UTF-8') ?></small>
                            </p>
                            <p><?= nl2br(htmlspecialchars((string) $message['body'], ENT_QUOTES, 'UTF-8')) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <aside class="messages-compose-panel">
            <div class="panel-header">
                <h2>Envoyer un message</h2>
                <p class="muted">Sélectionnez une entreprise et rédigez un message rapide.</p>
            </div>

            <form action="/profil/messagerie/envoyer" method="post" class="compose-form">
                <label>Entreprise destinataire
                    <select name="recipient_id" required>
                        <option value="">Choisir une entreprise</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?= (int) ($company['id'] ?? 0) ?>">
                                <?= htmlspecialchars((string) ($company['company_name'] ?? $company['recruiter_name'] ?? 'Entreprise'), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Sujet
                    <input type="text" name="subject" required>
                </label>
                <label>Message
                    <textarea name="body" rows="6" required></textarea>
                </label>
                <button class="btn-solid" type="submit">Envoyer</button>
            </form>
        </aside>
    </div>
</section>
