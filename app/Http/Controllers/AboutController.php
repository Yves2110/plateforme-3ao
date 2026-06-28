<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(Request $request): View
    {
        $partners = [
            ['name' => 'ROPPA', 'description' => 'Réseau des organisations paysannes et de producteurs agricoles de l\'Afrique de l\'Ouest'],
            ['name' => 'IPES-Food', 'description' => 'International Panel of Experts on Sustainable Food Systems'],
            ['name' => 'AFSA', 'description' => 'Alliance for Food Sovereignty in Africa'],
            ['name' => 'Enda Pronat', 'description' => 'Programme de promotion de l\'agroécologie et des semences paysannes'],
            ['name' => 'CIRAD', 'description' => 'Centre de coopération internationale en recherche agronomique pour le développement'],
            ['name' => 'ACF', 'description' => 'Action Contre la Faim'],
            ['name' => 'JAFOWA', 'description' => 'Jeunes Agriculteurs et Agricultrices de l\'Afrique de l\'Ouest'],
            ['name' => 'FENAB', 'description' => 'Fédération nationale des associations de producteurs agricoles du Burkina'],
            ['name' => 'COPAGEN', 'description' => 'Coalition pour la protection du patrimoine génétique africain'],
            ['name' => 'TAFAE', 'description' => 'Task Force Multi-Acteurs pour la Promotion de l\'Agroécologie'],
        ];

        $steeringCommittee = [
            ['name' => 'ROPPA', 'role' => 'Représentant des organisations paysannes'],
            ['name' => 'IPES-Food', 'role' => 'Expertise scientifique et recherche'],
            ['name' => 'AFSA', 'role' => 'Alliance for Food Sovereignty in Africa'],
            ['name' => 'Enda Pronat', 'role' => 'Coordination et appui technique'],
            ['name' => 'CIRAD', 'role' => 'Recherche agronomique et innovation'],
            ['name' => 'ACF', 'role' => 'Action humanitaire et nutrition'],
            ['name' => 'JAFOWA', 'role' => 'Représentation des jeunes agriculteurs'],
        ];

        return view('about.index', [
            'partners' => $partners,
            'steeringCommittee' => $steeringCommittee,
        ]);
    }
}
