<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
<section class="site-shell page-section">
    <?php $flash = $flash ?? null; ?>
    <?php $authUser = $authUser ?? null; ?>
    <?php $companyOffers = $companyOffers ?? []; ?>

    <?php if (is_array($flash)): ?>
        <p class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if (!is_array($authUser)): ?>
        <section class="login-panel">
            <h1>Connectez-vous à votre compte</h1>

            <form action="/connexion" method="post" class="login-form">
                <label>
                    Email
                    <input type="email" name="email" placeholder="Email" required>
                </label>

                <label>
                    Mot de passe
                    <input type="password" name="password" placeholder="Mot de passe" required>
                </label>

                <button class="btn-submit" type="submit">
                    Se connecter
                </button>
            </form>
        </section>
    <?php endif; ?>

    <article class="dashboard-card">

        <header>
            <div>
                <h2>Nom de l'entreprise</h2>
                <p>
                    <?= htmlspecialchars(
                        (string) (
                            $authUser['company_name']
                            ?? $authUser['fullname']
                            ?? 'Entreprise'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>
            </div>

            <span>⋮</span>
        </header>

        <div class="dashboard-media"></div>

        <!-- <div class="dashboard-copy">
            <h3>Poste</h3>
            <p>Sous-titre</p>
            <p class="muted">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            </p>
        </div> -->

        <?php if (is_array($authUser) && ($authUser['role'] ?? '') === 'entreprise'): ?>

            <form action="/offres/publier" method="post" class="publish-offer-form">

                <label>
                    Titre de l'offre
                    <input
                        type="text"
                        name="title"
                        placeholder="Ex : Développeur Full Stack"
                        required>
                </label>

                <label>
                    Ville
                    <input
                        type="text"
                        name="city"
                        placeholder="Ex : Cotonou"
                        required>
                </label>

                <label>
                    Type de contrat
                    <select name="contract_type" required>
                        <option value="">Choisir...</option>
                        <option value="stage">Stage</option>
                        <option value="cdd">CDD</option>
                        <option value="job_etudiant">Job étudiant</option>
                        <option value="cdi">CDI</option>
                    </select>
                </label>

                <label>Description de l'offre</label>

                <div
                    id="offer-description-editor"
                    class="rich-editor"
                    style="min-height:250px;">
                </div>

                <textarea
                    id="offer-description-input"
                    name="description"
                    hidden
                    required>
                </textarea>

                <label>
                    Date limite
                    <input type="date" name="deadline">
                </label>

                <button class="btn-submit" type="submit">
                    Publier une offre
                </button>

            </form>

        <?php endif; ?>

        <div class="dashboard-actions">

            <button class="btn-accent" type="button">
                Modifier
            </button>

            <a class="btn-solid" href="/entreprise/candidatures">
                Voir les candidatures
            </a>

            <?php if (is_array($authUser)): ?>
                <form action="/deconnexion" method="post">
                    <button class="btn-outline" type="submit">
                        Déconnexion
                    </button>
                </form>
            <?php endif; ?>

        </div>

    </article>

    <?php if (!empty($companyOffers)): ?>

        <section class="company-offers-list">

            <h2>Vos dernières offres</h2>

            <?php foreach ($companyOffers as $item): ?>

                <article class="company-offer-item">

                    <h3>
                        <?= htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars((string) ($item['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        |
                        <?= htmlspecialchars((string) ($item['contract_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        |
                        <?= htmlspecialchars((string) ($item['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </article>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>

</section>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const editorElement = document.getElementById("offer-description-editor");

    if (!editorElement) {
        return;
    }

    const quill = new Quill(editorElement, {
        theme: "snow",
        placeholder: "Rédigez une description détaillée et attractive de l'offre...",
        modules: {
            toolbar: [
                ["bold", "italic", "underline", "strike"],
                [{ header: [1, 2, 3, false] }],
                [{ list: "ordered" }, { list: "bullet" }],
                [{ align: [] }],
                ["blockquote"],
                ["link"],
                ["clean"]
            ]
        }
    });

    const form = document.querySelector(".publish-offer-form");
    const hiddenInput = document.getElementById("offer-description-input");

    form?.addEventListener("submit", function (event) {

        const plainText = quill.getText().trim();

        if (plainText.length === 0) {
            event.preventDefault();
            alert("Veuillez renseigner la description de l'offre.");
            return;
        }

        hiddenInput.value = quill.root.innerHTML;
    });

});
</script>