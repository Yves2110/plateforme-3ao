<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_platform_owner')->default(false)->after('is_active');
        });

        $ownerEmail = config('platform.owner_email');
        $owner = User::where('email', $ownerEmail)->first();

        if ($owner) {
            User::query()->where('is_platform_owner', true)->update(['is_platform_owner' => false]);
            $owner->forceFill(['is_platform_owner' => true])->save();
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_platform_owner');
        });
    }
};
