<?php
require __DIR__ . '/bootstrap.php';
// The parent theme may enable bundled modules. Keep only the demo payment method.
foreach (['ps_checkout', 'ps_wirepayment', 'ps_checkpayment', 'ps_cashondelivery', 'ps_googleanalytics', 'ps_facebook', 'ps_eventbus', 'ps_accounts', 'psxmarketingwithgoogle', 'ps_emailsubscription', 'blockwishlist'] as $name) {
    if (Module::isInstalled($name)) Module::getInstanceByName($name)->disable();
}
Configuration::updateValue('PS_MAIL_METHOD', 3);
Configuration::updateValue('PS_SMARTY_CACHE', 1);
Configuration::updateValue('PS_SMARTY_FORCE_COMPILE', 0);
Configuration::updateValue('PS_CSS_THEME_CACHE', 0);
Configuration::updateValue('PS_JS_THEME_CACHE', 0);
Tools::generateHtaccess();
echo "Brisa theme ready. No real payment gateways or outbound email.\n";
