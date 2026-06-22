<section class="auth-page auth-bg-company">
    <?php
    $flash = $flash ?? null;
    $old = $old ?? [];
    ?>

    <div class="auth-card">
        <header class="auth-head">
            <h1>Inscription Entreprise</h1>
            <!-- <p class="phrase">
                Créez votre compte entreprise et commencez à publier vos offres d'emploi.
            </p> -->
        </header>

        <?php if (is_array($flash)): ?>
            <p class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <div class="auth-tabs">
            <a href="/inscription">Profils</a>
            <span class="is-active">Entreprises</span>
        </div>

        <form
            class="form-grid"
            action="/inscription-entreprise"
            method="post"
            enctype="multipart/form-data"
        >
            <label for="company_name">
                Nom de l'entreprise
                <input
                    id="company_name"
                    type="text"
                    name="company_name"
                    placeholder="Ex. : BENIN-AGRO"
                    value="<?= htmlspecialchars((string) ($old['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="organization"
                    required
                >
            </label>

            <label for="recruiter_name">
                Nom du recruteur
                <input
                    id="recruiter_name"
                    type="text"
                    name="recruiter_name"
                    placeholder="Nom et prénom"
                    value="<?= htmlspecialchars((string) ($old['recruiter_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="name"
                    required
                >
            </label>

            <label for="city">
                Adresse ou ville
                <input
                    id="city"
                    type="text"
                    name="city"
                    placeholder="Ex. : Cotonou, Porto-Novo..."
                    value="<?= htmlspecialchars((string) ($old['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="address-level2"
                >
            </label>

            <label for="sector">
                Secteur d'activité
                <input
                    id="sector"
                    type="text"
                    name="sector"
                    placeholder="Ex. : Informatique, BTP, Finance..."
                    value="<?= htmlspecialchars((string) ($old['sector'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                >
            </label>

            <label for="email">
                Adresse e-mail
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="contact@entreprise.com"
                    value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="email"
                    required
                >
            </label>

            <label for="phone">
                Numéro de téléphone
                <input
                    id="phone"
                    type="text"
                    name="phone"
                    placeholder="+229 01 XX XX XX XX"
                    value="<?= htmlspecialchars((string) ($old['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="tel"
                >
            </label>

            <label for="password">
                Mot de passe
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Votre mot de passe"
                    autocomplete="new-password"
                    required
                >
            </label>

            <label for="password_confirm">
                Confirmer le mot de passe
                <input
                    id="password_confirm"
                    type="password"
                    name="password_confirm"
                    placeholder="Confirmez le mot de passe"
                    autocomplete="new-password"
                    required
                >
            </label>

            <label class="wide" for="description">
                Description de l'entreprise
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Présentez brièvement votre entreprise, ses activités et ses valeurs."
                ><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>

            <div class="upload-row company-upload form-grid-full">
                <input
                    type="file"
                    id="company-logo"
                    name="logo"
                    accept="image/*"
                    hidden
                >

                <button
                    type="button"
                    class="upload-btn"
                    onclick="document.getElementById('company-logo').click();"
                    aria-label="Choisir un logo"
                >
                    <i class="bi bi-upload"></i>
                </button>

                <div>
                    <p class="upload-text" id="logo-label">
                        Insérez le logo de votre entreprise
                    </p>
                    <small id="logo-file-name"></small>
                </div>
            </div>

            <p class="muted center form-grid-full">
                En cliquant sur <strong>« Soumettre »</strong>, vous acceptez nos
                conditions d'utilisation et notre politique de confidentialité.
            </p>

            <button class="btn-submit form-grid-full" type="submit">
                Soumettre
            </button>
        </form>
    </div>

    <div class="auth-bottom-text">
        <p>Vous avez déjà un compte&nbsp;?</p>
        <a href="/espace-entreprise">Connectez-vous</a>
    </div>

    <section class="pricing-wrap">
        <h2>Nos formules d'abonnement</h2>

        <div class="pricing-grid">
            <article class="price-card free">
                <h3>Standard</h3>

                <p class="price">
                    Gratuit <span>/ mois</span>
                </p>

                <ul>
                    <li>Inscription gratuite</li>
                    <li>Publication de 3 offres d'emploi</li>
                    <li>Consultation des CV</li>
                    <li>Support standard</li>
                    <li>Gestion de votre espace entreprise</li>
                </ul>

                <button type="button">
                    Choisir cette formule
                </button>
            </article>

            <article class="price-card premium">
                <h3>Premium</h3>

                <p class="price">
                    80&nbsp;000 FCFA <span>/ mois</span>
                </p>

                <ul>
                    <li>Inscription gratuite</li>
                    <li>Publication jusqu'à 30 offres</li>
                    <li>Accès complet aux CV</li>
                    <li>Mise en avant des offres</li>
                    <li>Support prioritaire</li>
                </ul>

                <button type="button">
                    Choisir cette formule
                </button>
            </article>
        </div>
    </section>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('company-logo');
    const fileName = document.getElementById('logo-file-name');

    input?.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            fileName.textContent = this.files[0].name;
        } else {
            fileName.textContent = '';
        }
    });
});
</script>