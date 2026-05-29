@extends('admin.layouts.admin')

@section('title', 'Éditer ' . $user->name)
@section('page-title', 'Éditer l\'utilisateur')
@section('page-subtitle', $user->email)

@section('content')
<div class="py-6 max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
            </div>
        @endif

        <div class="mb-5 p-4 rounded-xl border border-gray-100 bg-gray-50/80 space-y-2 text-sm">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-gray-600 font-medium">Statut du compte :</span>
                @include('admin.utilisateurs._approval-status', ['user' => $user])
            </div>
            @if($user->approved_at)
                <p class="text-gray-500 text-xs">Approuvé le {{ $user->approved_at->format('d/m/Y à H:i') }}</p>
            @endif
            @if($user->isPlatformOwner())
                <p class="text-xs text-[#2D6A4F] font-medium">Compte propriétaire de la plateforme (protégé)</p>
            @endif
            <p class="text-xs text-gray-500">
                Compte {{ ($user->is_active ?? true) ? 'actif' : 'désactivé' }}
            </p>
            @if($user->approval_status === 'rejected' && $user->rejection_reason)
                <p class="text-xs text-red-700"><span class="font-medium">Motif du refus :</span> {{ $user->rejection_reason }}</p>
            @endif
            <p class="text-xs text-gray-500">Inscrit le {{ $user->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <form action="{{ route('admin.utilisateurs.update', $user) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Rôle</label>
                    <select name="role" required {{ ($user->isPlatformOwner() || ($user->hasRole('super_admin') && !auth()->user()->isPlatformOwner())) ? 'disabled' : '' }}
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white disabled:bg-gray-100 disabled:text-gray-500">
                        @foreach($roles as $role)
                            @if($role->name === 'super_admin' && !auth()->user()->isPlatformOwner())
                                @continue
                            @endif
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @if($user->isPlatformOwner() || ($user->hasRole('super_admin') && !auth()->user()->isPlatformOwner()))
                        <input type="hidden" name="role" value="super_admin">
                        <p class="mt-1 text-xs text-gray-500">Le rôle super_admin ne peut être modifié que par le propriétaire de la plateforme.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Organisation</label>
                    <input type="text" name="organization" value="{{ old('organization', $user->organization) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pays</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
                </div>
            </div>

            @if($user->registration_reason)
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Motif de la demande (lecture seule)</label>
                    <textarea readonly rows="3"
                              class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 text-gray-600 resize-none">{{ $user->registration_reason }}</textarea>
                </div>
            @endif

            {{-- Matrice de permissions granulaires --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Permissions directes (surcharge du rôle)</h3>
                </div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($permissions as $permission)
                        @php $hasPermission = $user->hasDirectPermission($permission->name); @endphp
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                   {{ $hasPermission ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#2D6A4F] rounded border-gray-300 focus:ring-[#52B788]">
                            <span class="text-sm text-gray-700 group-hover:text-[#2D6A4F] transition-colors">
                                {{ $permission->name }}
                                @if($user->hasPermissionTo($permission->name) && !$hasPermission)
                                    <span class="text-xs text-gray-400">(via rôle)</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="px-5 py-2 bg-[#2D6A4F] text-white text-sm font-semibold rounded-xl hover:bg-[#40916C] transition-colors">
                    Enregistrer
                </button>
                <a href="{{ route('admin.utilisateurs.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
