FROM mediawiki:1.35 AS build

ENV MEDIAWIKI_EXT_BRANCH REL1_35

RUN set -x; \
    apt-get update \
 && apt-get install -y --no-install-recommends \
    git \
    libzip-dev \
    unzip \
    zlib1g-dev \
 && docker-php-ext-install \
    zip \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring

COPY --from=composer:1.10 /usr/bin/composer /usr/bin/composer

# Install composer based extensions
RUN cd /var/www/html/ \
 && composer require mediawiki/semantic-media-wiki "~3.0" --update-no-dev \
 && composer require mediawiki/semantic-result-formats "~3.0" --update-no-dev \
 && composer require mediawiki/semantic-compound-queries "~2.1" --update-no-dev \
 && composer require jumbojett/openid-connect-php "0.5.0" --update-no-dev

# Install extensions with a git based install
RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/ExternalData.git /var/www/html/extensions/ExternalData

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/SemanticInternalObjects.git /var/www/html/extensions/SemanticInternalObjects

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/SemanticDrilldown.git /var/www/html/extensions/SemanticDrilldown

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/PageForms.git /var/www/html/extensions/PageForms

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/Arrays.git /var/www/html/extensions/Arrays

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/HeaderTabs.git /var/www/html/extensions/HeaderTabs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/ApprovedRevs.git /var/www/html/extensions/ApprovedRevs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/Auth_remoteuser.git /var/www/html/extensions/Auth_remoteuser

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/HierarchyBuilder.git /var/www/html/extensions/HierarchyBuilder

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/skins/Material.git /var/www/html/skins/Material

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/PluggableAuth.git /var/www/html/extensions/PluggableAuth

# Needs workaround for until https://github.com/wikimedia/mediawiki-extensions-OpenIDConnect/commit/d123d847d1cc7d1c4bb451f53b5cdee90948eea3 is released
RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/OpenIDConnect.git /var/www/html/extensions/OpenIDConnect \
 && sed -i '/if ( $oidc->authenticate() ) {/i $oidc->setRedirectURL(getenv("MW_AUTH_OIDC_REDIRECT_URI"));' extensions/OpenIDConnect/src/OpenIDConnect.php

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/UserMerge.git /var/www/html/extensions/UserMerge

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/NativeSvgHandler.git /var/www/html/extensions/NativeSvgHandler

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/mediawiki/extensions/DrawioEditor.git /var/www/html/extensions/DrawioEditor

FROM mediawiki:1.35

LABEL vendor='Radio Bern RaBe' \
      maintainer='RaBe IT-Reaktion <it@rabe.ch>'

RUN set -x; \
    apt-get update \
 && apt-get install -y --no-install-recommends \
    librsvg2-bin \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring

COPY --from=build /var/www/html /var/www/html

COPY apache.conf /etc/apache2/conf-enabled/99-wiki.conf

COPY LocalSettings.php /var/www/html/LocalSettings.php 

COPY wiki-cmd.sh /usr/local/bin/wiki-cmd

CMD ["wiki-cmd"]
