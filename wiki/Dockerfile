FROM synctree/mediawiki:1.25

RUN set -x; \
    apt-get update \
    && apt-get install -y --no-install-recommends git \
    && rm -rf /var/lib/apt/lists/*

RUN set -x; \
    git clone --depth=1 -b REL1_25 https://gerrit.wikimedia.org/r/p/mediawiki/extensions/VisualEditor.git /usr/src/mediawiki/extensions/VisualEditor \
    && cd /usr/src/mediawiki/extensions/VisualEditor \
    && git submodule update --init 

COPY LocalSettings.php /var/www/html/LocalSettings.php 
