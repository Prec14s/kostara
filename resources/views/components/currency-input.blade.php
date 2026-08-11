@props(['name', 'value' => null, 'placeholder' => null, 'required' => false])
<div class="relative">
    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink/40 text-sm font-medium pointer-events-none">Rp</span>
    <input
        type="text"
        inputmode="numeric"
        name="{{ $name }}"
        value="{{ ($value !== null && $value !== '') ? number_format((float) $value, 0, '', '.') : '' }}"
        placeholder="{{ $placeholder }}"
        oninput="formatRupiah(this)"
        class="rupiah-input !pl-9"
        {{ $required ? 'required' : '' }}
    >
</div>
