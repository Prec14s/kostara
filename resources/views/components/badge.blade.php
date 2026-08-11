@props(['color' => 'gray'])
@php
$colors = [
    'gray' => 'bg-ink/5 text-ink/60',
    'green' => 'bg-sage-50 text-sage',
    'amber' => 'bg-brass-50 text-brass-dark',
    'red' => 'bg-clay-50 text-clay',
    'blue' => 'bg-teal-50 text-teal',
];
@endphp
<span class="chip {{ $colors[$color] ?? $colors['gray'] }}">{{ $slot }}</span>
