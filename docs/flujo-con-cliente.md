# Trabajo con el cliente

Esta prueba permite validar diseño, catálogo y compra en PrestaShop antes de contratar o configurar los servicios comerciales.

## Entrega de la prueba

1. Revisar la portada en móvil y escritorio, las cuatro categorías y una ficha de producto.
2. Hacer un pedido con datos ficticios y comprobarlo en Pedidos. Debe figurar como «Demo · Sin cobro».
3. Entrar en Catálogo → Productos, editar una descripción y comprobar el cambio en la tienda.
4. Acordar cambios de marca, contenido y navegación. Registrar cada cambio con un resultado verificable y una persona que lo aprueba.

## Reparto de tareas

| Trabajo | Responsable habitual | Dónde se gestiona |
|---|---|---|
| Textos, imágenes, referencias, precios y stock | Cliente, con revisión inicial del equipo | Panel de PrestaShop; CSV para cargas en lote |
| Identidad visual, plantillas y funcionalidades | Equipo técnico desde este chat | Tema y módulos propios en GitHub |
| Pedidos y atención al comprador | Cliente | Panel de PrestaShop |
| Versiones, incidencias y despliegues | Equipo técnico | GitHub y Railway |
| Aprobación comercial de cambios | Cliente | Lista de aceptación y revisión de la tienda de pruebas |

El catálogo inicial del repositorio solo sirve para crear esta demo. Una edición de producto posterior se conserva en MySQL y no se sobrescribe al desplegar código. Las imágenes subidas al panel se conservan en el volumen de la aplicación.

## Peticiones desde el chat

Indicar la URL o referencia del producto afectado, el comportamiento actual y el resultado esperado. Para incidencias, añadir los pasos para reproducirlas y la hora aproximada. No pegar contraseñas ni datos personales de compradores en las conversaciones o en GitHub.

Para cambios de código: preparar el cambio, probarlo, revisar la vista previa y desplegar la versión acordada. Para cambios masivos del catálogo: exportar primero, preparar un CSV de revisión y validar referencias, precios e imágenes antes de importar. No modificar el núcleo de PrestaShop para personalizar la tienda.

## Paso a una tienda comercial

Crear entornos separados de pruebas y producción, con credenciales y bases de datos distintas. Sustituir todos los productos y textos ficticios. Validar con el cliente la documentación del vendedor y del producto, impuestos, zonas y tarifas de transporte, devoluciones y privacidad.

Integrar el proveedor de pagos primero en su entorno de pruebas y verificar pago, cancelación, devolución y notificaciones. Configurar correo transaccional y verificar su entrega. Sustituir el módulo de pedidos ficticios antes de aceptar compras reales.

Configurar copias coordinadas de MySQL y los archivos persistentes; comprobar una restauración. Acordar quién responde a incidencias, el plazo de respuesta y la ventana de mantenimiento. Railway sirve para esta prueba; la continuidad comercial exige revisar capacidad, costes, backups y disponibilidad con el cliente.

## Mantenimiento

Revisar errores, disponibilidad, espacio del volumen y copias de seguridad. Probar las actualizaciones de PrestaShop, PHP, tema y módulos en el entorno de pruebas antes de aplicarlas. Una actualización del núcleo puede necesitar migraciones de base de datos: volver a una imagen antigua de Docker no basta para deshacerlas.

Mantener en Git los módulos y archivos personalizados que deban sobrevivir a un despliegue. Las tareas recurrentes y avisos desde el chat requieren configurar una automatización expresamente; esta demo no crea monitorización ni avisos programados.
