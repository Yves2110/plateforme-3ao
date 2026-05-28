<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'pdf'     => 'Guide technique',
            'video'   => 'Vidéo',
            'article' => 'Publication scientifique',
            'guide'   => 'Guide technique',
            'rapport' => 'Publication scientifique',
            'Document' => 'Guide technique',
        ];

        foreach ($map as $old => $new) {
            DB::table('resources')->where('type', $old)->update(['type' => $new]);
        }
    }

    public function down(): void
    {
        // Non réversible sans perte d'information
    }
};
