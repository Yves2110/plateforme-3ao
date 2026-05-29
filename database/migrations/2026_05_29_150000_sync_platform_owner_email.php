<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $ownerEmail = config('platform.owner_email');

        User::query()->where('is_platform_owner', true)->update(['is_platform_owner' => false]);

        $owner = User::where('email', $ownerEmail)->first();

        if ($owner) {
            $owner->forceFill(['is_platform_owner' => true])->save();
        }
    }

    public function down(): void
    {
        User::where('email', config('platform.owner_email'))->update(['is_platform_owner' => false]);
    }
};
