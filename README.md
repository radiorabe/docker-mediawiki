# Semantic MediaWiki on Docker

This repo contains an opinionated installation of MediaWiki. It has all the
semantic wiki extensions installed and configured and is configured with
visualeditor support (aka the works).

The mediawiki container image is based on the [RaBe Universal Base Image 8 Minimal](https://github.com/radiorabe/container-image-ubi8-minimal)
base image. The mediawiki source is copied from the upstream Mediawiki Container
image and it uses modular RPMs to install Apache 2.4 and `mod_php`.

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

## Release Management

The CI/CD setup uses semantic commit messages following the [conventional commits standard](https://www.conventionalcommits.org/en/v1.0.0/).
There is a GitHub Action in [.github/workflows/semantic-release.yaml](./.github/workflows/semantic-release.yaml)
that uses [go-semantic-commit](https://go-semantic-release.xyz/) to create new
releases.

The commit message should be structured as follows:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

The commit contains the following structural elements, to communicate intent to the consumers of your library:

1. **fix:** a commit of the type `fix` patches gets released with a PATCH version bump
1. **feat:** a commit of the type `feat` gets released as a MINOR version bump
1. **BREAKING CHANGE:** a commit that has a footer `BREAKING CHANGE:` gets released as a MAJOR version bump
1. types other than `fix:` and `feat:` are allowed and don't trigger a release

If a commit does not contain a conventional commit style message you can fix
it during the squash and merge operation on the PR.
