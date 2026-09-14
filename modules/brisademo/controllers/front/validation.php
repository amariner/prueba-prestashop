<?php
class BrisademoValidationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function postProcess()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !hash_equals(Tools::getToken(false), (string) Tools::getValue('brisa_token'))) {
            http_response_code(403); exit('Solicitud de prueba no válida.');
        }
        $cart = $this->context->cart;
        if (!$this->module->active || !$cart->id || (int) Tools::getValue('brisa_cart') !== (int) $cart->id || !$cart->id_customer || !$cart->id_address_delivery || !$cart->id_address_invoice) {
            Tools::redirect($this->context->link->getPageLink('order')); return;
        }
        $customer = new Customer((int) $cart->id_customer);
        if (!$this->context->customer->isLogged(true) || (int) $this->context->customer->id !== (int) $customer->id || !hash_equals($customer->secure_key, $cart->secure_key)) {
            http_response_code(403); exit('El carrito no pertenece a esta sesión.');
        }
        $lock = 'brisa_order_' . (int) $cart->id;
        if (!Db::getInstance()->getValue("SELECT GET_LOCK('" . pSQL($lock) . "', 10)")) { http_response_code(409); exit('Pedido en proceso.'); }
        try {
            $orderId = Order::getIdByCartId((int) $cart->id);
            if (!$orderId) {
                $this->module->validateOrder((int) $cart->id, (int) Configuration::get('BRISA_OS_DEMO'), (float) $cart->getOrderTotal(true, Cart::BOTH), 'Demo · Sin cobro', 'Pedido ficticio: no cobrar, no enviar.', [], (int) $cart->id_currency, false, $customer->secure_key);
                $orderId = (int) $this->module->currentOrder;
            }
        } finally { Db::getInstance()->execute("DO RELEASE_LOCK('" . pSQL($lock) . "')"); }
        Tools::redirect($this->context->link->getPageLink('order-confirmation', true, null, ['id_cart' => $cart->id, 'id_module' => $this->module->id, 'id_order' => $orderId, 'key' => $customer->secure_key]));
    }
}
