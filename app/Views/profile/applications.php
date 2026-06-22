<div class="page-section">
    <div class="site-shell">
        
        <?php if (isset($flash) && is_array($flash)): ?>
        <div class="flash-message flash-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>" style="margin-bottom: 1.5rem;">
            <strong><?php echo $flash['type'] === 'success' ? 'Succès' : 'Erreur'; ?>:</strong> 
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
        <?php endif; ?>

        <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0 0 0.5rem 0;">
            Mes candidatures
        </h1>
        <p style="margin: 0 0 2rem 0; color: #666; font-size: 0.95rem;">
            Suivi de toutes vos candidatures et leurs statuts
        </p>

        <?php if (empty($applications)): ?>
        <div style="background: #f5f5f5; padding: 3rem 2rem; border-radius: var(--radius-lg); text-align: center;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; color: #ccc;">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h2 style="margin: 0 0 0.5rem 0; font-size: 1.2rem; color: #666;">
                Aucune candidature pour le moment
            </h2>
            <p style="margin: 0 0 1.5rem 0; color: #999;">
                Vous n'avez pas encore candidaté à une offre. Explorez nos offres et commencez à postuler!
            </p>
            <a href="/offres" class="btn-solid" style="display: inline-block; padding: 0.75rem 1.5rem; text-decoration: none; text-align: center;">
                Découvrir les offres
            </a>
        </div>
        <?php else: ?>

        <div style="display: grid; gap: 1.5rem;">
            <?php foreach ($applications as $app): ?>
            <div style="background: white; border: 1px solid #e0e0e0; border-radius: var(--radius-lg); padding: 1.5rem; display: grid; grid-template-columns: 1fr auto; gap: 2rem; align-items: start;">
                
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main);">
                        <?php echo htmlspecialchars((string) ($app['title'] ?? '')); ?>
                    </h3>
                    <p style="margin: 0 0 1rem 0; color: #666; font-size: 0.95rem;">
                        <strong><?php echo htmlspecialchars((string) ($app['company_name'] ?? '')); ?></strong> • 
                        <?php echo htmlspecialchars((string) ($app['city'] ?? '')); ?>
                    </p>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.85rem; margin-bottom: 1rem;">
                        <span style="background: #f0f0f0; padding: 0.35rem 0.75rem; border-radius: var(--radius);">
                            <?php 
                                $contractLabels = [
                                    'stage' => 'Stage',
                                    'cdd' => 'CDD',
                                    'job_etudiant' => 'Job étudiant'
                                ];
                                echo htmlspecialchars($contractLabels[(string) ($app['contract_type'] ?? '')] ?? 'Contrat');
                            ?>
                        </span>
                        <span style="background: #f0f0f0; padding: 0.35rem 0.75rem; border-radius: var(--radius);">
                            Date limite: <?php echo htmlspecialchars((string) ($app['deadline'] ?? 'Non spécifiée')); ?>
                        </span>
                    </div>

                    <p style="margin: 0; color: #999; font-size: 0.9rem;">
                        Candidature envoyée le <?php 
                            $createdAt = (string) ($app['created_at'] ?? '');
                            if ($createdAt !== '') {
                                echo htmlspecialchars(date('d/m/Y à H:i', strtotime($createdAt)));
                            } else {
                                echo 'date inconnue';
                            }
                        ?>
                    </p>
                </div>

                <div style="text-align: right;">
                    <?php 
                        $statusLabels = [
                            'sent' => 'Envoyée',
                            'reviewed' => 'En examen',
                            'accepted' => 'Acceptée',
                            'rejected' => 'Refusée'
                        ];
                        $statusColors = [
                            'sent' => '#5265d9',
                            'reviewed' => '#ff7a00',
                            'accepted' => '#4ca4d8',
                            'rejected' => '#e74c3c'
                        ];
                        $status = (string) ($app['status'] ?? 'sent');
                        $label = $statusLabels[$status] ?? $status;
                        $color = $statusColors[$status] ?? '#999';
                    ?>
                    <span style="display: inline-block; background: <?php echo htmlspecialchars($color); ?>; color: white; padding: 0.5rem 1rem; border-radius: var(--radius); font-weight: 600; font-size: 0.9rem;">
                        <?php echo htmlspecialchars($label); ?>
                    </span>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e0e0e0;">
            <a href="/offres" class="btn-outline" style="display: inline-block; padding: 0.75rem 1.5rem; text-decoration: none; text-align: center;">
                Continuer à explorer les offres
            </a>
        </div>

    </div>
</div>
