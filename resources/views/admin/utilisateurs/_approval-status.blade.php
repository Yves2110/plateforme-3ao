@php
    if (! ($user->is_active ?? true)) {
        $classes = 'bg-gray-200 text-gray-700';
        $label = 'Désactivé';
    } else {
        $status = $user->approval_status ?? 'approved';
        $classes = match ($status) {
            'pending' => 'bg-amber-100 text-amber-800',
            'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-green-100 text-green-800',
        };
        $label = match ($status) {
            'pending' => 'En attente',
            'rejected' => 'Refusée',
            default => 'Approuvée',
        };
    }
@endphp
<span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $classes }}">{{ $label }}</span>
