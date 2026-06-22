<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuthMiddleware;
use App\Core\Container;
use App\Core\Controller;
use App\Repositories\ApplicationRepository;
use App\Repositories\MessageRepository;
use App\Repositories\OfferRepository;
use App\Repositories\UserRepository;
use RuntimeException;

final class AuthController extends Controller
{
    // Page d'inscription etudiant.
    public function registerStudent(): void
    {
        AuthMiddleware::requireGuest('/espace-entreprise');

        $this->view('auth.register-student', [
            'title' => 'HireIn - Inscription Etudiant',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    // Page d'inscription entreprise.
    public function registerCompany(): void
    {
        AuthMiddleware::requireGuest('/espace-entreprise');

        $this->view('auth.register-company', [
            'title' => 'HireIn - Inscription Entreprise',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'old' => $this->pullOld(),
        ]);
    }

    // Page de connexion et espace entreprise.
    public function companySpace(): void
    {
        $authUser = $_SESSION['auth'] ?? null;
        $companyOffers = [];
        $companyProfile = null;

        if (is_array($authUser) && ($authUser['role'] ?? '') === 'etudiant') {
            $this->flashError('Acces reserve aux entreprises.');
            $this->redirect('/profil/candidatures');
        }

        if (is_array($authUser) && ($authUser['role'] ?? '') === 'entreprise' && Container::has('db')) {
            $offerRepository = new OfferRepository(Container::get('db'));
            $companyOffers = $offerRepository->getByCompanyUserId((int) $authUser['id']);

            $userRepository = new UserRepository(Container::get('db'));
            $companyProfile = $userRepository->getCompanyProfileByUserId((int) $authUser['id']);
        }

        $this->view('auth.company-space', [
            'title' => 'HireIn - Espace Entreprise',
            'activeNav' => 'offers',
            'flash' => $this->pullFlash(),
            'authUser' => $authUser,
            'companyOffers' => $companyOffers,
            'companyProfile' => $companyProfile,
        ]);
    }

    // Page des candidatures recues par l'entreprise.
    public function companyApplications(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'entreprise',
            '/espace-entreprise',
            'Connexion entreprise requise.'
        );

        $applications = [];
        if (Container::has('db')) {
            $applicationRepository = new ApplicationRepository(Container::get('db'));
            $applications = $applicationRepository->getByCompany((int) $authUser['id'], 50, 0);
        }

        $this->view('company.applications', [
            'title' => 'HireIn - Candidatures Recues',
            'activeNav' => 'offers',
            'flash' => $this->pullFlash(),
            'applications' => $applications,
        ]);
    }

    public function updateApplicationStatus(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'entreprise',
            '/espace-entreprise',
            'Connexion entreprise requise.'
        );

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $status = trim((string) ($_POST['status'] ?? ''));

        if ($applicationId <= 0 || !in_array($status, ['reviewed', 'accepted', 'rejected'], true)) {
            $this->flashError('Parametres de statut invalides.');
            $this->redirect('/entreprise/candidatures');
        }

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/entreprise/candidatures');
        }

        $applicationRepository = new ApplicationRepository(Container::get('db'));
        $application = $applicationRepository->getById($applicationId);
        if (!is_array($application) || (int) ($application['company_user_id'] ?? 0) !== (int) $authUser['id']) {
            $this->flashError('Candidature introuvable ou acces non autorise.');
            $this->redirect('/entreprise/candidatures');
        }

        if ($applicationRepository->updateStatus($applicationId, $status)) {
            $this->flashSuccess('Statut de candidature mis a jour.');
        } else {
            $this->flashError('Impossible de mettre a jour le statut.');
        }

        $this->redirect('/entreprise/candidatures');
    }

