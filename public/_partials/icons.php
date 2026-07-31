<?php
/* Server-side mirror of assets/site.js ICONS map — used to render icon-badge SVGs
   without depending on client-side JS (keeps content in PHP, translatable via tx()). */
$GLOBALS['ICONS'] = [
    'calendarX'     => '<path d="M3 8h18M7 3v4M17 3v4M4 6h16v14H4z"/><path d="M9 11l6 6M15 11l-6 6"/>',
    'clock'         => '<circle cx="12" cy="13" r="8.5"/><path d="M12 9v4l3 2M9 3h6"/>',
    'receiptAlert'  => '<path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M8 9h8M8 13h5"/>',
    'layers'        => '<path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5M3 18l9 5 9-5"/>',
    'box'           => '<path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 8v9l9 5 9-5V8M12 13v9"/>',
    'chartDown'     => '<path d="M4 4v16h16"/><path d="M7 10l4 4 3-3 5 5"/>',
    'calendarCheck' => '<path d="M4 6h16v14H4z"/><path d="M8 3v4M16 3v4M4 10h16"/><path d="M9 15l2 2 4-4"/>',
    'zap'           => '<path d="M13 2L4 14h6l-1 8 9-12h-6z"/>',
    'heartPulse'    => '<path d="M4 12h4l2-5 3 10 2-5h5"/>',
    'receiptCheck'  => '<path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M9 12l2 2 4-4"/>',
    'boxCheck'      => '<path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 8v9l9 5 9-5V8"/><path d="M9.5 13l2 2 4-3.5"/>',
    'chartUp'       => '<path d="M4 4v16h16"/><path d="M7 15l4-4 3 3 5-6"/>',
    'arrowRight'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'users'         => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.6"/><path d="M15.5 14.2c2.3.4 4 2.3 4.5 4.8"/>',
    'building'      => '<path d="M4 21V5l8-2 8 2v16"/><path d="M4 21h16M9 9h2M13 9h2M9 13h2M13 13h2M9 17h2M13 17h2"/>',
    'target'        => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r=".8" fill="var(--navy)"/>',
    'route'         => '<circle cx="6" cy="19" r="2.4"/><circle cx="18" cy="5" r="2.4"/><path d="M8.2 19H14a4 4 0 004-4V9a4 4 0 00-4-4H9.8"/>',
    'sliders'       => '<path d="M4 6h9M17 6h3M4 12h3M9 12h11M4 18h13M20 18h0"/><circle cx="15" cy="6" r="2"/><circle cx="7" cy="12" r="2"/><circle cx="17" cy="18" r="2"/>',
    'shield'        => '<path d="M12 3l8 3v6c0 5-3.4 8.4-8 9-4.6-.6-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
    'fileText'      => '<path d="M6 3h9l5 5v13H6z"/><path d="M15 3v5h5M9 12h6M9 16h6M9 8h2"/>',
    'image'         => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-9 9"/>',
    'plug'          => '<path d="M9 2v6M15 2v6M6 8h12v4a6 6 0 01-6 6 6 6 0 01-6-6z"/><path d="M12 18v4"/>',
    'monitor'       => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>',
    'database'      => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
    'cloud'         => '<path d="M7 18a4.5 4.5 0 01-.7-8.94A5.5 5.5 0 0117 8a4 4 0 01-.5 8H7z"/>',
    'server'        => '<rect x="3" y="4" width="18" height="6" rx="1.5"/><rect x="3" y="14" width="18" height="6" rx="1.5"/><path d="M7 7h.01M7 17h.01"/>',
    'mail'          => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
    'phone'         => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2.2 2A17 17 0 013 6.2 2 2 0 015 4z"/>',
    'checkCircle'   => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/>',
    'search'        => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
    'plusMinus'     => '<path d="M5 12h14M12 5v14"/>',
];

function ic(string $name, string $cls = ''): string
{
    $path = $GLOBALS['ICONS'][$name] ?? '';
    return '<svg class="icon ' . htmlspecialchars($cls, ENT_QUOTES, 'UTF-8') . '" viewBox="0 0 24 24">' . $path . '</svg>';
}
