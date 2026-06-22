<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Container;
use App\Repositories\UserRepository;

final class CompanyController extends Controller
{
    // Action qui affiche l'annuaire des entreprises.
    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $companies = [];

        if (Container::has('db')) {
            $repository = new UserRepository(Container::get('db'));
            $companies = $repository->getCompanies($search);
        }

        $sectors = [];
        foreach ($companies as $company) {
            if (!empty($company['sector']) && !in_array($company['sector'], $sectors, true)) {
                $sectors[] = $company['sector'];
            }
        }

        $this->view('companies.index', [
            'title' => 'HireIn - Annuaire Entreprises',
            'activeNav' => 'profiles',
            'companies' => $companies,
            'sectors' => $sectors,
            'search' => $search,
        ]);
    }
}
