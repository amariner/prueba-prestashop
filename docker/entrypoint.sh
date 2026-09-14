#!/usr/bin/env bash
set -Eeuo pipefail
umask 027
cd /var/www/html
export PS_DOMAIN="${PS_DOMAIN:-${RAILWAY_PUBLIC_DOMAIN:-}}"
export BRISA_DATA_DIR="${BRISA_DATA_DIR:-/data}"
for key in DB_SERVER DB_NAME DB_USER DB_PASSWD PS_DOMAIN ADMIN_MAIL ADMIN_PASSWD; do
    if [[ -z "${!key:-}" ]]; then echo "Missing required variable: $key" >&2; exit 1; fi
done
if [[ ! "${PORT:-8080}" =~ ^[0-9]+$ ]]; then echo 'Invalid PORT' >&2; exit 1; fi
sed -i "s/^Listen .*/Listen ${PORT:-8080}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:8080>/<VirtualHost *:${PORT:-8080}>/" /etc/apache2/sites-available/000-default.conf
mkdir -p "$BRISA_DATA_DIR" "$BRISA_DATA_DIR/config"
for folder in img upload download; do
    if [[ ! -d "$BRISA_DATA_DIR/$folder" ]]; then
        mkdir -p "$BRISA_DATA_DIR/$folder"
        cp -a "$folder/." "$BRISA_DATA_DIR/$folder/"
    fi
    if [[ ! -L "$folder" ]]; then rm -rf "$folder"; ln -s "$BRISA_DATA_DIR/$folder" "$folder"; fi
done
if [[ -f "$BRISA_DATA_DIR/config/parameters.php" ]]; then
    ln -sf "$BRISA_DATA_DIR/config/parameters.php" app/config/parameters.php
fi
chown -R www-data:www-data "$BRISA_DATA_DIR" var app/config
php /opt/brisa/scripts/wait-db.php
if [[ ! -f "$BRISA_DATA_DIR/install-complete" ]]; then
    # Never reinstall over an existing database or silently discard customer data.
    php /opt/brisa/scripts/check-empty-db.php
    echo 'Installing PrestaShop 9.1.5 (Spanish, EUR, empty catalog)...'
    # Run supported installer steps, then install assets in our actual admin folder.
    # The upstream finalizer assumes admin-dev if admin was already renamed.
    runuser -u www-data -- php -d memory_limit=768M install/index_cli.php \
        --domain="$PS_DOMAIN" --db_server="$DB_SERVER:${DB_PORT:-3306}" \
        --db_name="$DB_NAME" --db_user="$DB_USER" --db_password="$DB_PASSWD" \
        --prefix="${DB_PREFIX:-ps_}" --db_clear=0 --fixtures=0 \
        --step=database,modules,theme,postInstall \
        --name=Brisa --firstname=Admin --lastname=Brisa \
        --password="$ADMIN_PASSWD" --email="$ADMIN_MAIL" \
        --language=es --country=es --all_languages=0 --timezone=Europe/Madrid \
        --ssl="${PS_ENABLE_SSL:-1}" --theme=hummingbird \
        --modules=ps_shoppingcart,ps_customersignin,ps_searchbar,ps_categorytree,ps_contactinfo,ps_customeraccountlinks,ps_linklist,ps_mainmenu,contactform,ps_facetedsearch,ps_featuredproducts,ps_emailsubscription,ps_socialfollow,blockreassurance
    runuser -u www-data -- php bin/console assets:install admin-brisa --symlink --env=prod --no-debug
    cp app/config/parameters.php "$BRISA_DATA_DIR/config/parameters.php"
    chmod 640 "$BRISA_DATA_DIR/config/parameters.php"
    ln -sf "$BRISA_DATA_DIR/config/parameters.php" app/config/parameters.php
    touch "$BRISA_DATA_DIR/install-complete"
fi
rm -rf install
chown -R www-data:www-data "$BRISA_DATA_DIR" var app/config
runuser -u www-data -- php /opt/brisa/scripts/configure.php
if [[ ! -f "$BRISA_DATA_DIR/seed-v1-complete" ]]; then
    runuser -u www-data -- php /opt/brisa/scripts/seed.php
    runuser -u www-data -- php bin/console prestashop:theme:enable brisa --env=prod --no-debug
    runuser -u www-data -- php /opt/brisa/scripts/finalize.php
    touch "$BRISA_DATA_DIR/seed-v1-complete"
fi
runuser -u www-data -- php bin/console cache:clear --env=prod --no-debug --no-warmup
touch "$BRISA_DATA_DIR/ready"
echo 'Brisa is ready. Starting Apache.'
exec apache2-foreground
