# Semantic MediaWiki on Docker

This repo contains an opinionated installation of MediaWiki. It has all the
semantic wiki extensions installed and configured and is configured with
visualeditor support (aka the works).

## Installation

```bash
docker-compose pull
```

## Configuration

```bash
cp docker-compose.example.yml docker-compose.override.yml
$EDITOR docker-compose.override.yml
```

## Startup

```bash
docker-compose up -d
``

## Database migration

You need to run the migrations after the initial start of the conatiners so
the tables needed by the semantic extensions are installed.

You can also choose to import old data (see below) and run the migration after
that.

```bash
docker-compose exec wiki php maintenance/update.php --skip-external-dependencies --quick
``

## Backup database and images

```bash
docker-compose exec mysql mysqldump -uroot -p$MYSQL_ROOT_PASSWORD mediawiki > backup.sql

# TODO image volume backup
``

## Import data from old wiki

You need to place data.sql in `pwd` and then run the following commands

```bash
cat docker-composer.override.yml <<<EOD
mysql:
  volumes:
    - ./data.sql:/tmp/data.sql
EOD

docker-compose up -d

docker-compose exec mysql mysql -uroot -p$MYSQL_ROOT_PASSWORD mediawiki -e "source /tmp/data.sql"

docker-compose exec wiki php maintenance/update.php --skip-external-dependencies --quick
``

If you used `<code><pre>` instead of `<syntaxhighlight>` you can do this:

```bash
docker-compose exec wiki php extensions/ReplaceText/maintenance/replaceAll.php --nsall '<code><pre>' '<syntaxhighlight lang="bash">'
docker-compose exec wiki php extensions/ReplaceText/maintenance/replaceAll.php --nsall '</pre></code>' '</syntaxhighlight>'
```

## Confguration

Most configurable aspects may be found in the docker-compose yaml files.

I'll add a table here when I'm not lazy.
