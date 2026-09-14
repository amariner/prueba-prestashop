<section class="brisa-hero" aria-labelledby="brisa-hero-title">
  <div class="brisa-hero-copy">
    <span class="brisa-eyebrow"><span></span> TU CASA, EN SU MEJOR MOMENTO</span>
    <h1 id="brisa-hero-title">Que lo limpio<br>se sienta <em>bien.</em></h1>
    <p>Pequeños cuidados que cambian tu día.<br>Descubre los esenciales para un hogar muy tuyo.</p>
    <a class="brisa-button" href="{$brisa_catalog_url|escape:'html':'UTF-8'}">Encuentra tus esenciales <span>↗</span></a>
    <div class="brisa-hero-footnote"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v20M2 12h20M5 5l14 14M5 19 19 5"/></svg> Menos complicaciones. Más hogar.</div>
  </div>
  <div class="brisa-hero-art">
    <img src="{$urls.theme_assets}img/hero.webp" width="1200" height="1100" alt="Colección Brisa: botellas de limpieza en tonos verde salvia, crema y terracota" fetchpriority="high">
    <span class="brisa-art-caption">LOS ESENCIALES DE CADA DÍA · COLECCIÓN 01</span>
    <div class="brisa-roundel">un soplo<br>de <em>brisa</em><span>✳</span></div>
  </div>
</section>
<div class="brisa-benefits"><span><b>↗</b> Envío demo gratis desde 35 €</span><span><b>✳</b> Un esencial para cada rincón</span><span><b>♡</b> Cuidar tu casa, a tu manera</span></div>
<section class="brisa-section brisa-favorites" aria-labelledby="favorites-title">
  <div class="brisa-section-heading"><div><span class="brisa-eyebrow">LA BUENA RUTINA EMPIEZA AQUÍ</span><h2 id="favorites-title">Tus nuevos <em>imprescindibles.</em></h2></div><a class="brisa-text-link" href="{$brisa_catalog_url|escape:'html':'UTF-8'}">Ver toda la colección ↗</a></div>
  <div class="brisa-product-grid">
    {foreach $brisa_products as $product}
    <article class="brisa-product-card">
      <a class="brisa-product-image" href="{$product.url|escape:'html':'UTF-8'}"><span class="brisa-product-tag">COLECCIÓN BRISA</span><img src="{$product.image|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" width="800" height="800" loading="lazy"><span class="brisa-product-arrow" aria-hidden="true">↗</span></a>
      <div class="brisa-product-title"><h3><a href="{$product.url|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h3><span>{$product.price|escape:'html':'UTF-8'}</span></div><p>{$product.description|escape:'html':'UTF-8'}</p>
    </article>
    {/foreach}
  </div>
</section>
<section class="brisa-section brisa-categories" aria-labelledby="categories-title">
  <div class="brisa-section-heading"><div><span class="brisa-eyebrow">CADA ESPACIO TIENE SU BRISA</span><h2 id="categories-title">Un hogar cuidado.<br><em>Rincón a rincón.</em></h2></div><p>De las primeras sábanas del lunes<br>a la última taza del domingo.</p></div>
  <div class="brisa-category-grid">{foreach $brisa_categories as $category}<a class="brisa-category-card" href="{$category.url|escape:'html':'UTF-8'}"><span class="brisa-category-number">0{$category@iteration}</span><img src="{$urls.theme_assets}img/category-{$category@iteration}.webp" alt="Esenciales de {$category.name|escape:'html':'UTF-8'}" width="600" height="640" loading="lazy"><div><h3>{$category.name|escape:'html':'UTF-8'}</h3><span aria-hidden="true">↗</span></div></a>{/foreach}</div>
</section>
<section class="brisa-story">
  <div class="brisa-story-art"><img src="{$urls.theme_assets}img/ritual.webp" width="1000" height="1000" alt="Botella Brisa y paños de algodón junto a una rama de olivo" loading="lazy"></div>
  <div class="brisa-story-copy"><span class="brisa-eyebrow">MUCHO MÁS QUE UNA TAREA</span><h2>El placer de<br><em>volver a casa.</em></h2><p>Abrir las ventanas. Poner una lavadora.<br>Dejar la cocina lista para mañana.</p><p>Creemos en esos pequeños rituales que hacen de un espacio tu lugar favorito. Brisa nace para acompañarlos.</p><a class="brisa-text-link" href="{$brisa_catalog_url|escape:'html':'UTF-8'}">Dale una Brisa a tu rutina ↗</a></div>
</section>
<section class="brisa-closing"><span>✳</span><p>Hogar es donde<br><em>respiras a gusto.</em></p><a href="{$brisa_catalog_url|escape:'html':'UTF-8'}" class="brisa-button">Descubre la colección ↗</a></section>
