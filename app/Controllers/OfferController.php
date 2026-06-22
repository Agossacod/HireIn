<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuthMiddleware;
use App\Core\Controller;
use App\Core\Container;
use App\Repositories\OfferRepository;

final class OfferController extends Controller
{
    // Action qui affiche la liste des offres.
    public function index(): void
    {
        $offers = [];
        $query = trim((string) ($_GET['q'] ?? ''));
        $contractType = trim((string) ($_GET['type'] ?? ''));
        $city = trim((string) ($_GET['city'] ?? ''));
        $sector = trim((string) ($_GET['sector'] ?? ''));
        $categories = [
            'Marketing',
            'Informatique',
            'Ressources humaines',
            'Finances',
            'Droit',
            'Sante',
            'Hotellerie',
            'Agro alimentaire',
        ];

        if (Container::has('db')) {
            $offerRepository = new OfferRepository(Container::get('db'));
            $offers = $offerRepository->search([
                'query' => $query,
                'contract_type' => $contractType,
                'city' => $city,
                'sector' => $sector,
            ], 12, 0);

            $distinctSectors = $offerRepository->getDistinctSectors();
            if ($distinctSectors !== []) {
                $categories = $distinctSectors;
            }
        }

        if ($offers === []) {
            $offers = [
                [
                    'id' => 1,
                    'title' => 'Stage en Marketing',
                    'subtitle' => 'Subheading',
                    'type' => 'Stage professionnel',
                    'time' => 'Temps plein',
                    'city' => 'Cotonou',
                ],
                [
                    'id' => 2,
                    'title' => 'CDD Analyste Data',
                    'subtitle' => 'Subheading',
                    'type' => 'CDD',
                    'time' => 'Temps plein',
                    'city' => 'Porto-Novo',
                ],
                [
                    'id' => 3,
                    'title' => 'Aide cuisinier - Job a temps partiel',
                    'subtitle' => 'Subheading',
                    'type' => 'Job etudiant',
                    'time' => 'Temps partiel',
                    'city' => 'Calavi',
                ],
                [
                    'id' => 4,
                    'title' => 'CDD Designer Graphique',
                    'subtitle' => 'Subheading',
                    'type' => 'CDD',
                    'time' => 'Temps plein',
                    'city' => 'Parakou',
                ],
            ];
        }

        $this->view('offers.index', [
            'title' => 'HireIn - Offres',
            'activeNav' => 'offers',
            'categories' => $categories,
            'offers' => $offers,
            'search' => ['q' => $query, 'type' => $contractType, 'city' => $city, 'sector' => $sector],
        ]);
    }

