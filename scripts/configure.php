<?php
require __DIR__ . '/bootstrap.php';
$domain = getenv('PS_DOMAIN');
if (!$domain || !preg_match('/^[a-zA-Z0-9.-]+(?::[0-9]+)?$/', $domain)) { throw new RuntimeException('Invalid PS_DOMAIN'); }
$url = new ShopUrl(1);
$url->domain = $domain;
$url->domain_ssl = $domain;
$url->physical_uri = '/';
brisaCheck($url->update(), 'Could not set shop domain');
Configuration::updateValue('PS_SHOP_DOMAIN', $domain);
Configuration::updateValue('PS_SHOP_DOMAIN_SSL', $domain);
Configuration::updateValue('PS_SSL_ENABLED', getenv('PS_ENABLE_SSL') !== '0');
Configuration::updateValue('PS_SSL_ENABLED_EVERYWHERE', getenv('PS_ENABLE_SSL') !== '0');
Configuration::updateValue('PS_MAIL_METHOD', 3); // Demo: do not deliver emails.
Configuration::updateValue('PS_SHOP_ENABLE', 1);
Configuration::updateValue('PS_REWRITING_SETTINGS', 1);
Configuration::updateValue('PS_COOKIE_CHECKIP', 0); // Railway reverse proxy.
// A fictitious checkout has no need to collect fiscal identification numbers.
$spain = new Country((int) Country::getByIso('ES'));
$spain->need_identification_number = false;
brisaCheck($spain->update(), 'Could not configure demo address fields');
Tools::generateHtaccess();
echo "Domain and HTTPS configured; external mail disabled.\n";
