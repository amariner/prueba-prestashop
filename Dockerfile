FROM php:8.4-apache-bookworm

ARG PRESTASHOP_VERSION=9.1.5
ARG PRESTASHOP_DISTRIBUTION=9.1.5-5.0
ARG PRESTASHOP_SHA256=37140cb77c03acf61b832f76893cd8fe1e304b0fc14fa5485cfea4a7bfaf3a72

RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libicu-dev \
    libzip-dev libonig-dev libxml2-dev unzip curl ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j2 gd intl pdo_mysql mbstring zip bcmath soap opcache \
    && a2enmod rewrite headers remoteip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
RUN curl --fail --location --retry 3 \
      "https://api.prestashop-project.org/assets/prestashop-classic/${PRESTASHOP_DISTRIBUTION}/prestashop.zip" \
      -o /tmp/release.zip \
    && echo "${PRESTASHOP_SHA256}  /tmp/release.zip" | sha256sum -c - \
    && unzip -q /tmp/release.zip prestashop.zip -d /tmp/package \
    && unzip -q /tmp/package/prestashop.zip -d /var/www/html \
    && test -f themes/hummingbird/assets/css/theme.css \
    && mv admin admin-brisa \
    && mkdir -p /opt/brisa/default-img \
    && cp -a img/. /opt/brisa/default-img/ \
    && rm -rf /tmp/release.zip /tmp/package \
    && chown -R www-data:www-data /var/www/html

COPY docker/php.ini /usr/local/etc/php/conf.d/brisa.ini
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/brisa-entrypoint
COPY scripts /opt/brisa/scripts
COPY catalog /opt/brisa/catalog
COPY --chown=www-data:www-data themes/brisa /var/www/html/themes/brisa
COPY --chown=www-data:www-data modules /var/www/html/modules
COPY docker/health.php /var/www/html/health.php
RUN chmod +x /usr/local/bin/brisa-entrypoint

ENV PORT=8080 PS_LANGUAGE=es PS_COUNTRY=es PS_ENABLE_SSL=1 \
    DB_PORT=3306 DB_PREFIX=ps_ BRISA_DATA_DIR=/data
EXPOSE 8080
ENTRYPOINT ["brisa-entrypoint"]
