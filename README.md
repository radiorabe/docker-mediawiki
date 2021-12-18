# Semantic MediaWiki on Docker

This repo contains an opinionated installation of MediaWiki. It has all the
semantic wiki extensions installed and configured and is configured with
visualeditor support (aka the works).

## Installation

```bash
docker-compose pull
```

## Upgrading

Before merging Mediawiki upgrades we need to check if all of our extensions are compatible with the new version. Special care needs to be
taken with those extensions that are installed via composer and not using the `MEDIAWIKI_EXT_BRANCH` env variable in wiki/Dockerfile.

Some extensions publish their own compatibility information.
* [Semantic MediaWiki Compatibility Matrix](https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/COMPATIBILITY.md)

## Configuration

Configuration is done through environmaent variables. Most configurable aspects may be found in the docker-compose yaml files.

The file `docker-compose.example.yml` describes some of the variables. You can copy it to `docker-composer.override.yml` as is to get started.

```bash
cp docker-compose.example.yml docker-compose.override.yml
$EDITOR docker-compose.override.yml
```

I'll add a table here when I'm not lazy.

## Startup

```bash
docker-compose up -d
```

## Database migration

You need to run the migrations after the initial start of the containers so
the tables needed by the semantic extensions are installed.

You can also choose to import old data (see below) and run the migration after
that.

```bash
docker-compose exec wiki php maintenance/update.php --skip-external-dependencies --quick
```

## Backup database and images

```bash
docker-compose exec mysql mysqldump -uroot -p$MYSQL_ROOT_PASSWORD mediawiki > backup.sql

# TODO image volume backup
```

## Import data from old wiki

You can SQL file containing a `mysqldump` in `./data.sql` and then run the following commands.

```bash
cat docker-composer.override.yml <<<EOD
mysql:
  volumes:
    - ./data.sql:/tmp/data.sql
EOD

docker-compose up -d

docker-compose exec mysql mysql -uroot -p$MYSQL_ROOT_PASSWORD mediawiki -e "source /tmp/data.sql"

docker-compose exec wiki php maintenance/update.php --skip-external-dependencies --quick
```

If you used `<code><pre>` instead of `<syntaxhighlight>` you can do this:

```bash
docker-compose exec wiki php extensions/ReplaceText/maintenance/replaceAll.php --nsall '<code><pre>' '<syntaxhighlight lang="bash">'
docker-compose exec wiki php extensions/ReplaceText/maintenance/replaceAll.php --nsall '</pre></code>' '</syntaxhighlight>'
```

Now is also the time to do some final cleanup and optimization after having migrated your wiki to this stack.

```bash
docker-compose exec wiki php maintenance/runJobs.php
```

If you had to wait a long time for the jobs to run you might want to run the updater again so it can optimize the db tables after the jobs have run.

```bash
docker-compose exec wiki php maintenance/update.php --skip-external-dependencies --quick
```


