FROM alpine:3.18

RUN apk add --no-cache python3 py3-pip bash coreutils postgresql-client lftp \
    && pip3 install --upgrade awscli

COPY ./wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

COPY ./backup/backup.sh /usr/local/bin/backup
RUN chmod 555 /usr/local/bin/backup

WORKDIR /app

CMD ["backup"]
