<?php declare(strict_types=1);

namespace App\Support;

/**
 * Visual identity tokens + the wordmark SVG.
 * Landing, admin, and invoice views all pull from here.
 */
final class Brand
{
    public const DARK = [
        'bg'        => '#0a0e1a',
        'bg_2'      => '#0f1424',
        'surface'   => '#131a2b',
        'surface_2' => '#1a2238',
        'border'    => '#1f2a44',
        'border_2'  => '#2a3656',
        'text'      => '#e2e8f0',
        'text_dim'  => '#cbd5e1',
        'muted'     => '#94a3b8',
        'accent'    => '#38bdf8',
        'accent_2'  => '#818cf8',
        'accent_3' => '#4ade80',
        'danger'    => '#f87171',
    ];

    public static function logo(string $name, string $tone = 'light', int $height = 22): string
    {
        $textColor = $tone === 'light' ? '#f8fafc' : '#0f172a';
        $accent    = '#38bdf8';
        $accent2   = '#818cf8';
        $safe      = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $h         = $height;
        return <<<SVG
<span class="brand-mark" style="display:inline-flex;align-items:center;gap:8px;font-family:'JetBrains Mono','SF Mono','Menlo',monospace;font-weight:700;letter-spacing:-0.01em;line-height:1;color:{$textColor};">
  <svg width="{$h}" height="{$h}" viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <defs><linearGradient id="brand-g" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
      <stop offset="0%" stop-color="{$accent}"/><stop offset="100%" stop-color="{$accent2}"/>
    </linearGradient></defs>
    <path d="M4 6 L11 12 L4 18" stroke="url(#brand-g)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M13 6 L20 12 L13 18" stroke="url(#brand-g)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" opacity="0.55"/>
  </svg>
  <span>{$safe}</span>
</span>
SVG;
    }

    public static function faviconDataUri(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">'
             . '<defs><linearGradient id="g" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">'
             . '<stop offset="0%" stop-color="#38bdf8"/><stop offset="100%" stop-color="#818cf8"/>'
             . '</linearGradient></defs>'
             . '<rect width="24" height="24" rx="6" fill="#0a0e1a"/>'
             . '<path d="M5 7 L11 12 L5 17" stroke="url(#g)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'
             . '<path d="M13 7 L19 12 L13 17" stroke="url(#g)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.55"/>'
             . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
