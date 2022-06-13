FROM mediawiki:1.37.2 AS build

ENV MEDIAWIKI_EXT_BRANCH REL1_37

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
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

# Install composer based extensions
RUN cd /var/www/html/ \
 && composer require mediawiki/semantic-media-wiki "~4.0" --update-no-dev \
 && rm composer.lock \
 && composer require mediawiki/semantic-result-formats "~4.0" --update-no-dev \
 && composer require mediawiki/semantic-compound-queries "~2.2" --update-no-dev \
 && composer require jumbojett/openid-connect-php "0.9.5" --update-no-dev

# Install extensions with a git based install
RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/ExternalData.git /var/www/html/extensions/ExternalData

# Use a version from the main branch that can be loaded with wfLoadExtension
# TODO remove after >v2.1 is release, probably in mw 1.38
RUN    git clone https://github.com/wikimedia/mediawiki-extensions-SemanticDrilldown.git /var/www/html/extensions/SemanticDrilldown \
    && cd /var/www/html/extensions/SemanticDrilldown \
    && git reset --hard f59cae7785ecafc10cd42e289c06232a9d34e1fe

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/PageForms.git /var/www/html/extensions/PageForms

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/Arrays.git /var/www/html/extensions/Arrays

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/HeaderTabs.git /var/www/html/extensions/HeaderTabs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/ApprovedRevs.git /var/www/html/extensions/ApprovedRevs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/Auth_remoteuser.git /var/www/html/extensions/Auth_remoteuser

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/HierarchyBuilder.git /var/www/html/extensions/HierarchyBuilder

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/skins/Material.git /var/www/html/skins/Material

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/PluggableAuth.git /var/www/html/extensions/PluggableAuth

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/OpenIDConnect.git /var/www/html/extensions/OpenIDConnect \
 && sed -i "s/\$oidc->requestUserInfo( 'sub' );/\$oidc->requestUserInfo( \$GLOBALS['wgOpenIDConnect_SubjectUserInfoClaim'] );/" /var/www/html/extensions/OpenIDConnect/src/OpenIDConnect.php

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/UserMerge.git /var/www/html/extensions/UserMerge

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/NativeSvgHandler.git /var/www/html/extensions/NativeSvgHandler

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://github.com/wikimedia/mediawiki-extensions-DrawioEditor.git /var/www/html/extensions/DrawioEditor

# DKL-LI-0003: remove unnecessary files
RUN rm -rf \
      /var/www/html/extensions/*/.git \
      /var/www/html/skins/*/.git

FROM ghcr.io/radiorabe/ubi8-minimal:0.4.0

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

# grab entrypoint scripts from fedora since no public cs8 images have them
COPY --from=registry.fedoraproject.org/f29/httpd /usr/libexec/httpd-prepare /usr/libexec/
COPY --from=registry.fedoraproject.org/f29/httpd /usr/bin/run-httpd /usr/bin/
COPY --from=registry.fedoraproject.org/f29/httpd /usr/bin/rpm-file-permissions /usr/bin/
COPY --from=registry.fedoraproject.org/f29/httpd /usr/share/container-scripts/httpd /usr/share/container-scripts/httpd

RUN    microdnf module enable php:7.4 \
    && rpm -hiv \
         https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm \
    && microdnf update -y \
    && microdnf install -y \
         jasper-libs \
         lcms2 \
         libgomp \
         libjpeg-turbo \
    && rpm -hiv \
         http://mirror.centos.org/centos/8-stream/AppStream/x86_64/os/Packages/LibRaw-0.19.5-2.el8.x86_64.rpm \
    && microdnf install -y \
         --enablerepo="ubi-8-codeready-builder" \
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

# like upstreams prep but deactivate chmoding ssl certs first
RUN    sed --in-place \
         --expression='/localhost/ s/^/#/' \
         /usr/libexec/httpd-prepare \
    && /usr/libexec/httpd-prepare \
    && rpm-file-permissions

COPY apache.conf /etc/httpd/conf.d/99-wiki.conf

COPY LocalSettings.php /var/www/html/LocalSettings.php 

COPY run-wiki /usr/bin/run-wiki

WORKDIR /var/www/html

USER 1001

CMD ["run-wiki"]

HEALTHCHECK CMD curl -f http://localhost:8080/ || exit 1
