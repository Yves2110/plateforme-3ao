@extends('admin.layouts.admin')

@section('title', 'Inscriptions en attente')
@section('page-title', 'Inscriptions en attente')
@section('page-subtitle', 'Validation des nouveaux utilisateurs')

@push('styles')
<style>
    .pending-user-line {
        display: grid;
        grid-template-columns: minmax(7rem, auto) minmax(8rem, 1fr) minmax(9rem, 1fr) auto;
        column-gap: 1.25rem;
        row-gap: 0.35rem;
        align-items: center;
        flex: 1;
        min-width: 0;
    }
    .pending-user-line-head {
        display: grid;
        grid-template-columns: minmax(7rem, auto) minmax(8rem, 1fr) minmax(9rem, 1fr) auto;
        column-gap: 1.25rem;
        align-items: center;
    }
    @media (max-width: 1023px) {
        .pending-user-line,
        .pending-user-line-head {
            grid-template-columns: 1fr;
            row-gap: 0.4rem;
        }
        .pending-user-line-head {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-gray-500">Validation des demandes d'accès à la plateforme.</p>
        <a href="{{ route('admin.utilisateurs.index') }}"
           class="px-4 py-2 text-sm font-medium text-[#2D6A4F] bg-[#E8F5E9] rounded-xl hover:bg-[#B7E4C7] transition-colors">
            Tous les utilisateurs
        </a>
    </div>
    @if($pendingUsers->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-sm text-gray-800">{{ $pendingUsers->total() }} inscription(s) en attente de validation</h3>
            </div>
            
            <div class="px-4 py-2.5 border-b border-gray-100 bg-gray-50/80 hidden lg:block">
                <div class="pending-user-line-head text-[11px] font-semibold uppercase tracking-wide text-gray-400 pl-[3.25rem]">
                    <span>Nom</span>
                    <span>Organisation</span>
                    <span>E-mail</span>
                    <span>Date</span>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($pendingUsers as $user)
                <div class="px-4 py-3.5 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-4 min-w-0 flex-1">
                        <div class="w-9 h-9 shrink-0 rounded-full bg-[#2D6A4F] text-white flex items-center justify-center font-semibold text-sm mt-0.5">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="pending-user-line text-sm">
                                <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                                <span class="{{ $user->organization ? 'font-medium text-[#2D6A4F]' : 'text-gray-400 italic' }} truncate" title="{{ $user->organization }}">
                                    {{ $user->organization ?: 'Non renseignée' }}
                                </span>
                                <span class="text-gray-500 truncate">{{ $user->email }}</span>
                                <span class="text-xs text-gray-400 whitespace-nowrap">Inscrit le {{ $user->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            @if($user->registration_reason)
                                <p class="mt-1.5 text-xs text-gray-500 line-clamp-2">
                                    <span class="font-medium text-gray-600">Motif :</span> {{ $user->registration_reason }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 shrink-0 sm:pl-2">
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-[#2D6A4F] text-white text-xs sm:text-sm rounded-lg hover:bg-[#40916C] transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approuver
                            </button>
                        </form>
                        
                        <button onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.remove('hidden')" 
                                class="px-3 py-1.5 bg-red-100 text-red-700 text-xs sm:text-sm rounded-lg hover:bg-red-200 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Refuser
                        </button>
                    </div>
                </div>
                
                {{-- Modal de refus --}}
                <div id="reject-modal-{{ $user->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6">
                        <h3 class="font-semibold text-lg mb-4">Refuser l'inscription</h3>
                        <p class="text-gray-600 mb-4">Vous êtes sur le point de refuser l'inscription de <strong>{{ $user->name }}</strong>.</p>
                        
                        <form method="POST" action="{{ route('admin.users.reject', $user) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du refus (obligatoire)</label>
                                <textarea name="reason" required rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Expliquez pourquoi cette inscription est refusée..."></textarea>
                            </div>
                            
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.add('hidden')" 
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    Annuler
                                </button>
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Confirmer le refus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($pendingUsers->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $pendingUsers->links() }}
            </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <div class="text-6xl mb-4">✅</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucune inscription en attente</h3>
            <p class="text-gray-500">Toutes les inscriptions ont été traitées.</p>
        </div>
    @endif
</div>
@endsection
