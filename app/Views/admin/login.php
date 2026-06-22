<section class="site-shell page-section admin-login-page">
    <?php $flash = $flash ?? null; ?>
    <?php $hasAdmin = $hasAdmin ?? true; ?>

    <div class="auth-card auth-card--small">
        <header class="auth-head">
            <h1><?= $hasAdmin ? 'Connexion administrateur' : 'Créer un compte administrateur' ?></h1>
            <p><?= $hasAdmin ? 'Accédez au tableau de bord de gestion complète.' : 'Aucun compte administrateur n’existe encore. Créez le premier administrateur.' ?></p>
        </header>

        <?php if (is_array($flash)): ?>
            <p class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($hasAdmin): ?>
            <form action="/admin/login" method="post" class="form-grid">
                <label>Email<input type="email" name="email" placeholder="admin@exemple.com" required></label>
                <label>Mot de passe<input type="password" name="password" placeholder="Votre mot de passe" required></label>
                <button class="btn-submit form-grid-full" type="submit">Se connecter</button>
            </form>
        <?php else: ?>
            <form action="/admin/creation" method="post" class="form-grid">
                <label>Nom complet<input type="text" name="fullname" placeholder="Nom complet" required></label>
                <label>Email<input type="email" name="email" placeholder="admin@exemple.com" required></label>
                <label>Mot de passe<input type="password" name="password" placeholder="Mot de passe sécurisé" required></label>
                <label>Confirmer le mot de passe<input type="password" name="password_confirm" placeholder="Confirmez le mot de passe" required></label>
                <button class="btn-submit form-grid-full" type="submit">Créer le compte administrateur</button>
            </form>
        <?php endif; ?>
    </div>
</section>
