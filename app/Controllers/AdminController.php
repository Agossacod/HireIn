<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AuthMiddleware;
use App\Core\Container;
use App\Core\Controller;
use App\Repositories\UserRepository;
use RuntimeException;

final class AdminController extends Controller
{
    public function index(): void
    {
        $auth = AuthMiddleware::requireAuth('admin', '/admin/login', 'Acces admin requis.');

        $this->view('admin.dashboard', [
            'title' => 'HireIn - Administration',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'authUser' => $auth,
        ]);
    }

    public function loginPage(): void
    {
        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/');
        }

        $repo = new UserRepository(Container::get('db'));
        $hasAdmin = $repo->countAdmins() > 0;

        $this->view('admin.login', [
            'title' => 'HireIn - Connexion Admin',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'hasAdmin' => $hasAdmin,
        ]);
    }

    public function loginSubmit(): void
    {
        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/admin/login');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->flashError('Email et mot de passe requis.');
            $this->redirect('/admin/login');
        }

        $repo = new UserRepository(Container::get('db'));
        $user = $repo->findByEmail($email);

        if (!is_array($user) || (string) ($user['role'] ?? '') !== 'admin' || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $this->flashError('Identifiants administrateur invalides.');
            $this->redirect('/admin/login');
        }

        $_SESSION['auth'] = [
            'id' => (int) $user['id'],
            'role' => 'admin',
            'fullname' => (string) $user['fullname'],
            'email' => (string) $user['email'],
        ];

        $this->flashSuccess('Connexion administrateur reussie.');
        $this->redirect('/admin');
    }

    public function setupAdmin(): void
    {
        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/admin/login');
        }

        $repo = new UserRepository(Container::get('db'));
        if ($repo->countAdmins() > 0) {
            $this->flashError('Un compte administrateur existe deja.');
            $this->redirect('/admin/login');
        }

        $fullname = trim((string) ($_POST['fullname'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        if ($fullname === '' || $email === '' || $password === '' || $passwordConfirm === '') {
            $this->flashError('Tous les champs sont obligatoires pour creer le compte administrateur.');
            $this->redirect('/admin/login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashError('Email administrateur invalide.');
            $this->redirect('/admin/login');
        }

        if ($password !== $passwordConfirm) {
            $this->flashError('Les mots de passe ne correspondent pas.');
            $this->redirect('/admin/login');
        }

        try {
            $adminId = $repo->createAdmin($fullname, $email, $password);
        } catch (RuntimeException $exception) {
            $this->flashError($exception->getMessage());
            $this->redirect('/admin/login');
        }

        $_SESSION['auth'] = [
            'id' => $adminId,
            'role' => 'admin',
            'fullname' => $fullname,
            'email' => $email,
        ];

        $this->flashSuccess('Compte administrateur cree avec succes.');
        $this->redirect('/admin');
    }

    public function users(): void
    {
        AuthMiddleware::requireAuth('admin','/admin/login','Acces admin requis.') ;

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/admin');
        }

        $repo = new UserRepository(Container::get('db'));
        $users = $repo->getAllUsers();

        $this->view('admin.users', [
            'title' => 'HireIn - Gestion utilisateurs',
            'activeNav' => 'profiles',
            'flash' => $this->pullFlash(),
            'users' => $users,
        ]);
    }

    public function updateUserRole(): void
    {
        $auth = AuthMiddleware::requireAuth('admin','/admin/login','Acces admin requis.');

// AuthMiddleware::requireAuth('admin', '/espace-entreprise', 'Acces admin requis.');
        $userId = (int) ($_POST['user_id'] ?? 0);
        $role = trim((string) ($_POST['role'] ?? ''));

        if ($userId <= 0 || !in_array($role, ['etudiant', 'entreprise', 'admin'], true)) {
            $this->flashError('Parametres invalides.');
            $this->redirect('/admin/utilisateurs');
        }

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/admin/utilisateurs');
        }

        $repo = new UserRepository(Container::get('db'));
        if ($repo->updateUserRole($userId, $role)) {
            $this->flashSuccess('Role utilisateur mis a jour.');
        } else {
            $this->flashError('Impossible de mettre a jour le role.');
        }

        $this->redirect('/admin/utilisateurs');
    }

    public function offers(): void
    {
        AuthMiddleware::requireAuth(
            'admin',
            '/admin/login',
            'Acces admin requis.'
        );

        // Charger les offres...
    }

    public function companies(): void
    {
        AuthMiddleware::requireAuth(
            'admin',
            '/admin/login',
            'Acces admin requis.'
        );

        // Charger les entreprises...
    }

    public function deleteUser(): void
    {
        AuthMiddleware::requireAuth('admin','/admin/login','Acces admin requis.');


        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->flashError('Utilisateur invalide.');
            $this->redirect('/admin/utilisateurs');
        }

        if (!Container::has('db')) {
            $this->flashError('Connexion base de donnees indisponible.');
            $this->redirect('/admin/utilisateurs');
        }

        $repo = new UserRepository(Container::get('db'));
        if ($repo->deleteUser($userId)) {
            $this->flashSuccess('Utilisateur supprime.');
        } else {
            $this->flashError('Impossible de supprimer l\'utilisateur.');
        }

        $this->redirect('/admin/utilisateurs');
    }
}
