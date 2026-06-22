<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class HomeController extends Controller
{
    // Action de la page d'accueil.
    public function index(): void
    {
        $heroTags = ['Localisation', 'Secteur d\'activite', 'Type de contrat'];

        $talents = [
            ['name' => 'Alice Fagnon', 'role' => 'Etudiante - AGC', 'tone' => 'sand', 'stars' => 4],
            ['name' => 'Alfred Assogba', 'role' => 'Developpeur', 'tone' => 'blue', 'stars' => 5],
            ['name' => 'Elvire Dossa', 'role' => 'Etudiante - MCC', 'tone' => 'mint', 'stars' => 3],
            ['name' => 'Aline Yessono', 'role' => 'Etudiante', 'tone' => 'sand', 'stars' => 4],
            ['name' => 'Marie Akindji', 'role' => 'Gestionnaire projet', 'tone' => 'blue', 'stars' => 5],
            ['name' => 'Aime Gando', 'role' => 'Etudiante', 'tone' => 'mint', 'stars' => 4],
            ['name' => 'Alice Fagnon', 'role' => 'Etudiante - AGC', 'tone' => 'sand', 'stars' => 4],
            ['name' => 'Alfred Assogba', 'role' => 'Developpeur', 'tone' => 'blue', 'stars' => 5],
        ];

        $cities = [
            'Parakou',
            'Dassa-Zoume',
            'Savalou',
            'Porto-Novo',
            'Abomey',
            'Kandi',
            'Natitingou',
            'Lokossa',
            'Akpakpa',
            'Abomey-Calavi',
            'Cotonou',
            'Ouidah',
        ];

        // Envoie le titre a la vue home.
        $this->view('home', [
            'title' => 'HireIn - Accueil',
            'activeNav' => 'home',
            'heroTags' => $heroTags,
            'talents' => $talents,
            'cities' => $cities,
        ]);
    }

    // Action de la page a propos.
    public function about(): void
    {
        $this->view('about', [
            'title' => 'HireIn - A propos',
            'activeNav' => 'about',
        ]);
    }
}
