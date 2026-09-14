<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { exit(1); }
$_SERVER['HTTP_HOST'] = getenv('PS_DOMAIN') ?: 'localhost:8080';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
if (getenv('PS_ENABLE_SSL') !== '0') { $_SERVER['HTTPS'] = 'on'; $_SERVER['SERVER_PORT'] = '443'; }
require '/var/www/html/config/config.inc.php';
$context = Context::getContext();
$context->shop = new Shop(1);
Shop::setContext(Shop::CONTEXT_SHOP, 1);
$context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
$context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
$context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
$context->employee = new Employee(1);
function brisaCheck($result, string $message): void { if ($result === false) { throw new RuntimeException($message); } }
function brisaLang(string $value): array {
    $result = [];
    foreach (Language::getLanguages(false) as $lang) { $result[(int) $lang['id_lang']] = $value; }
    return $result;
}
