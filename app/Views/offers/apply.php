<div class="page-section">
    <div class="site-shell">
        <div style="max-width: 800px; margin: 0 auto;">
            
            <?php if (isset($offer)): ?>
            <div style="background: var(--bg-hero); color: white; padding: 2rem; border-radius: var(--radius-lg); margin-bottom: 2rem;">
                <h2 style="margin: 0 0 0.5rem 0; font-size: 1.75rem; font-weight: 700;">
                    <?php echo htmlspecialchars($offer['title'] ?? ''); ?>
                </h2>
                <p style="margin: 0 0 1rem 0; font-size: 1rem; opacity: 0.95;">
                    <?php echo htmlspecialchars($offer['company_name'] ?? ''); ?> • 
                    <?php echo htmlspecialchars($offer['city'] ?? ''); ?>
                </p>
                <span style="display: inline-block; background: rgba(255,255,255,0.2); padding: 0.4rem 0.8rem; border-radius: var(--radius); font-size: 0.9rem;">
                    <?php echo htmlspecialchars($offer['contract_type'] ?? ''); ?>
                </span>
            </div>
            <?php endif; ?>

            <?php if (isset($flash) && is_array($flash)): ?>
            <div class="flash-message flash-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>" style="margin-bottom: 1.5rem;">
                <strong><?php echo $flash['type'] === 'success' ? 'Succès' : 'Erreur'; ?>:</strong> 
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
            <?php endif; ?>

            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 1.5rem 0;">
                Envoyer votre candidature
            </h1>

            <form action="/offres/candidater" method="post" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="offer_id" value="<?php echo isset($offer) ? (int) $offer['id'] : 0; ?>">
                
                <div style="grid-column: 1 / -1;">
                    <label for="cover_letter" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.95rem;">
                        Lettre de motivation <span style="color: var(--orange);">*</span>
                    </label>
                    <div id="cover-letter-editor" class="rich-editor"></div>
                    <input type="hidden" id="cover-letter-input" name="cover_letter" value="<?= isset($old['cover_letter']) ? htmlspecialchars($old['cover_letter'], ENT_QUOTES, 'UTF-8') : '' ?>">
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: #666;">
                        Partagez votre passion pour ce poste et montrez pourquoi vous êtes le candidat ideal.
                    </p>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label for="cv" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.95rem;">
                        Joindre votre CV <span style="color: #999;">(optionnel)</span>
                    </label>
                    <div style="border: 2px dashed #ccc; border-radius: var(--radius); padding: 2rem; text-align: center; background: #f9f9f9; cursor: pointer;" id="cv-upload-zone">
                        <input 
                            type="file" 
                            id="cv" 
                            name="cv" 
                            accept=".pdf,.doc,.docx" 
                            style="display: none;"
                        >
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 0.5rem; color: var(--blue);">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <p style="margin: 0 0 0.5rem 0; font-weight: 600; color: var(--text-main);">
                            Glissez votre CV ou cliquez pour parcourir
                        </p>
                        <p style="margin: 0; font-size: 0.85rem; color: #666;">
                            PDF, DOC ou DOCX • Taille max: 5 MB
                        </p>
                        <p id="cv-filename" style="margin: 0.75rem 0 0 0; font-size: 0.9rem; color: var(--orange); font-weight: 600; display: none;"></p>
                    </div>
                </div>

                <div style="grid-column: 1 / -1; display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-solid" style="flex: 1; padding: 0.75rem 1.5rem; font-size: 1rem;">
                        Envoyer ma candidature
                    </button>
                    <a href="/offres" class="btn-outline" style="flex: 1; padding: 0.75rem 1.5rem; font-size: 1rem; text-align: center; text-decoration: none;">
                        Annuler
                    </a>
                </div>
            </form>

            <div style="margin-top: 3rem; padding: 1.5rem; background: #f5f5f5; border-radius: var(--radius-lg);">
                <h3 style="margin: 0 0 1rem 0; font-weight: 700; font-size: 1rem;">
                    Conseils pour votre candidature
                </h3>
                <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.95rem; line-height: 1.6;">
                    <li>Personnalisez votre lettre pour chaque offre</li>
                    <li>Mettez en avant les compétences qui correspondent à l'offre</li>
                    <li>Soyez concis mais descriptif (200-400 mots)</li>
                    <li>Joignez un CV à jour au format PDF ou Word</li>
                    <li>Verifiez l'orthographe et la grammaire avant d'envoyer</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('cv-upload-zone');
    const cvInput = document.getElementById('cv');
    const filenameDisplay = document.getElementById('cv-filename');

    uploadZone.addEventListener('click', function() {
        cvInput.click();
    });

    cvInput.addEventListener('change', function() {
        if (cvInput.files.length > 0) {
            filenameDisplay.textContent = '✓ ' + cvInput.files[0].name;
            filenameDisplay.style.display = 'block';
        }
    });

    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.style.background = '#f0f0f0';
    });

    uploadZone.addEventListener('dragleave', function() {
        uploadZone.style.background = '#f9f9f9';
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.style.background = '#f9f9f9';
        if (e.dataTransfer.files.length > 0) {
            cvInput.files = e.dataTransfer.files;
            if (cvInput.files[0]) {
                filenameDisplay.textContent = '✓ ' + cvInput.files[0].name;
                filenameDisplay.style.display = 'block';
            }
        }
    });
});
</script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const coverInput = document.getElementById('cover-letter-input');
        const coverEditor = new Quill('#cover-letter-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ header: [1, 2, 3, false] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['clean']
                ]
            },
            placeholder: 'Rédigez votre lettre de motivation en valorisant vos compétences, votre expérience et votre motivation.'
        });

        const storedValue = coverInput.value;
        if (storedValue !== '') {
            coverEditor.root.innerHTML = storedValue;
        }

        const coverForm = document.querySelector('form.form-grid');
        coverForm?.addEventListener('submit', function () {
            coverInput.value = coverEditor.root.innerHTML.trim();
        });
    });
</script>
