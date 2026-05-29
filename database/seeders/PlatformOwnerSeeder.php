<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PlatformOwnerSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $ownerEmail = config('platform.owner_email');

        User::query()->where('is_platform_owner', true)->update(['is_platform_owner' => false]);

        $owner = User::where('email', $ownerEmail)->first();

        if (! $owner) {
            $owner = User::create([
                'name'              => 'Ismael Yves Kaboré',
                'email'             => $ownerEmail,
                'password'          => bcrypt('Admin3AO@2026!'),
                'email_verified_at' => now(),
                'approval_status'   => 'approved',
                'approved_at'       => now(),
                'organization'      => 'Secrétariat 3AO',
                'country'           => 'Burkina Faso',
                'is_active'         => true,
                'is_platform_owner' => true,
            ]);

            $this->command?->info("Compte propriétaire créé : {$ownerEmail}");
        } else {
            $owner->forceFill([
                'is_platform_owner' => true,
                'approval_status'   => 'approved',
                'approved_at'       => $owner->approved_at ?? now(),
                'email_verified_at' => $owner->email_verified_at ?? now(),
                'is_active'         => true,
            ])->save();

            $this->command?->info("Compte propriétaire mis à jour : {$ownerEmail}");
        }

        if (! $owner->hasRole('super_admin')) {
            $owner->assignRole('super_admin');
        }

        User::where('email', 'admin@3ao.org')->update(['is_platform_owner' => false]);

        $this->command?->info('Mot de passe initial (si compte créé) : Admin3AO@2026! — changez-le après connexion.');
    }
}
