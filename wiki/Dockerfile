FROM mediawiki:1.39.7 AS build

ENV MEDIAWIKI_EXT_BRANCH=REL1_39

RUN set -x; \
    apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y --no-install-recommends \
    git \
    libzip-dev \
    unzip \
    zlib1g-dev \
 && docker-php-ext-install \
    calendar \
    zip \
 && rm -rf /var/lib/apt/lists/*

RUN set -x; \
    apt-get update \
 && apt-get install -y --no-install-recommends \
    libonig-dev \
 && docker-php-ext-install \
    mbstring \
 && rm -rf /var/lib/apt/lists/* \
 && chown -R 1000 /var/www/html/vendor \
 && chown -R 1000 /var/www/html/extensions \
 && chown -R 1000 /var/www/html/skins

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

USER 1000
ENV COMPOSER_HOME=/tmp

# Install composer based extensions
RUN cd /var/www/html/ \
 && composer config platform.php 8.0 \
 && composer require --update-no-dev \
    "mediawiki/semantic-media-wiki  ~4.1.2" \
    "mediawiki/semantic-result-formats ~4.2.0" \
    "mediawiki/semantic-compound-queries ~2.2" \
    "jumbojett/openid-connect-php 1.0.0" \
    "mwstake/mediawiki-component-manifestregistry:~2.0"

# Install extensions with a git based install
RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/ExternalData.git /var/www/html/extensions/ExternalData

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/PageForms.git /var/www/html/extensions/PageForms

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/Arrays.git /var/www/html/extensions/Arrays

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/HeaderTabs.git /var/www/html/extensions/HeaderTabs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/ApprovedRevs.git /var/www/html/extensions/ApprovedRevs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/Auth_remoteuser.git /var/www/html/extensions/Auth_remoteuser

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/HierarchyBuilder.git /var/www/html/extensions/HierarchyBuilder

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/skins/Material.git /var/www/html/skins/Material

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/PluggableAuth.git /var/www/html/extensions/PluggableAuth

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/OpenIDConnect.git /var/www/html/extensions/OpenIDConnect \
 && sed -i "s/\$this->subject = \$this->getClaim( 'sub' );/\$this->subject = \$this->getClaim( \$GLOBALS['wgOpenIDConnect_SubjectUserInfoClaim'] );/" extensions/OpenIDConnect/includes/OpenIDConnect.php

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/UserMerge.git /var/www/html/extensions/UserMerge

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/NativeSvgHandler.git /var/www/html/extensions/NativeSvgHandler

RUN git clone --depth=1 https://gerrit.wikimedia.org/r/mediawiki/extensions/DrawioEditor /var/www/html/extensions/DrawioEditor

# DKL-LI-0003: remove unnecessary files
RUN rm -rf \
      /var/www/html/extensions/*/.git \
      /var/www/html/skins/*/.git

FROM ghcr.io/radiorabe/ubi8-minimal:1.3.6

LABEL vendor='Radio Bern RaBe' \
      maintainer='RaBe IT-Reaktion <it@rabe.ch>'

ENV HTTPD_CONTAINER_SCRIPTS_PATH=/usr/share/container-scripts/httpd/ \
    HTTPD_APP_ROOT=/var/www/html \
    HTTPD_CONFIGURATION_PATH=${APP_ROOT}/etc/httpd.d \
    HTTPD_MAIN_CONF_PATH=/etc/httpd/conf \
    HTTPD_MAIN_CONF_MODULES_D_PATH=/etc/httpd/conf.modules.d \
    HTTPD_MAIN_CONF_D_PATH=/etc/httpd/conf.d \
    HTTPD_TLS_CERT_PATH=/etc/httpd/tls \
    HTTPD_VAR_RUN=/var/run/httpd \
    HTTPD_DATA_PATH=/var/www \
    HTTPD_DATA_ORIG_PATH=/var/www \
    HTTPD_LOG_PATH=/var/log/httpd

# grab entrypoint scripts from an ubi8 image
COPY --from=registry.access.redhat.com/ubi9/httpd-24 /usr/libexec/httpd-prepare /usr/libexec/
COPY --from=registry.access.redhat.com/ubi9/httpd-24 /usr/bin/run-httpd /usr/bin/
COPY --from=registry.access.redhat.com/ubi9/httpd-24 /usr/bin/rpm-file-permissions /usr/bin/
COPY --from=registry.access.redhat.com/ubi9/httpd-24 /usr/share/container-scripts/httpd /usr/share/container-scripts/httpd

RUN    microdnf module enable php:8.0 \
    && rpm -hiv \
         https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm \
    && microdnf update -y \
    && microdnf install -y \
         jasper-libs \
         lcms2 \
         libgomp \
         libjpeg-turbo \
    && rpm -hiv \
         https://repo.almalinux.org/almalinux/8/AppStream/x86_64/os/Packages/LibRaw-0.19.5-4.el8.x86_64.rpm \
    && microdnf install -y \
         bind-utils \
         findutils \
         gettext \
         hostname \
         httpd \
         ImageMagick \
         mod_security \
         mod_session \
         mod_ssl \
         nss_wrapper \
         php \
         php-intl \
         php-mbstring \
         php-mysqlnd \
         sscg \
    && find /var/www/html -uid 1000 -exec chown 48 {} \; \
    && find /var/www/html -gid 1000 -exec chgrp 48 {} \; \
    && microdnf remove -y \
         nginx-filesystem \
         php-fpm \
    && sed --in-place \
         --expression='/LoadModule mpm_event_module/ s/^#*/#/' \
         --expression='s/#LoadModule mpm_prefork_module/LoadModule mpm_prefork_module/' \
       /etc/httpd/conf.modules.d/00-mpm.conf \
    && microdnf reinstall -y tzdata \
    && microdnf clean all \
    && chmod a-s \
         /usr/bin/* \
         /usr/sbin/* \
         /usr/libexec/*/* \
    && useradd -u 1001 -r -g 0 -s /sbin/nologin \
         -c "Default Application User" default \
    && chown 1001:0 \
         /etc/pki/tls/certs/ \
         /etc/pki/tls/private/

COPY --from=build /var/www/html /var/www/html

RUN    /usr/libexec/httpd-prepare \
    && rpm-file-permissions

RUN rm /etc/httpd/conf.d/mod_security.conf \
       /etc/httpd/conf.modules.d/10-mod_security.conf

COPY apache.conf /etc/httpd/conf.d/99-wiki.conf

COPY LocalSettings.php /var/www/html/LocalSettings.php 

COPY run-wiki /usr/bin/run-wiki

WORKDIR /var/www/html

USER 1001

CMD ["run-wiki"]
