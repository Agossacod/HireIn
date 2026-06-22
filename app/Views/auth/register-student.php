<section class="auth-page auth-bg-student">
    <?php $flash = $flash ?? null; ?>
    <?php $old = $old ?? []; ?>

    <div class="auth-card">
        <header class="auth-head">
            <h1>Formulaire d'inscription</h1>
            <!-- <p>Créez votre profil en remplissant le formulaire ci-dessous.</p> -->
        </header>

        <?php if (is_array($flash)): ?>
            <p class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <div class="auth-tabs">
            <span class="is-active">Profils</span>
            <a href="/inscription-entreprise">Entreprises</a>
        </div>

        <form class="form-grid" action="/inscription" method="post" enctype="multipart/form-data">
            <label>Nom complet<input type="text" name="fullname" placeholder="Nom complet" value="<?= htmlspecialchars((string) ($old['fullname'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>Adresse précise (ville, quartier ...)<input type="text" name="city" placeholder="Ex: Cotonou, Porto-Novo, etc." value="<?= htmlspecialchars((string) ($old['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Niveau d'étude<input type="text" name="level" placeholder="Ex: Licence, Master, etc." value="<?= htmlspecialchars((string) ($old['level'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Secteur d'activité recherche<input type="text" name="search_sector" placeholder="Ex: Informatique, Marketing, etc." value="<?= htmlspecialchars((string) ($old['search_sector'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Email<input type="email" name="email" placeholder="votre email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
            <label>Université frequentée<input type="text" name="university" placeholder="Ex: Gaza formation" value="<?= htmlspecialchars((string) ($old['university'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Numero de téléphone<input type="text" name="phone" placeholder="+229 01 XX XX XX XX" value="<?= htmlspecialchars((string) ($old['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Mot de passe<input type="password" name="password" placeholder="votre mot de passe" required></label>
            <label>Compétences<input type="text" name="skills" placeholder="Ex: Communication, design, etc." value="<?= htmlspecialchars((string) ($old['skills'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
            <label>Confirmer le mot de passe<input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required></label>

            <div class="upload-row form-grid-full">
                <div class="avatar-placeholder">
                    <input type="file" id="profile-photo" name="profile_photo" accept="image/*" style="display: none;">
                    <button class="upload-btn" type="button" onclick="document.getElementById('profile-photo').click()" aria-label="Ajouter une photo"></button>
                </div>
                <p>Photo de Profil</p>
                <p class="upload-text">Joignez votre CV → </p>
                <input type="file" id="student-cv" name="cv" accept=".pdf,.doc,.docx" style="display: none;">
                <button class="upload-btn" type="button" onclick="document.getElementById('student-cv').click()" aria-label="Ajouter un CV"><i class="bi bi-download"></i></button>
            </div>

             <p class="muted center form-grid-full">En cliquant sur <strong>« Soumettre »</strong>, vous acceptez nos Conditions d'utilisation et notre Politique de confidentialité.</p>
            <button class="btn-submit form-grid-full" type="submit">Soumettre</button>
        </form>
    </div>

    <div class="auth-bottom-text">
        <p>Vous avez deja un compte ?</p>
        <a href="/espace-entreprise">Connectez-vous</a>
    </div>
</section>

<script>
    // Gérer l'affichage du nom du fichier photo
    document.getElementById('profile-photo')?.addEventListener('change', function() {
        if (this.files.length > 0) {
            const button = this.parentElement.querySelector('.upload-btn');
            button.title = 'Photo: ' + this.files[0].name;
        }
    });

    // Gérer l'affichage du nom du fichier CV
    document.getElementById('student-cv')?.addEventListener('change', function() {
        if (this.files.length > 0) {
            const button = this.parentElement.querySelector('.upload-btn:last-child');
            button.title = 'CV: ' + this.files[0].name;
        }
    });
</script>
