<?php

namespace Database\Seeders;

use App\Models\HomePartner;
use Illuminate\Database\Seeder;

class HomePartnerSeeder extends Seeder
{
    public function run(): void
    {
        if (HomePartner::exists()) {
            return;
        }

        $names = ['ROPPA', 'CIRAD', 'FAO', 'GIZ', 'ARAA', 'CEDEAO', 'ENDA-PRONAT'];

        foreach ($names as $order => $name) {
            HomePartner::create([
                'name'       => $name,
                'sort_order' => $order,
                'is_active'  => true,
            ]);
        }
    }
}
