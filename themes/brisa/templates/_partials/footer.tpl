<div class="brisa-footer-inner">
  <div class="brisa-footer-brand"><a class="brisa-logo" href="{$urls.base_url}">brisa<span>®</span></a><p>Lo cotidiano,<br>un poco más bonito.</p><span>CUIDADO DEL HOGAR</span></div>
  <div><h2>Encuentra tu Brisa</h2><a href="{$link->getCategoryLink(2)|escape:'html':'UTF-8'}">Todos los productos</a>{if isset($brisa_categories)}{foreach $brisa_categories as $category}<a href="{$category.url|escape:'html':'UTF-8'}">{$category.name|escape:'html':'UTF-8'}</a>{/foreach}{/if}</div>
  <div><h2>Estamos cerca</h2><a href="{$urls.pages.my_account}">Mi cuenta</a><a href="{$urls.pages.history}">Mis pedidos</a>{if isset($brisa_cms)}{foreach $brisa_cms as $cms}<a href="{$cms.url|escape:'html':'UTF-8'}">{$cms.title|escape:'html':'UTF-8'}</a>{/foreach}{/if}</div>
  <div class="brisa-footer-note"><span class="brisa-small-label">HECHO PARA PROBAR</span><p>Una tienda de demostración con productos ficticios. Puedes explorar y hacer un pedido de prueba, sin pagar.</p><a href="{$link->getCategoryLink(2)|escape:'html':'UTF-8'}">Empieza por lo esencial ↗</a></div>
</div>
<div class="brisa-footer-bottom"><span>© {$smarty.now|date_format:'%Y'} Brisa. Proyecto de demostración.</span><span>EUR € · Español</span><span>Una casa. Mil pequeños cuidados.</span></div>
{hook h='displayFooterAfter'}
