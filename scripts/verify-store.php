<?php
require __DIR__ . '/bootstrap.php';
$count = (int) Db::getInstance()->getValue("SELECT COUNT(*) FROM " . _DB_PREFIX_ . "product WHERE reference LIKE 'BRISA-%'");
if ($count !== 12) throw new RuntimeException('Expected 12 Brisa products; got ' . $count);
if ((new Shop(1))->theme_name !== 'brisa') throw new RuntimeException('Theme is not Brisa');
if (!Module::isEnabled('brisademo')) throw new RuntimeException('Demo payment is not enabled');
if ((int) Configuration::get('PS_MAIL_METHOD') !== 3) throw new RuntimeException('Outgoing mail must be disabled');
$paymentModules = PaymentModule::getInstalledPaymentModules();
foreach ($paymentModules as $module) {
    if ($module['name'] !== 'brisademo' && Module::isEnabled($module['name'])) throw new RuntimeException('Unexpected payment module: ' . $module['name']);
}
echo "PASS: 12 demo products, Brisa theme, demo payment only, email disabled.\n";
