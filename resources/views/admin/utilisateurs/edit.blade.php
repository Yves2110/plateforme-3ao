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
                    <select name="role" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788] bg-white">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
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
