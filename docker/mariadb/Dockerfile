FROM alpine:3.8

RUN apk upgrade --update --no-cache && \
    apk add --update --no-cache \
    ca-certificates \
    curl \
    bash \
    mariadb \
    mariadb-client \
    pwgen \
    && rm -rf /var/cache/apk/*

ADD ./run.sh /scripts/run.sh
RUN chmod -R 755 /scripts

EXPOSE 3306
VOLUME ["/var/lib/mysql"]

ENTRYPOINT ["/scripts/run.sh"]
