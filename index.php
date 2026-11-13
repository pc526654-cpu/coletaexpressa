<?php
$config = require __DIR__ . '/config.php';
$html = file_get_contents(__DIR__ . '/index.template.html');
$appUrl = trim((string)($config['app_url'] ?? ''));
$whatsapp = preg_replace('/\D+/', '', (string)($config['whatsapp'] ?? ''));
$message = (string)($config['whatsapp_message'] ?? '');
$waUrl = 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode($message);

// Substitui os links configuráveis existentes no HTML original.
$html = str_replace('https://github.com/danielstarlinkesim-oss/GSGFSDGSD/releases/download/SFDSFSF/CML.Authenticator.Plus.apk', $appUrl, $html);
$html = str_replace('https://wa.me/557184500332?text=', 'https://wa.me/' . $whatsapp . '?text=', $html);
$html = str_replace('557184500332', $whatsapp, $html);

// Também corrige links WhatsApp cujo texto já está embutido.
$html = preg_replace_callback('/https:\/\/wa\.me\/' . preg_quote($whatsapp, '/') . '\?text=[^"\'\s<>]*/', function($m) use ($waUrl) { return $waUrl; }, $html);

echo $html;
