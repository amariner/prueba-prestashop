# Brisa · PrestaShop demo

Tienda de limpieza en español, con PrestaShop **9.1.5**, PHP **8.4**, MySQL, tema hijo de **Hummingbird**, doce productos ficticios y checkout sin cobro. Compose fija MySQL **8.4**; la plantilla de Railway ha creado MySQL **9.4**.

Tienda: https://prestashop-production-cf43.up.railway.app · [Proyecto Railway](https://railway.com/project/ef9fdf0d-aa6e-4e55-9129-666726bd2806)

## Qué contiene

- `themes/brisa`: tema propio responsive. Portada editorial, navegación, buscador, pie y estilos de catálogo/checkout.
- `modules/brisastore`: escaparate conectado al catálogo nativo (precios, imágenes y enlaces reales).
- `modules/brisademo`: pedido de prueba con validación de sesión, CSRF y protección contra duplicados; sin pasarela bancaria.
- `catalog/products.json`: catálogo inicial. Solo se carga al crear la tienda; las ediciones posteriores se hacen en el back office.
- `scripts/generate-assets.mjs`: ilustraciones originales SVG y sus imágenes rasterizadas; `npm ci && npm run assets` para regenerarlas.
- `docker/` y `scripts/`: instalación, persistencia, configuración y comprobación de disponibilidad.

## Desarrollo local

Requiere Docker con Compose. Node solo es necesario para regenerar imágenes o ejecutar Playwright.

```sh
cp .env.example .env
# Edita .env: establece tres contraseñas distintas y un correo de administrador.
docker compose up --build -d
docker compose logs -f prestashop
```

Tienda: http://localhost:8080 · Administración: http://localhost:8080/admin-brisa/

El primer arranque instala PrestaShop y prepara el catálogo. Puede tardar varios minutos. Las credenciales son las definidas en `.env`. Las credenciales del despliegue de Railway se guardan localmente en `.secrets/access.json`, excluido de Git y Docker.

```sh
curl -f http://localhost:8080/health.php
npm ci
npx playwright install chromium
npm test
```

## Railway

La arquitectura consta de **dos servicios**, una réplica de aplicación y **dos volúmenes**:

| Servicio | Configuración |
|---|---|
| MySQL | Plantilla MySQL de Railway; volumen en `/var/lib/mysql`; sin exposición pública necesaria |
| prestashop | Dockerfile de este repositorio; volumen en `/data`; dominio HTTPS; puerto 8080 |

Variables del servicio `prestashop`:

| Variable | Valor |
|---|---|
| `DB_SERVER` | `${{MySQL.MYSQLHOST}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_NAME` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USER` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASSWD` | `${{MySQL.MYSQLPASSWORD}}` |
| `PS_DOMAIN` | Dominio Railway sin `https://` ni barra final |
| `PS_ENABLE_SSL` | `1` |
| `ADMIN_MAIL` | Correo del administrador inicial |
| `ADMIN_PASSWD` | Contraseña única del administrador inicial |
| `PORT` | `8080` |
| `BRISA_DATA_DIR` | `/data` |

`ADMIN_PASSWD` sirve para la instalación inicial. Cambiar esta variable después **no** cambia la contraseña de un empleado existente.

Crear el dominio antes del primer arranque. El contenedor interpreta el HTTPS del proxy Railway. `railway.json` configura el healthcheck, el plazo de instalación y el reinicio ante fallos.

En este despliegue de prueba, `DB_NAME=brisa_demo`: se creó una base nueva para recuperar el primer intento de instalación interrumpido. La base original y su volcado se conservaron en MySQL; se comprobó que no tenían productos, clientes ni pedidos.

```sh
railway link
railway service link prestashop
railway volume add --mount-path /data
railway domain --port 8080
# Configura las variables anteriores antes de desplegar.
railway up --service prestashop --detach
railway service source connect --service prestashop --repo amariner/prueba-prestashop --branch main
```

El contenedor no declara `VOLUME`; Railway gestiona los montajes, siguiendo el patrón de `bot-bin`.

## Persistencia y actualizaciones

`/data` conserva `img`, `upload`, `download`, los parámetros de instalación y los marcadores de instalación/catálogo. El código y el tema se reconstruyen desde Git. MySQL conserva productos, clientes, stock, configuración y pedidos en su propio volumen.

- Los redeploys **no reinstalan ni sobrescriben el catálogo**.
- No borres los marcadores para repetir la instalación. Si encuentra tablas sin el marcador, el arranque se detiene para evitar pérdida de datos.
- Las actualizaciones de núcleo requieren migración explícita; cambiar el argumento de versión del Dockerfile por sí solo no migra la base de datos.
- Instalar módulos o editar archivos desde el panel no incorpora sus archivos a Git. Para conservarlos tras reconstruir el contenedor, añade el módulo al repositorio (respetando su licencia) y pruébalo.
- Un volumen persistente **no es una copia de seguridad**. Antes de usar datos reales, configura backups coordinados de MySQL y `/data` y prueba una restauración. Esta demo no configura un plan de backups externo.
- Mantén una sola réplica mientras uses el volumen local de Railway.

## Alcance de la demo

Catálogo en EUR con impuesto simulado del 21% para España. Transporte simulado de 3,90 €, gratuito desde 35 € de productos. Stock inicial: 100 unidades por producto. Productos simples sin combinaciones.

Los pedidos se guardan como **Demo · Sin cobro**, sin factura ni envío. El correo saliente, las pasarelas reales y la analítica publicitaria se desactivan. Todas las respuestas llevan `X-Robots-Tag: noindex, nofollow, noarchive`. Introduce únicamente datos ficticios; el panel permite consultar los datos introducidos en las pruebas.

Antes de una tienda comercial faltan: identidad y documentación real del vendedor, fichas/composición/seguridad verificadas, transportistas y fiscalidad reales, pagos sandbox y luego producción, correo transaccional, backups, privacidad/cookies revisadas y validación comercial.

## Flujo de trabajo

1. Describe la tarea en el chat con el resultado esperado.
2. Modifica el tema o los módulos; conserva la lógica nativa de PrestaShop.
3. Ejecuta las comprobaciones de sintaxis, el build y el recorrido de compra.
4. Revisa el cambio en GitHub y despliega el commit en Railway.
5. Comprueba `/health.php`, producto, carrito, checkout y administración.

PrestaShop se descarga de la distribución oficial con versión y SHA-256 fijados. PrestaShop conserva su licencia OSL-3.0 y Hummingbird AFL-3.0. Las ilustraciones y la personalización Brisa son originales de esta demo.

DM Sans y Manrope se sirven desde el propio proyecto. Sus licencias SIL Open Font License se incluyen en `themes/brisa/assets/fonts/`.