    public function registerStudentSubmit(): void
    {
        AuthMiddleware::requireGuest('/espace-entreprise');

        $fullname = trim((string) ($_POST['fullname'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $old = [
            'fullname' => $fullname,
            'city' => trim((string) ($_POST['city'] ?? '')),
            'level' => trim((string) ($_POST['level'] ?? '')),
            'search_sector' => trim((string) ($_POST['search_sector'] ?? '')),
            'university' => trim((string) ($_POST['university'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'skills' => trim((string) ($_POST['skills'] ?? '')),
            'email' => $email,
        ];

        if ($fullname === '' || $email === '' || $password === '') {
            $this->flashError('Veuillez remplir tous les champs obligatoires.');
            $this->storeOld($old);
            $this->redirect('/inscription');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashError('Email invalide.');
            $this->storeOld($old);
            $this->redirect('/inscription');
        }

        if ($password !== $passwordConfirm) {
            $this->flashError('Les mots de passe ne correspondent pas.');
            $this->storeOld($old);
            $this->redirect('/inscription');
        }

        $profilePhotoPath = '';
        if (isset($_FILES['profile_photo']) && is_uploaded_file($_FILES['profile_photo']['tmp_name'])) {
            $profilePhotoPath = $this->handleProfilePhotoUpload($_FILES['profile_photo']);
            if ($profilePhotoPath === null) {
                $this->flashError('Erreur lors de l\'upload de la photo de profil.');
                $this->storeOld($old);
                $this->redirect('/inscription');
            }
        }

        $cvPath = '';
        if (isset($_FILES['cv']) && is_uploaded_file($_FILES['cv']['tmp_name'])) {
            $cvPath = $this->handleStudentCvUpload($_FILES['cv']);
            if ($cvPath === null) {
                $this->flashError('Erreur lors de l\'upload du CV.');
                $this->storeOld($old);
                $this->redirect('/inscription');
            }
        }

        try {
            $this->userRepository()->createStudent([
                'fullname' => $fullname,
                'email' => $email,
                'password' => $password,
                'city' => $old['city'],
                'level' => $old['level'],
                'university' => $old['university'],
                'skills' => $old['skills'],
                'search_sector' => $old['search_sector'],
                'phone' => $old['phone'],
                'profile_photo' => $profilePhotoPath,
                'cv' => $cvPath,
            ]);

            $this->flashSuccess('Compte etudiant cree avec succes. Connectez-vous.');
            $this->redirect('/espace-entreprise');
        } catch (RuntimeException $exception) {
            $this->flashError($exception->getMessage());
            $this->storeOld($old);
            $this->redirect('/inscription');
        }
    }

    private function handleProfilePhotoUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowed, true)) {
            $this->flashError('Format de photo non pris en charge (jpeg,png,webp,gif).');
            return null;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $this->flashError('La photo de profil doit faire moins de 2 Mo.');
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('profile_', true) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $dest = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }

        return '/uploads/photos/' . $filename;
    }

    private function handleStudentCvUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($mime, $allowed, true)) {
            $this->flashError('Format de CV non pris en charge (pdf, doc, docx).');
            return null;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $this->flashError('Le CV doit faire moins de 5 Mo.');
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cv_', true) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/cv/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $dest = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }

        return '/uploads/cv/' . $filename;
    }

    public function registerCompanySubmit(): void
    {
        AuthMiddleware::requireGuest('/espace-entreprise');

        $companyName = trim((string) ($_POST['company_name'] ?? ''));
        $recruiterName = trim((string) ($_POST['recruiter_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $old = [
            'company_name' => $companyName,
            'recruiter_name' => $recruiterName,
            'city' => trim((string) ($_POST['city'] ?? '')),
            'sector' => trim((string) ($_POST['sector'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'email' => $email,
        ];

        if ($companyName === '' || $recruiterName === '' || $email === '' || $password === '') {
            $this->flashError('Veuillez remplir tous les champs obligatoires.');
            $this->storeOld($old);
            $this->redirect('/inscription-entreprise');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashError('Email invalide.');
            $this->storeOld($old);
            $this->redirect('/inscription-entreprise');
        }

        if ($password !== $passwordConfirm) {
            $this->flashError('Les mots de passe ne correspondent pas.');
            $this->storeOld($old);
            $this->redirect('/inscription-entreprise');
        }

        try {
            $logoPath = '';
            if (isset($_FILES['logo']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
                $file = $_FILES['logo'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($file['tmp_name']);
                    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                    if (!in_array($mime, $allowed, true)) {
                        $this->flashError('Format de logo non pris en charge (jpeg,png,webp,gif).');
                        $this->storeOld($old);
                        $this->redirect('/inscription-entreprise');
                    }

                    if ($file['size'] > 2 * 1024 * 1024) {
                        $this->flashError('Le logo doit faire moins de 2 Mo.');
                        $this->storeOld($old);
                        $this->redirect('/inscription-entreprise');
                    }

                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('logo_', true) . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../public/uploads/logos/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $dest = $uploadDir . $filename;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $logoPath = '/uploads/logos/' . $filename;
                    } else {
                        $this->flashError('Impossible d\'enregistrer le logo.');
                        $this->storeOld($old);
                        $this->redirect('/inscription-entreprise');
                    }
                } else {
                    $this->flashError('Erreur lors de l\'upload du logo.');
                    $this->storeOld($old);
                    $this->redirect('/inscription-entreprise');
                }
            }

            $this->userRepository()->createCompany([
                'company_name' => $companyName,
                'recruiter_name' => $recruiterName,
                'email' => $email,
                'password' => $password,
                'city' => $old['city'],
                'sector' => $old['sector'],
                'description' => $old['description'],
                'phone' => $old['phone'],
                'logo' => $logoPath,
            ]);

            $this->flashSuccess('Compte entreprise cree avec succes. Connectez-vous.');
            $this->redirect('/espace-entreprise');
        } catch (RuntimeException $exception) {
            $this->flashError($exception->getMessage());
            $this->storeOld($old);
            $this->redirect('/inscription-entreprise');
        }
    }

    public function loginSubmit(): void
    {
        AuthMiddleware::requireGuest('/espace-entreprise');

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->flashError('Veuillez renseigner email et mot de passe.');
            $this->redirect('/espace-entreprise');
        }

        $user = $this->userRepository()->findByEmail($email);
        if (!is_array($user) || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $this->flashError('Identifiants invalides.');
            $this->redirect('/espace-entreprise');
        }

        $_SESSION['auth'] = [
            'id' => (int) $user['id'],
            'role' => (string) $user['role'],
            'fullname' => (string) $user['fullname'],
            'email' => (string) $user['email'],
        ];

        $this->flashSuccess('Connexion reussie.');
        $role = (string) $user['role'];
        if ($role === 'admin') {
            $this->redirect('/admin');
        }
        if ($role === 'etudiant') {
            $this->redirect('/profil');
        }
        $this->redirect('/espace-entreprise');
    }

    public function logoutSubmit(): void
    {
        unset($_SESSION['auth']);
        $this->flashSuccess('Vous etes deconnecte.');
        $this->redirect('/espace-entreprise');
    }

    // Page du tableau de bord etudiant affichant les candidatures.
    public function studentApplications(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise.'
        );

        $applications = [];
        if (Container::has('db')) {
            $applicationRepository = new \App\Repositories\ApplicationRepository(Container::get('db'));
            $applications = $applicationRepository->getByStudent((int) $authUser['id'], 50, 0);
        }

        $this->view('profile.applications', [
            'title' => 'HireIn - Mes Candidatures',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'applications' => $applications,
        ]);
    }

    public function profileDashboard(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise.'
        );

        $profileRepository = new UserRepository(Container::get('db'));
        $applicationRepository = new ApplicationRepository(Container::get('db'));
        $messageRepository = new MessageRepository(Container::get('db'));

        $profile = $profileRepository->getStudentProfileByUserId((int) $authUser['id']) ?? [];
        $applications = $applicationRepository->getByStudent((int) $authUser['id'], 5, 0);
        $messages = $messageRepository->getMessagesForUser((int) $authUser['id'], 5);

        $this->view('profile.dashboard', [
            'title' => 'HireIn - Mon tableau de bord',
            'activeNav' => 'profiles',
            'profile' => $profile,
            'applications' => $applications,
            'messages' => $messages,
        ]);
    }

    public function editProfile(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise.'
        );

        $profile = $this->userRepository()->getStudentProfileByUserId((int) $authUser['id']);
        if ($profile === null) {
            $this->flashError('Profil etudiant introuvable.');
            $this->redirect('/profil');
        }

        $this->view('profile.edit', [
            'title' => 'HireIn - Edition du profil',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'profile' => $profile,
        ]);
    }

    public function updateProfile(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise.'
        );

        $profile = $this->userRepository()->getStudentProfileByUserId((int) $authUser['id']);
        if ($profile === null) {
            $this->flashError('Profil etudiant introuvable.');
            $this->redirect('/profil');
        }

        $fullname = trim((string) ($_POST['fullname'] ?? ''));
        $university = trim((string) ($_POST['university'] ?? ''));
        $level = trim((string) ($_POST['level'] ?? ''));
        $searchSector = trim((string) ($_POST['search_sector'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $skills = trim((string) ($_POST['skills'] ?? ''));

        if ($fullname === '') {
            $this->flashError('Le nom complet est requis.');
            $this->redirect('/profil/edition');
        }

        $profilePhotoPath = (string) ($profile['profile_photo'] ?? '');
        if (isset($_FILES['profile_photo']) && is_uploaded_file($_FILES['profile_photo']['tmp_name'])) {
            $upload = $this->handleProfilePhotoUpload($_FILES['profile_photo']);
            if ($upload === null) {
                $this->flashError('Erreur lors de l’upload de la photo.');
                $this->redirect('/profil/edition');
            }
            $profilePhotoPath = $upload;
        }

        $cvPath = (string) ($profile['cv'] ?? '');
        if (isset($_FILES['cv']) && is_uploaded_file($_FILES['cv']['tmp_name'])) {
            $upload = $this->handleStudentCvUpload($_FILES['cv']);
            if ($upload === null) {
                $this->flashError('Erreur lors de l’upload du CV.');
                $this->redirect('/profil/edition');
            }
            $cvPath = $upload;
        }

        $updateData = [
            'fullname' => $fullname,
            'university' => $university,
            'level' => $level,
            'search_sector' => $searchSector,
            'city' => $city,
            'phone' => $phone,
            'skills' => $skills,
            'profile_photo' => $profilePhotoPath,
            'cv' => $cvPath,
        ];

        if (!$this->userRepository()->updateStudentProfile((int) $authUser['id'], $updateData)) {
            $this->flashError('Impossible de mettre à jour le profil.');
            $this->redirect('/profil/edition');
        }

        $this->flashSuccess('Profil mis à jour avec succès.');
        $this->redirect('/profil');
    }

    public function messages(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise.'
        );

        $messageRepository = new MessageRepository(Container::get('db'));
        $companies = $this->userRepository()->getCompanies('');
        $messages = $messageRepository->getMessagesForUser((int) $authUser['id'], 30);

        $this->view('profile.messages', [
            'title' => 'HireIn - Messagerie',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'companies' => $companies,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(): void
    {
        $authUser = AuthMiddleware::requireAuth(
            'etudiant',
            '/inscription',
            'Connexion etudiant requise.'
        );

        $recipientId = (int) ($_POST['recipient_id'] ?? 0);
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $body = trim((string) ($_POST['body'] ?? ''));

        if ($recipientId <= 0 || $subject === '' || $body === '') {
            $this->flashError('Tous les champs du message sont obligatoires.');
            $this->redirect('/profil/messagerie');
        }

        if ($this->userRepository()->getCompanyProfileByUserId($recipientId) === null) {
            $this->flashError('Entreprise destinataire invalide.');
            $this->redirect('/profil/messagerie');
        }

        $messageRepository = new MessageRepository(Container::get('db'));
        if (!$messageRepository->create((int) $authUser['id'], $recipientId, $subject, $body)) {
            $this->flashError('Impossible d’envoyer le message.');
            $this->redirect('/profil/messagerie');
        }

        $this->flashSuccess('Message envoyé avec succès.');
        $this->redirect('/profil/messagerie');
    }

}
