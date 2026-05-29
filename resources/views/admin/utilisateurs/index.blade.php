@extends('admin.layouts.admin')

@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')
@section('page-subtitle', $users->total() . ' comptes enregistrés')

@section('content')
<div class="py-6">
  <form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, e-mail ou organisation…"
           class="flex-1 min-w-[200px] max-w-md px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#52B788]">
    <button class="px-4 py-2 bg-[#2D6A4F] text-white text-sm font-medium rounded-xl hover:bg-[#40916C] transition-colors">Rechercher</button>
    <a href="{{ route('admin.users.pending') }}"
       class="px-4 py-2 bg-amber-50 text-amber-800 text-sm font-medium rounded-xl border border-amber-200 hover:bg-amber-100 transition-colors">
      Inscriptions en attente
    </a>
  </form>

  <div class="bg-white rounded-2xl border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm min-w-[720px]">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-3 py-2.5 font-semibold text-gray-600">Nom</th>
          <th class="text-left px-3 py-2.5 font-semibold text-gray-600">Organisation</th>
          <th class="text-left px-3 py-2.5 font-semibold text-gray-600">E-mail</th>
          <th class="text-left px-3 py-2.5 font-semibold text-gray-600">Rôle</th>
          <th class="text-left px-3 py-2.5 font-semibold text-gray-600">Statut</th>
          <th class="text-left px-3 py-2.5 font-semibold text-gray-600">Inscription</th>
          <th class="px-3 py-2.5"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($users as $user)
          <tr class="hover:bg-gray-50 transition-colors {{ ! ($user->is_active ?? true) ? 'opacity-60' : '' }}">
            <td class="px-3 py-2.5">
              <div class="flex items-center gap-2 min-w-0">
                <div class="w-7 h-7 rounded-full bg-[#2D6A4F] flex items-center justify-center text-white text-xs font-bold shrink-0">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <span class="font-medium text-gray-900 truncate max-w-[10rem]">{{ $user->name }}</span>
              </div>
            </td>
            <td class="px-3 py-2.5 max-w-[11rem]">
              <span class="font-medium text-[#2D6A4F] truncate block" title="{{ $user->organization }}">
                {{ $user->organization ?: '—' }}
              </span>
            </td>
            <td class="px-3 py-2.5 text-gray-600 truncate max-w-[12rem]" title="{{ $user->email }}">{{ $user->email }}</td>
            <td class="px-3 py-2.5">
              @forelse($user->roles as $role)
                <span class="px-2 py-0.5 text-xs font-semibold bg-[#E8F5E9] text-[#2D6A4F] rounded-full">{{ $role->name }}</span>
              @empty
                <span class="text-xs text-gray-400">—</span>
              @endforelse
            </td>
            <td class="px-3 py-2.5">
              @include('admin.utilisateurs._approval-status', ['user' => $user])
            </td>
            <td class="px-3 py-2.5 text-gray-400 text-xs whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
            <td class="px-3 py-2.5">
              @php $actor = auth()->user(); @endphp
              @include('admin._partials.table-actions', [
                'editRoute'   => $user->canAdminEdit($actor) ? route('admin.utilisateurs.edit', $user) : null,
                'deleteRoute' => $user->canAdminDelete($actor) ? route('admin.utilisateurs.destroy', $user) : null,
                'deleteName'  => $user->name,
                'toggleRoute' => $user->canAdminToggle($actor) ? route('admin.utilisateurs.toggle-active', $user) : null,
                'isActive'    => $user->is_active,
              ])
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">Aucun utilisateur trouvé.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
    <div class="mt-4 pt-3 border-t border-gray-100">{{ $users->links() }}</div>
  @endif
</div>
@endsection
