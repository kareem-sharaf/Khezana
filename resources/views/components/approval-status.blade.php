{{-- Approval Status Badge Component --}}
@props(['status', 'size' => 'md'])

@php
    $colors = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'archived' => 'gray',
    ];

    $labels = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'archived' => 'Archived',
    ];

    $color = $colors[$status] ?? 'gray';
    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
    @if($color === 'warning') bg-yellow-100 text-yellow-800 
    @elseif($color === 'success') bg-green-100 text-green-800 
    @elseif($color === 'danger') bg-red-100 text-red-800 
    @else bg-gray-100 text-gray-800 
    @endif">
    {{ $label }}
</span>
