FROM ubuntu:18.04 as builder

WORKDIR /
RUN DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get upgrade -y \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
    && mkdir /var/www \
    && git clone https://github.com/yeastgenome/yeastgfp-docker.git /var/www \
    && rm -rf /var/www/html/images \
    && mkdir /var/www/html/images

#####

FROM ubuntu:18.04

WORKDIR /
RUN DEBIAN_FRONTEND=noninteractive apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get upgrade -y \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    	apache2 \
	libapache2-mod-php \
        mysql-client \
	php \
	php-mysql \
	tzdata \
    && DEBIAN_FRONTEND=noninteractive apt-get autoremove -y \
    && mv /var/www /var/www.orig

COPY --from=builder /var/www/html /var/www/html/
COPY --from=builder /var/www/yeastgfp.conf /etc/apache2/sites-available/yeastgfp.conf

RUN a2ensite yeastgfp

CMD ["apachectl", "-D", "FOREGROUND"]
