<section class="site-shell page-section profile-edit-page">
    <?php $profile = $profile ?? []; ?>
    <?php $flash = $flash ?? null; ?>

    <?php if (is_array($flash)): ?>
        <div class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <h1>Modifier mon profil</h1>
        <p class="muted">Mettez à jour vos informations professionnelles et vos documents.</p>

        <form action="/profil/edition" method="post" enctype="multipart/form-data" class="profile-edit-form">
            <label>Nom complet
                <input type="text" name="fullname" value="<?= htmlspecialchars((string) ($profile['fullname'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
            </label>

            <label>Email
                <input type="email" value="<?= htmlspecialchars((string) ($profile['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" disabled>
            </label>

            <label>Université / établissement
                <input type="text" name="university" value="<?= htmlspecialchars((string) ($profile['university'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </label>

            <label>Niveau d'étude
                <input type="text" name="level" value="<?= htmlspecialchars((string) ($profile['level'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </label>

            <label>Domaine recherché
                <input type="text" name="search_sector" value="<?= htmlspecialchars((string) ($profile['search_sector'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </label>

            <label>Ville
                <input type="text" name="city" value="<?= htmlspecialchars((string) ($profile['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </label>

            <label>Téléphone
                <input type="text" name="phone" value="<?= htmlspecialchars((string) ($profile['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </label>

            <label>Compétences clés
                <textarea name="skills" rows="4"><?= htmlspecialchars((string) ($profile['skills'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>

            <div class="file-upload-row">
                <div>
                    <label>Photo de profil</label>
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <p><img class="preview-image" src="<?= htmlspecialchars((string) $profile['profile_photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Photo actuelle"></p>
                    <?php endif; ?>
                    <input type="file" name="profile_photo" accept="image/*">
                </div>
                <div>
                    <label>CV PDF / DOC</label>
                    <?php if (!empty($profile['cv'])): ?>
                        <p><a href="<?= htmlspecialchars((string) $profile['cv'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">CV actuel</a></p>
                    <?php endif; ?>
                    <input type="file" name="cv" accept=".pdf,.doc,.docx">
                </div>
            </div>

            <button class="btn-submit" type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</section>
