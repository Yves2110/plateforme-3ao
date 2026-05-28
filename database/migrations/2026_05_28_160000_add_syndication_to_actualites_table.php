<?php

use App\Models\Actualite;
use App\Models\RssFeedItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actualites', function (Blueprint $table) {
            $table->string('syndicated_source')->nullable()->after('category');
            $table->string('source_url', 500)->nullable()->after('syndicated_source');
        });

        RssFeedItem::query()
            ->where('status', 'approved')
            ->whereNotNull('actualite_id')
            ->with('source')
            ->each(function (RssFeedItem $item) {
                if (! $item->source || ! $item->actualite_id) {
                    return;
                }

                Actualite::whereKey($item->actualite_id)->update([
                    'syndicated_source' => $item->source->name,
                    'source_url'        => $item->link,
                    'category'          => 'Actualité',
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('actualites', function (Blueprint $table) {
            $table->dropColumn(['syndicated_source', 'source_url']);
        });
    }
};
