#!/bin/bash

USER="Arip"
HOST="ftp.pharm36.ru"

set -o errexit
set -o pipefail

if [ -f "$POSTGRES_PASSWORD_FILE" ]; then
  POSTGRES_PASSWORD="$(cat "$POSTGRES_PASSWORD_FILE")"
fi

BACKUP_FILE="${BACKUP_NAME:?}_$(date +%Y-%m-%d_%H-%M).sql.gz"

echo "Dump $BACKUP_FILE"

export PGPASSWORD="${POSTGRES_PASSWORD:?}"

pg_dump \
    --dbname="${POSTGRES_DB:?}" \
    --username="${POSTGRES_USERNAME:?}" \
    --host="${POSTGRES_HOST:?}" \
    | gzip -9 > "$BACKUP_FILE"

#echo "Upload to Synology NAS"
#
#rsync -av "$BACKUP_FILE" $USER@"${NAS_HOST:?}"::NetBackup/"$BACKUP_FILE"

echo "Send to ftp server"

lftp -u $USER,"$FTP_PASSWORD" $HOST <<EOF
cd pharm36
put $BACKUP_FILE
bye
EOF

unlink "$BACKUP_FILE"
