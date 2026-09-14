<?php
if (!defined('_PS_VERSION_')) { exit; }
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
class Brisademo extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'brisademo'; $this->tab = 'payments_gateways'; $this->version = '1.0.0';
        $this->author = 'Brisa Studio'; $this->controllers = ['validation'];
        $this->currencies = true; $this->currencies_mode = 'checkbox';
        parent::__construct();
        $this->displayName = 'Pedido de prueba · Sin cobro';
        $this->description = 'Crea un pedido de demostración, sin pasarela bancaria ni cobros.';
        $this->ps_versions_compliancy = ['min' => '9.1.0', 'max' => _PS_VERSION_];
    }
    public function install()
    {
        if (!parent::install() || !$this->registerHook('paymentOptions') || !$this->registerHook('displayPaymentReturn')) return false;
        if (!Configuration::get('BRISA_OS_DEMO')) {
            $state = new OrderState();
            foreach (Language::getLanguages(false) as $language) $state->name[(int) $language['id_lang']] = 'Demo · Sin cobro';
            $state->color = '#234E40'; $state->module_name = $this->name;
            $state->send_email = false; $state->paid = false; $state->logable = false;
            $state->invoice = false; $state->delivery = false; $state->shipped = false;
            $state->hidden = false; $state->unremovable = false; $state->pdf_invoice = false; $state->pdf_delivery = false;
            if (!$state->add()) return false;
            Configuration::updateValue('BRISA_OS_DEMO', $state->id);
        }
        return true;
    }
    public function hookPaymentOptions($params)
    {
        if (!$this->active) return [];
        $option = new PaymentOption();
        $option->setCallToActionText('Pedido de prueba — no se realizará ningún cobro')
            ->setModuleName($this->name)
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true))
            ->setInputs([
                'brisa_token' => ['name' => 'brisa_token', 'type' => 'hidden', 'value' => Tools::getToken(false)],
                'brisa_cart' => ['name' => 'brisa_cart', 'type' => 'hidden', 'value' => (string) $this->context->cart->id],
            ])
            ->setAdditionalInformation('<p>Productos ficticios. El pedido se guardará para probar la tienda. No introduzcas datos bancarios. No se enviará mercancía ni correo.</p>');
        return [$option];
    }
    public function hookDisplayPaymentReturn($params) { return '<div class="alert alert-success">Pedido de prueba registrado. No se ha realizado ningún cobro ni se enviará mercancía.</div>'; }
}
