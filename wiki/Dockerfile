FROM mediawiki:1.37.1 AS build

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

# rm remove IDE aliases and add (hacky) compat for mw w/o SMWSQLStore2 (only v3)
# TODO remove rm once ext can be installed using wfLoadExtension
RUN    git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/SemanticInternalObjects.git /var/www/html/extensions/SemanticInternalObjects \
    && rm /var/www/html/extensions/SemanticMediaWiki/includes/IdeAliases.php \
    && sed -i \
         -e '/SMWSQLStore2/d' \
       extensions/SemanticInternalObjects/SemanticInternalObjects.php \
    && sed -i \
         -e 's/SMWSQLStore2/SMWSQLStore3/' \
       /var/www/html/extensions/SemanticInternalObjects/SemanticInternalObjects_body.php \
    && composer dump-autoload

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

FROM ghcr.io/radiorabe/ubi8-minimal:0.2.3

LABEL vendor='Radio Bern RaBe' \
      maintainer='RaBe IT-Reaktion <it@rabe.ch>'

RUN    microdnf module enable php:7.4 \
    && microdnf update -y \
    && microdnf install -y \
         httpd \
         php \
         php-intl \
         php-mbstring \
         php-mysqlnd \
    && microdnf remove -y \
         nginx-filesystem \
         php-fpm \
    && sed --in-place \
         -e '/LoadModule mpm_event_module/ s/^#*/#/' \
         -e 's/#LoadModule mpm_prefork_module/LoadModule mpm_prefork_module/' \
       /etc/httpd/conf.modules.d/00-mpm.conf \
    && microdnf reinstall -y tzdata \
    && microdnf clean all \
    && chmod a-s \
         /usr/bin/* \
         /usr/sbin/*

COPY --from=build /var/www/html /var/www/html

# override config w/o fpm
COPY php.conf /etc/httpd/conf.d/php.conf
COPY apache.conf /etc/httpd/conf.d/99-wiki.conf

COPY LocalSettings.php /var/www/html/LocalSettings.php 

COPY wiki-cmd.sh /usr/local/bin/wiki-cmd

WORKDIR /var/www/html

CMD ["wiki-cmd"]

HEALTHCHECK CMD curl -f http://localhost/ || exit 1
