<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /** @var list<string> */
    protected array $tables = [
        'users',
        'actualites',
        'media',
        'events',
        'resources',
        'actors',
        'formations',
        'forum_threads',
        'hero_slides',
        'home_partners',
        'rss_sources',
        'newsletter_campaigns',
        'newsletter_subscribers',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'uuid')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->uuid('uuid')->nullable()->unique()->after('id');
            });

            DB::table($table)->whereNull('uuid')->orderBy('id')->lazyById()->each(function ($row) use ($table) {
                DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('uuid');
                });
            }
        }
    }
};
