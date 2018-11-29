#!/bin/sh

now=$(date +"%s_%Y-%m-%d")
mysqldump --opt -h ${MYSQL_HOST} -u ${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE} | gzip > "/backup/${now}_${MYSQL_DATABASE}.sql.gz"
tar -zcf /backup/${now}_${MYSQL_DATABASE}.tar.gz /images

# cleanup (keep last 14 backups...)
ls -d -1t /backup/* | tail -n +`expr $BACKUP_RETENTION + $BACKUP_RETENTION + 1` | xargs rm -f
