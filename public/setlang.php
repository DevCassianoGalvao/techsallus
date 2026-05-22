<?php
$supported = ['pt', 'en', 'es'];
$lang = $_GET['lang'] ?? 'pt';
if (!in_array($lang, $supported, true)) $lang = 'pt';

setcookie('lang', $lang, [
    'expires'  => time() + 60 * 60 * 24 * 365,
    'path'     => '/',
    'samesite' => 'Lax',
    'secure'   => false,
    'httponly' => false,
]);

$back = $_SERVER['HTTP_REFERER'] ?? '/';
header('Location: ' . $back);
exit;
