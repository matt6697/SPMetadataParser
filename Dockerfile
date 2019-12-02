FROM debian:buster-slim

RUN apt-get update \
    && apt-get install -y wget apache2 libapache2-mod-php7.3 php7.3-curl php7.3-gmp php7.3-mysql php7.3-sqlite php7.3-mbstring php7.3-xml composer

COPY . /var/www/html

RUN rm /var/www/html/index.html \
    && mv /var/www/html/apache2/000-default.conf /etc/apache2/sites-available/ \
    && a2enmod headers rewrite

#allow SSL connections with protocol < TLS1.2
RUN sed -i '355,355 s/ssl_conf/#ssl_conf/' /etc/ssl/openssl.cnf

EXPOSE 80
CMD apachectl -D FOREGROUND
