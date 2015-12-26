
## Installation, Startup

``bash
docker-compose up -d
``

## Database migration

``bash
docker-compose -f docker-compose-run.yml run update
``

## Backup database and images

``bash
docker-compose -f docker-compose-run.yml run backup_database
docker-compose -f docker-compose-run.yml run backup_images
``
