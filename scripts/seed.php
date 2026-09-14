<?php
require __DIR__ . '/bootstrap.php';
$catalog = json_decode(file_get_contents('/opt/brisa/catalog/products.json'), true, 512, JSON_THROW_ON_ERROR);
$langId = (int) $context->language->id;
$db = Db::getInstance();
$categoryIds = [];
foreach ($catalog['categories'] as $entry) {
    $id = (int) $db->getValue("SELECT c.id_category FROM " . _DB_PREFIX_ . "category c INNER JOIN " . _DB_PREFIX_ . "category_lang l ON l.id_category=c.id_category WHERE c.id_parent=2 AND l.link_rewrite='" . pSQL($entry['slug']) . "'");
    if (!$id) {
        $category = new Category();
        $category->id_parent = 2; $category->active = true;
        $category->name = brisaLang($entry['name']); $category->link_rewrite = brisaLang($entry['slug']);
        $category->description = brisaLang('<p>' . $entry['description'] . '</p>');
        $category->meta_title = brisaLang($entry['name'] . ' · Brisa');
        brisaCheck($category->add(), 'Cannot create category');
        $category->addGroups([1, 2, 3]);
        $id = (int) $category->id;
    }
    $categoryIds[$entry['slug']] = $id;
}
Configuration::updateValue('BRISA_CATEGORIES', json_encode(array_values($categoryIds)));
$home = new Category(2); $home->name = brisaLang('Todos los productos');
$home->description = brisaLang('<p>Los esenciales de Brisa. Encuentra un pequeño cuidado para cada rincón de tu hogar.</p>');
$home->meta_title = brisaLang('Productos de limpieza · Brisa'); $home->update();

$taxGroupId = (int) Configuration::get('BRISA_TAX_GROUP');
if (!$taxGroupId) {
    $tax = new Tax(); $tax->name = brisaLang('IVA demo 21%'); $tax->rate = 21; $tax->active = true;
    brisaCheck($tax->add(), 'Cannot create demo tax');
    $group = new TaxRulesGroup(); $group->name = 'Brisa · IVA demo España 21%'; $group->active = true;
    brisaCheck($group->add(), 'Cannot create tax group');
    $rule = new TaxRule(); $rule->id_tax_rules_group = $group->id; $rule->id_country = (int) Country::getByIso('ES');
    $rule->id_state = 0; $rule->zipcode_from = 0; $rule->zipcode_to = 0; $rule->id_tax = $tax->id; $rule->behavior = 0;
    brisaCheck($rule->add(), 'Cannot create tax rule');
    $taxGroupId = (int) $group->id;
    Configuration::updateValue('BRISA_TAX_GROUP', $taxGroupId);
}
foreach ($catalog['products'] as $entry) {
    $id = (int) $db->getValue("SELECT id_product FROM " . _DB_PREFIX_ . "product WHERE reference='" . pSQL($entry['sku']) . "'");
    if (!$id) {
        $product = new Product(); $product->reference = $entry['sku'];
        $product->name = brisaLang($entry['name']); $product->link_rewrite = brisaLang($entry['slug']);
        $product->description_short = brisaLang('<p>' . $entry['summary'] . '</p>');
        $product->description = brisaLang($entry['description']);
        $product->meta_title = brisaLang($entry['name'] . ' · Brisa');
        $product->meta_description = brisaLang($entry['summary'] . '. Producto ficticio de la tienda de demostración Brisa.');
        $product->price = round($entry['price'] / 1.21, 6);
        $product->id_tax_rules_group = $taxGroupId; $product->id_category_default = $categoryIds[$entry['category']];
        $product->weight = $entry['weight']; $product->active = true; $product->available_for_order = true;
        $product->show_price = true; $product->minimal_quantity = 1; $product->visibility = 'both';
        $product->condition = 'new'; $product->is_virtual = false; $product->indexed = 0;
        brisaCheck($product->add(), 'Cannot create product ' . $entry['sku']);
        $product->updateCategories([2, $categoryIds[$entry['category']]]);
        StockAvailable::setQuantity((int) $product->id, 0, $entry['stock'], 1);
        StockAvailable::setProductOutOfStock((int) $product->id, 0, 1);
        $id = (int) $product->id;
    }
    $cover = Image::getCover($id);
    if (!$cover) {
        $image = new Image(); $image->id_product = $id; $image->position = 1; $image->cover = true;
        $image->legend = brisaLang($entry['name'] . ' · Envase ficticio Brisa');
        brisaCheck($image->add(), 'Cannot create product image');
    } else { $image = new Image((int) $cover['id_image']); }
    $imagePath = $image->getPathForCreation();
    $source = '/opt/brisa/catalog/images/' . $entry['slug'] . '.jpg';
    brisaCheck(copy($source, $imagePath . '.jpg'), 'Cannot copy product image');
    foreach (ImageType::getImagesTypes('products') as $type) {
        brisaCheck(ImageManager::resize($source, $imagePath . '-' . $type['name'] . '.jpg', (int) $type['width'], (int) $type['height'], 'jpg'), 'Cannot generate thumbnail');
    }
    echo 'Seeded: ' . $entry['sku'] . "\n";
}

