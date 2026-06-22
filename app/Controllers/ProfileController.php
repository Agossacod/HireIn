<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Container;
use App\Repositories\UserRepository;

final class ProfileController extends Controller
{
    // Action qui affiche l'annuaire des profils etudiants.
    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $profiles = [];

        if (Container::has('db')) {
            $repository = new UserRepository(Container::get('db'));
            $rows = $repository->getStudentProfiles($search, 50, 0);

                foreach ($rows as $profile) {
                    $profiles[] = [
                        'name' => (string) ($profile['name'] ?? ''),
                        'job' => (string) ($profile['level'] ?? 'Étudiant'),
                        'skills' => (string) ($profile['skills'] ?? ''),
                        'city' => (string) ($profile['city'] ?? ''),
                        'university' => (string) ($profile['university'] ?? ''),
                        'phone' => (string) ($profile['phone'] ?? ''),
                        'profile_photo' => (string) ($profile['profile_photo'] ?? ''),
                        'cv' => (string) ($profile['cv'] ?? ''),
                    ];
                }
        }

        if ($profiles === []) {
            $profiles = [];
        }

        $this->view('profiles.index', [
            'title' => 'HireIn - Profils Etudiants',
            'activeNav' => 'profiles',
            'profiles' => $profiles,
            'search' => $search,
        ]);
    }
}
