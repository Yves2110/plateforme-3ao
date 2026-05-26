@extends('admin.layouts.admin')

@section('title', 'Inscriptions en attente')
@section('page-title', 'Inscriptions en attente')
@section('page-subtitle', 'Validation des nouveaux utilisateurs')

@section('content')
<div class="py-6">
    @if($pendingUsers->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">{{ $pendingUsers->total() }} inscription(s) en attente de validation</h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                @foreach($pendingUsers as $user)
                <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#2D6A4F] text-white flex items-center justify-center font-bold text-lg">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <p class="text-xs text-gray-400">Inscrit le {{ $user->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-[#2D6A4F] text-white text-sm rounded-lg hover:bg-[#40916C] transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approuver
                            </button>
                        </form>
                        
                        <button onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.remove('hidden')" 
                                class="px-4 py-2 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
            
            <div class="p-4 border-t border-gray-100">
                {{ $pendingUsers->links() }}
            </div>
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
