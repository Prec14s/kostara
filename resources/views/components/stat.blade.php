@props(['label', 'value', 'accent' => 'teal'])
@php
    $bar = ['teal' => 'bg-teal', 'brass' => 'bg-brass', 'sage' => 'bg-sage', 'clay' => 'bg-clay'][$accent] ?? 'bg-teal';
@endphp
<div class="surface p-4 relative overflow-hidden group hover:-translate-y-0.5 hover:shadow-lift transition-all duration-200">
    <span class="absolute left-0 top-0 h-full w-1 {{ $bar }} opacity-70 group-hover:opacity-100 transition"></span>
    <p class="text-xs font-semibold text-ink/45 uppercase tracking-wide pl-2">{{ $label }}</p>
    <p class="text-2xl font-display font-semibold text-ink mt-1 pl-2">{{ $value }}</p>
</div>
