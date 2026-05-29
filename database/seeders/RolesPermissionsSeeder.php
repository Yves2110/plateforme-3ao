<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===== Permissions (10 du CDC §7.1) =====
        $permissions = [
            'publier-bibliotheque'      => 'Publier dans la bibliothèque',
            'moderer-forum'             => 'Modérer le forum',
            'gerer-carte'               => 'Gérer la carte interactive',
            'soumettre-acteur'          => 'Soumettre une fiche acteur',
            'creer-evenements'          => 'Créer des événements',
            'gerer-rss'                 => 'Gérer les flux RSS',
            'publier-actualites'        => 'Publier des actualités',
            'administrer-utilisateurs'  => 'Administrer les utilisateurs',
            'acceder-statistiques'      => 'Accéder aux statistiques',
            'telecharger-documents'     => 'Télécharger des documents',
            'contribuer-multimedia'     => 'Contribuer au multimédia',
            'gerer-newsletter'          => 'Gérer la newsletter',
            'gerer-formations'          => 'Gérer les formations',
            'valider-inscriptions'      => 'Valider les inscriptions membres',
        ];

        foreach ($permissions as $slug => $label) {
            Permission::firstOrCreate(
                ['name' => $slug],
                ['guard_name' => 'web']
            );
        }

        // ===== Rôles par défaut (CDC §7.2) =====

        // Administrateur système — accès total
        $admin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin->syncPermissions(array_keys($permissions));

        // Modérateur
        $moderateur = Role::firstOrCreate(['name' => 'moderateur', 'guard_name' => 'web']);
        $moderateur->syncPermissions([
            'moderer-forum',
            'publier-actualites',
            'acceder-statistiques',
            'gerer-carte',
            'gerer-newsletter',
            'valider-inscriptions',
        ]);

        // Validateur d'inscriptions (accès limité : file d'attente + e-mail d'alerte)
        $validateur = Role::firstOrCreate(['name' => 'validateur_inscriptions', 'guard_name' => 'web']);
        $validateur->syncPermissions(['valider-inscriptions']);

        // Contributeur (Membre 3AO)
        $contributeur = Role::firstOrCreate(['name' => 'contributeur', 'guard_name' => 'web']);
        $contributeur->syncPermissions([
            'publier-bibliotheque',
            'soumettre-acteur',
            'creer-evenements',
            'contribuer-multimedia',
            'telecharger-documents',
        ]);

        // Partenaire externe
        $partenaire = Role::firstOrCreate(['name' => 'partenaire_externe', 'guard_name' => 'web']);
        $partenaire->syncPermissions([
            'telecharger-documents',
        ]);

        // Visiteur (rôle par défaut, aucune permission spéciale)
        Role::firstOrCreate(['name' => 'visiteur', 'guard_name' => 'web']);

        // ===== Compte admin de démonstration (sans statut propriétaire) =====
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@3ao.org'],
            [
                'name'              => 'Administrateur 3AO',
                'password'          => bcrypt('Admin3AO@2026!'),
                'email_verified_at' => now(),
                'approval_status'   => 'approved',
                'approved_at'       => now(),
                'organization'      => 'Secrétariat 3AO',
                'country'           => 'Burkina Faso',
                'is_platform_owner' => false,
            ]
        );
        $adminUser->forceFill([
            'approval_status'   => 'approved',
            'email_verified_at' => $adminUser->email_verified_at ?? now(),
            'approved_at'       => $adminUser->approved_at ?? now(),
            'is_platform_owner' => false,
        ])->save();
        $adminUser->assignRole('super_admin');

        // ===== Propriétaire de la plateforme (config/platform.php) =====
        User::query()->where('is_platform_owner', true)->update(['is_platform_owner' => false]);
        $ownerEmail = config('platform.owner_email');
        $owner = User::where('email', $ownerEmail)->first();
        if ($owner) {
            $owner->forceFill([
                'is_platform_owner' => true,
                'approval_status'   => 'approved',
                'approved_at'       => $owner->approved_at ?? now(),
                'email_verified_at' => $owner->email_verified_at ?? now(),
            ])->save();
            if (! $owner->hasRole('super_admin')) {
                $owner->assignRole('super_admin');
            }
            $this->command->info("✅ Propriétaire plateforme : {$ownerEmail}");
        } else {
            $this->command->warn("⚠ Aucun compte {$ownerEmail} trouvé. Créez-le puis relancez le seeder ou migrate.");
        }

        $this->command->info('✅ Rôles, permissions et compte admin créés !');
        $this->command->info('   Demo admin : admin@3ao.org | Mot de passe: Admin3AO@2026!');
    }
}
