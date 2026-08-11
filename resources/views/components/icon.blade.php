@props(['name', 'class' => 'w-[18px] h-[18px]'])
@php
$icons = [
    'dashboard' => 'M3 3h7v9H3V3Zm11 0h7v5h-7V3ZM3 16h7v5H3v-5Zm11-3h7v8h-7v-8Z',
    'home' => 'M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5',
    'door' => 'M5 21V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v16M5 21h10M5 21H3m12 0h2M14 12h.01',
    'calendar' => 'M7 2v3M17 2v3M3.5 8.5h17M4 5h16a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
    'file' => 'M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm7 0v5h5',
    'wallet' => 'M3 7a2 2 0 0 1 2-2h13a1 1 0 0 1 1 1v2M3 7v11a2 2 0 0 0 2 2h14a1 1 0 0 0 1-1v-5M3 7l1.5 1M17 13h.01M13 13h6a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-6a2 2 0 0 1 0-5Z',
    'card' => 'M2 8h20M6 16h4M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z',
    'chart' => 'M3 3v18h18M8 17V10M13 17V6M18 17v-4',
    'wrench' => 'M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.8 2.8-2-2 2.8-2.8Z',
    'check' => 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11',
    'megaphone' => 'M3 11v2a2 2 0 0 0 2 2h1l4 4v-5M3 11l14-6v14l-14-6M3 11h4M17 8a3 3 0 0 1 0 6',
    'user' => 'M20 21a8 8 0 1 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
    'users' => 'M17 21v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 4a4 4 0 0 0-3-3.87M15 4.13a4 4 0 0 1 0 7.75',
    'search' => 'M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm10 2-4.35-4.35',
    'logout' => 'M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9',
    'menu' => 'M4 6h16M4 12h16M4 18h16',
    'close' => 'M18 6 6 18M6 6l12 12',
];
$path = $icons[$name] ?? $icons['dashboard'];
@endphp
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="{{ $path }}"/>
</svg>