    public function store(): void
    {
        $auth = AuthMiddleware::requireAuth(
            'entreprise',
            '/espace-entreprise',
            'Connexion entreprise requise pour publier une offre.'
        );

        $title = trim((string) ($_POST['title'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $contractType = trim((string) ($_POST['contract_type'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $deadline = trim((string) ($_POST['deadline'] ?? ''));

        if ($title === '' || $city === '' || $contractType === '' || $description === '') {
            $this->flashError('Veuillez remplir tous les champs obligatoires de l\'offre.');
            $this->redirect('/espace-entreprise');
        }

        if (!in_array($contractType, ['stage', 'cdd', 'job_etudiant'], true)) {
            $this->flashError('Type de contrat invalide.');
            $this->redirect('/espace-entreprise');
        }

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/espace-entreprise');
        }

        $offerRepository = new OfferRepository(Container::get('db'));
        $offerRepository->create((int) $auth['id'], [
            'title' => $title,
            'city' => $city,
            'contract_type' => $contractType,
            'description' => $description,
            'deadline' => $deadline,
        ]);

        $this->flashSuccess('Offre publiee avec succes.');
        $this->redirect('/espace-entreprise');
    }

    public function close(): void
    {
        $auth = AuthMiddleware::requireAuth(
            'entreprise',
            '/espace-entreprise',
            'Connexion entreprise requise pour fermer une offre.'
        );

        $offerId = (int) ($_POST['offer_id'] ?? 0);
        if ($offerId <= 0) {
            $this->flashError('Offre invalide.');
            $this->redirect('/espace-entreprise');
        }

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/espace-entreprise');
        }

        $offerRepository = new OfferRepository(Container::get('db'));
        if ($offerRepository->closeOffer($offerId, (int) $auth['id'])) {
            $this->flashSuccess('Offre fermee avec succes.');
        } else {
            $this->flashError('Impossible de fermer cette offre.');
        }

        $this->redirect('/espace-entreprise');
    }

    // Action qui affiche une offre en detail.
    public function show(): void
    {
        $offer = null;
        $related = [];

        if (Container::has('db')) {
            $offerRepository = new OfferRepository(Container::get('db'));
            $offerId = (int) ($_GET['id'] ?? 0);
            $row = $offerId > 0 ? $offerRepository->getById($offerId) : null;

            if (is_array($row)) {
                $offer = [
                    'id' => $offerId,
                    'title' => (string) ($row['title'] ?? ''),
                    'description' => (string) ($row['description'] ?? ''),
                    'company_name' => (string) ($row['company_name'] ?? ''),
                    'company_description' => (string) ($row['company_description'] ?? ''),
                    'sector' => (string) ($row['sector'] ?? ''),
                    'city' => (string) ($row['city'] ?? ''),
                    'contract_type' => $this->formatContractType((string) ($row['contract_type'] ?? '')),
                    'deadline' => (string) ($row['deadline'] ?? ''),
                    'logo' => (string) ($row['logo'] ?? ''),
                ];

                $related = $offerRepository->getRelatedBySector((string) ($row['sector'] ?? ''), 4);
            }
        }

        if (!is_array($offer)) {
            $offer = [
                'id' => 0,
                'title' => 'Offre non trouvée',
                'description' => 'L\'offre demandée est introuvable ou a été archivée.',
                'company_name' => 'Entreprise inconnue',
                'company_description' => 'Aucune description disponible.',
                'sector' => '',
                'city' => '',
                'contract_type' => '',
                'deadline' => '',
                'logo' => '',
            ];
        }

        $this->view('offers.show', [
            'title' => 'HireIn - Detail Offre',
            'activeNav' => 'offers',
            'offer' => $offer,
            'related' => $related,
        ]);
    }

    // Action qui affiche le formulaire de candidature.
    public function apply(): void
    {
        $auth = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise pour candidater.'
        );

        $offerId = (int) ($_GET['id'] ?? 0);
        if ($offerId <= 0) {
            $this->flashError('Offre invalide.');
            $this->redirect('/offres');
        }

        $offer = null;
        if (Container::has('db')) {
            $offerRepository = new OfferRepository(Container::get('db'));
            $row = $offerRepository->getById($offerId);

            if (is_array($row)) {
                $offer = [
                    'id' => $offerId,
                    'title' => (string) ($row['title'] ?? ''),
                    'company_name' => (string) ($row['company_name'] ?? ''),
                    'city' => (string) ($row['city'] ?? ''),
                    'contract_type' => $this->formatContractType((string) ($row['contract_type'] ?? '')),
                ];
            }
        }

        if ($offer === null) {
            $this->flashError('Offre non trouvee.');
            $this->redirect('/offres');
        }

        $this->view('offers.apply', [
            'title' => 'HireIn - Candidature',
            'activeNav' => 'offers',
            'offer' => $offer,
            'old' => $_SESSION['old'] ?? [],
        ]);

        unset($_SESSION['old']);
    }

    // Action qui traite la soumission de candidature.
    public function submitApplication(): void
    {
        $auth = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise pour candidater.'
        );

        $offerId = (int) ($_POST['offer_id'] ?? 0);
        if ($offerId <= 0) {
            $this->flashError('Offre invalide.');
            $this->redirect('/offres');
        }

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/offres');
        }

        $coverLetter = trim((string) ($_POST['cover_letter'] ?? ''));
        if ($coverLetter === '') {
            $_SESSION['old'] = $_POST;
            $this->flashError('Veuillez ecrire une lettre de motivation.');
            $this->redirect('/offres/candidater?id=' . $offerId);
        }

        $applicationRepository = new \App\Repositories\ApplicationRepository(Container::get('db'));
        $studentUserId = (int) $auth['id'];

        // Vérifier si l'étudiant a déjà candidaté à cette offre.
        if ($applicationRepository->hasApplied($studentUserId, $offerId)) {
            $this->flashError('Vous avez deja candidate a cette offre.');
            $this->redirect('/offres');
        }

        // Traiter l'upload du CV si fourni.
        $cvPath = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $cvPath = $this->handleCvUpload($_FILES['cv'], $studentUserId);
            if ($cvPath === null) {
                $_SESSION['old'] = $_POST;
                $this->flashError('Erreur lors du telechargement du CV.');
                $this->redirect('/offres/candidater?id=' . $offerId);
            }
        }

        // Créer la candidature.
        $applicationRepository->create($studentUserId, $offerId, $coverLetter, $cvPath);

        // Envoyer notification par email à l'entreprise (si email disponible).
        if (Container::has('db')) {
            $offerRepository = new \App\Repositories\OfferRepository(Container::get('db'));
            $offerRow = $offerRepository->getById($offerId);
            $companyEmail = is_array($offerRow) ? ($offerRow['company_email'] ?? null) : null;
            $studentName = (string) ($auth['fullname'] ?? 'Un candidat');

            if (!empty($companyEmail) && filter_var($companyEmail, FILTER_VALIDATE_EMAIL)) {
                $subject = 'Nouvelle candidature pour l\'offre: ' . ($offerRow['title'] ?? 'offre');
                $message = "Bonjour,\n\nVous avez recu une nouvelle candidature de $studentName pour votre offre." . "\n\nConsultez votre espace entreprise pour plus de details.";
                $headers = 'From: no-reply@hirein.local' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
                @mail($companyEmail, $subject, $message, $headers);
            }

            // Confirmation au candidat
            $studentEmail = $auth['email'] ?? null;
            if (!empty($studentEmail) && filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
                $subject = 'Votre candidature a bien ete envoyee';
                $message = "Bonjour $studentName,\n\nVotre candidature a bien ete envoyee pour l'offre: " . ($offerRow['title'] ?? '') . ".\n\nBonne chance!";
                $headers = 'From: no-reply@hirein.local' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
                @mail($studentEmail, $subject, $message, $headers);
            }

        }

        $this->flashSuccess('Candidature envoyee avec succes. Bonne chance!');
        $this->redirect('/profil/candidatures');
    }

    /**
     * Traite l'upload du fichier CV.
     *
     * @param array $file
     * @param int $studentUserId
     * @return string|null
     */
    private function handleCvUpload(array $file, int $studentUserId): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/cv/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($file['type'], $allowedTypes, true)) {
            return null;
        }

        $maxSize = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $maxSize) {
            return null;
        }

        $filename = 'cv_' . $studentUserId . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return null;
        }

        return '/uploads/cv/' . $filename;
    }

    private function formatContractType(string $contractType): string
    {
        return match ($contractType) {
            'stage' => 'Stage professionnel',
            'cdd' => 'CDD',
            'job_etudiant' => 'Job etudiant',
            default => ucfirst(str_replace('_', ' ', $contractType)),
        };
    }

    private function formatTimeLabel(string $deadline): string
    {
        return $deadline !== '' ? 'Date limite: ' . $deadline : 'Temps plein';
    }

    protected function flashError(string $message): void
    {
        $_SESSION['flash'] = ['type' => 'error', 'message' => $message];
    }

    protected function flashSuccess(string $message): void
    {
        $_SESSION['flash'] = ['type' => 'success', 'message' => $message];
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}