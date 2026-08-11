@props(['title' => 'Belum ada data', 'subtitle' => null])
<div class="text-center py-12 px-4">
    <div class="w-12 h-12 rounded-full bg-linen border border-line flex items-center justify-center mx-auto mb-3">
        <span class="w-2 h-2 rounded-full bg-ink/20"></span>
    </div>
    <p class="text-sm font-semibold text-ink/60">{{ $title }}</p>
    @if ($subtitle)
        <p class="text-xs text-ink/40 mt-1">{{ $subtitle }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
