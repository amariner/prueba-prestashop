<?php
if (!defined('_PS_VERSION_')) { exit; }
class Brisastore extends Module
{
    public function __construct()
    {
        $this->name = 'brisastore'; $this->tab = 'front_office_features'; $this->version = '1.0.0';
        $this->author = 'Brisa Studio'; $this->bootstrap = true;
        parent::__construct();
        $this->displayName = 'Brisa · Escaparate';
        $this->description = 'Portada y navegación conectadas al catálogo real de PrestaShop.';
        $this->ps_versions_compliancy = ['min' => '9.1.0', 'max' => _PS_VERSION_];
    }
    public function install() { return parent::install() && $this->registerHook('displayHeader') && $this->registerHook('displayHome'); }
    private function assignStore(): void
    {
        $categories = [];
        foreach (json_decode(Configuration::get('BRISA_CATEGORIES') ?: '[]', true) as $id) {
            $category = new Category((int) $id, $this->context->language->id);
            if (!Validate::isLoadedObject($category) || !$category->active) continue;
            $categories[] = ['id' => $id, 'name' => $category->name, 'url' => $this->context->link->getCategoryLink($category)];
        }
        $cmsPages = [];
        foreach (json_decode(Configuration::get('BRISA_CMS') ?: '[]', true) as $id) {
            $cms = new CMS((int) $id, $this->context->language->id);
            if (Validate::isLoadedObject($cms)) $cmsPages[] = ['title' => $cms->meta_title, 'url' => $this->context->link->getCMSLink($cms)];
        }
        $this->context->smarty->assign(['brisa_categories' => $categories, 'brisa_cms' => $cmsPages]);
    }
    public function hookDisplayHeader($params) { $this->assignStore(); return ''; }
    public function hookDisplayHome($params)
    {
        $this->assignStore();
        $products = [];
        $rows = (new Category(2, $this->context->language->id))->getProducts($this->context->language->id, 1, 4, 'position', 'ASC');
        foreach ($rows ?: [] as $row) {
            $product = new Product((int) $row['id_product'], false, $this->context->language->id);
            $cover = Product::getCover($product->id);
            $products[] = [
                'name' => $product->name,
                'description' => strip_tags($product->description_short),
                'url' => $this->context->link->getProductLink($product),
                'image' => $cover ? $this->context->link->getImageLink($product->link_rewrite, $cover['id_image'], 'large_default') : '',
                'price' => $this->context->getCurrentLocale()->formatPrice(Product::getPriceStatic($product->id, true), $this->context->currency->iso_code),
            ];
        }
        $this->context->smarty->assign(['brisa_products' => $products, 'brisa_catalog_url' => $this->context->link->getCategoryLink(2)]);
        return $this->fetch('module:brisastore/views/templates/hook/home.tpl');
    }
}
