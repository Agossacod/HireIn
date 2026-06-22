<section class="site-shell page-section admin-users">
    <div class="form-card">
        <h1>Utilisateurs</h1>
        <p class="muted">Liste des utilisateurs enregistrés. Vous pouvez modifier le rôle ou supprimer un compte.</p>

        <?php $users = $users ?? []; $flash = $flash ?? null; ?>
        <?php if (is_array($flash)): ?>
            <div class="flash-message flash-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <table style="width:100%;border-collapse:collapse;margin-top:12px">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #eee">
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($users ?? []) as $u): ?>
                    <tr style="border-bottom:1px solid #f4f6fb">
                        <td><?= htmlspecialchars((string) ($u['fullname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($u['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form action="/admin/utilisateurs/modifier" method="post" style="display:flex;gap:8px;align-items:center">
                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                <select name="role">
                                    <option value="etudiant" <?= ($u['role'] === 'etudiant') ? 'selected' : '' ?>>Etudiant</option>
                                    <option value="entreprise" <?= ($u['role'] === 'entreprise') ? 'selected' : '' ?>>Entreprise</option>
                                    <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button class="btn-submit" type="submit">Mettre à jour</button>
                            </form>
                        </td>
                        <td>
                            <form action="/admin/utilisateurs/supprimer" method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                <button class="btn-outline" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</section>
