FROM synctree/mediawiki:1.25

ENV MEDIAWIKI_EXT_BRANCH REL1_25

RUN set -x; \
    apt-get update \
    && apt-get install -y --no-install-recommends git \
    && apt-get install -y --no-install-recommends zlib1g-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install composer based extensions
RUN cd /usr/src/mediawiki/ \
    && composer require mediawiki/semantic-media-wiki "~2.3" --update-no-dev

RUN cd /usr/src/mediawiki/ \
    && composer require mediawiki/semantic-result-formats "~2.3" --update-no-dev

# Install extensions with a git based install
RUN set -x; \
    git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/VisualEditor.git /usr/src/mediawiki/extensions/VisualEditor \
    && cd /usr/src/mediawiki/extensions/VisualEditor \
    && git submodule update --init 

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/CategoryTree.git /usr/src/mediawiki/extensions/CategoryTree

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/ExternalData.git /usr/src/mediawiki/extensions/ExternalData

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/SemanticInternalObjects.git /usr/src/mediawiki/extensions/SemanticInternalObjects

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/SemanticCompoundQueries.git /usr/src/mediawiki/extensions/SemanticCompoundQueries

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/SemanticDrilldown.git /usr/src/mediawiki/extensions/SemanticDrilldown

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/SemanticForms.git /usr/src/mediawiki/extensions/SemanticForms

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/SemanticFormsInputs.git /usr/src/mediawiki/extensions/SemanticFormsInputs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/SemanticImageInput.git /usr/src/mediawiki/extensions/SemanticImageInput

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/Arrays.git /usr/src/mediawiki/extensions/Arrays

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/HeaderTabs.git /usr/src/mediawiki/extensions/HeaderTabs

RUN git clone --depth=1 -b $MEDIAWIKI_EXT_BRANCH https://gerrit.wikimedia.org/r/p/mediawiki/extensions/ApprovedRevs.git /usr/src/mediawiki/extensions/ApprovedRevs

COPY LocalSettings.php /var/www/html/LocalSettings.php 
