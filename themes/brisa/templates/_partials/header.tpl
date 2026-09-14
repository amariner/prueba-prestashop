<div class="brisa-announcement"><span>Un pequeño gesto. Un hogar que se siente bien.</span><span class="brisa-demo-pill">TIENDA DEMO · SIN COBROS</span></div>
<div class="brisa-header">
  <a class="brisa-logo" href="{$urls.base_url}" aria-label="Brisa, inicio">brisa<span>®</span></a>
  <nav class="brisa-navigation" aria-label="Navegación principal">
    <a href="{$link->getCategoryLink(2)|escape:'html':'UTF-8'}">Todos los productos</a>
    {if isset($brisa_categories)}{foreach $brisa_categories as $category}<a href="{$category.url|escape:'html':'UTF-8'}">{$category.name|escape:'html':'UTF-8'}</a>{/foreach}{/if}
  </nav>
  <div class="brisa-header-actions">
    <button type="button" class="brisa-icon-button" data-brisa-search aria-label="Abrir buscador" aria-expanded="false"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg></button>
    <a class="brisa-icon-button" href="{$urls.pages.my_account}" aria-label="Mi cuenta"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7" r="3.5"/><path d="M4 22v-3a8 8 0 0 1 16 0v3"/></svg></a>
    <div class="brisa-cart-hook">{hook h='displayNav2' mod='ps_shoppingcart'}</div>
  </div>
</div>
<div class="brisa-search-panel" id="brisa-search-panel" hidden>
  <form method="get" action="{$urls.pages.search}"><label for="brisa-search">¿Qué necesita tu hogar?</label><div><input id="brisa-search" name="s" type="search" placeholder="Prueba «lavanda», «cocina» o «cristales»" required><button class="brisa-button" type="submit">Buscar →</button></div></form>
</div>