if (!Configuration::get('BRISA_CARRIER')) {
    $carrier = new Carrier(); $carrier->name = 'Brisa · Envío de prueba';
    $carrier->active = true; $carrier->deleted = false; $carrier->is_free = false;
    $carrier->shipping_handling = false; $carrier->range_behavior = false;
    $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE; $carrier->is_module = false;
    $carrier->need_range = true; $carrier->delay = brisaLang('Entrega simulada en 2–3 días · No se enviará mercancía');
    brisaCheck($carrier->add(), 'Cannot create demo carrier');
    $carrier->setGroups([1, 2, 3]);
    $zone = (int) (new Country((int) Country::getByIso('ES')))->id_zone;
    $carrier->addZone($zone);
    $range = new RangePrice(); $range->id_carrier = $carrier->id; $range->delimiter1 = 0; $range->delimiter2 = 100000;
    brisaCheck($range->add(), 'Cannot create carrier range');
    brisaCheck($carrier->addDeliveryPrice([['id_range_price' => $range->id, 'id_carrier' => $carrier->id, 'id_zone' => $zone, 'price' => 3.90]]), 'Cannot configure delivery');
    Configuration::updateValue('PS_CARRIER_DEFAULT', $carrier->id);
    Configuration::updateValue('BRISA_CARRIER', $carrier->id);
}

$cmsDefinitions = [
    'sobre-la-demo' => ['Sobre esta demo', '<h2>Una Brisa para tu hogar</h2><p>Brisa es una tienda ficticia de productos de limpieza creada para probar PrestaShop. Las imágenes, los nombres y los precios forman parte del proyecto. No somos un comercio abierto a la venta.</p><p>Puedes navegar, buscar productos, añadirlos al carrito y completar un pedido sin cobro. La gestión de productos y pedidos utiliza el panel real de PrestaShop.</p>'],
    'envios-demo' => ['Envíos y devoluciones', '<h2>Todo es de prueba</h2><p>No se envía mercancía. Para comprobar el cálculo del carrito, el transporte simulado cuesta 3,90 € y es gratuito desde 35 € de productos. La demo se limita a España.</p><p>No hay pagos ni devoluciones monetarias. El plazo mostrado sirve únicamente para probar el proceso de compra.</p>'],
    'privacidad-demo' => ['Privacidad de la demo', '<h2>Utiliza datos ficticios</h2><p>Esta tienda de pruebas guarda los datos introducidos en cuentas, direcciones, carritos y pedidos. Utiliza datos inventados y un correo de ejemplo. No introduzcas contraseñas reutilizadas ni información bancaria.</p><p>La demo utiliza las cookies de sesión necesarias para el carrito y el acceso. No incorpora analítica publicitaria. El envío de correo está desactivado. Los administradores del proyecto pueden consultar y borrar los datos de prueba.</p>'],
    'condiciones-demo' => ['Condiciones de la prueba', '<h2>Pedido de demostración</h2><p>Al completar el proceso confirmas que estás realizando una prueba: los productos son ficticios, no se cobra ningún importe y no se prepara ni envía mercancía.</p><p>Los importes e impuestos son datos simulados para verificar el funcionamiento técnico del ecommerce. Esta demostración no constituye una oferta comercial.</p>'],
];
$cmsIds = [];
foreach ($cmsDefinitions as $slug => [$title, $content]) {
    $id = (int) $db->getValue("SELECT id_cms FROM " . _DB_PREFIX_ . "cms_lang WHERE link_rewrite='" . pSQL($slug) . "'");
    if (!$id) {
        $cms = new CMS(); $cms->id_cms_category = 1; $cms->active = true; $cms->indexation = false;
        $cms->meta_title = brisaLang($title); $cms->link_rewrite = brisaLang($slug); $cms->content = brisaLang($content);
        brisaCheck($cms->add(), 'Cannot create demo information page'); $id = (int) $cms->id;
    }
    $cmsIds[] = $id;
    if ($slug === 'condiciones-demo') Configuration::updateValue('PS_CONDITIONS_CMS_ID', $id);
}
Configuration::updateValue('BRISA_CMS', json_encode($cmsIds));
foreach (['brisastore', 'brisademo'] as $moduleName) {
    if (!Module::isInstalled($moduleName)) brisaCheck(Module::getInstanceByName($moduleName)->install(), 'Cannot install ' . $moduleName);
}
// Register hooks idempotently so an interrupted initial module install can resume.
foreach (['brisastore' => ['displayHeader', 'displayHome'], 'brisademo' => ['paymentOptions', 'paymentReturn']] as $name => $hooks) {
    foreach ($hooks as $hook) brisaCheck(Module::getInstanceByName($name)->registerHook($hook), 'Cannot register ' . $hook);
}
Configuration::updateValue('PS_SHIPPING_FREE_PRICE', 35);
Configuration::updateValue('PS_SHIPPING_HANDLING', 0);
Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);
Configuration::updateValue('PS_CONDITIONS', 1);
Configuration::updateValue('PS_ORDER_RETURN', 0);
Configuration::updateValue('PS_STOCK_MANAGEMENT', 1);
Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', 0);
Configuration::updateValue('PS_INVOICE', 0);
Configuration::updateValue('PS_SHOP_NAME', 'Brisa');
Configuration::updateValue('PS_SHOP_EMAIL', getenv('ADMIN_MAIL'));
Configuration::updateValue('PS_SHOP_PHONE', '');
Configuration::updateValue('PS_SHOP_ADDR1', 'Tienda de demostración');
Configuration::updateValue('PS_CURRENCY_DEFAULT', (int) Currency::getIdByIsoCode('EUR'));
foreach (Country::getCountries($langId, true) as $entry) {
    $country = new Country((int) $entry['id_country']);
    $country->active = $country->iso_code === 'ES';
    if ($country->active) $country->contains_states = false;
    $country->update();
}
foreach (Carrier::getCarriers($langId, true, false, false, null, Carrier::ALL_CARRIERS) as $entry) {
    if ((int) $entry['id_carrier'] !== (int) Configuration::get('BRISA_CARRIER')) {
        $carrier = new Carrier((int) $entry['id_carrier']); $carrier->active = false; $carrier->update();
    }
}
Search::indexation(true);
echo "Catalog, demo checkout and shipping configured.\n";
