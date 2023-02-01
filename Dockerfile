FROM ubuntu:22.04

ARG DEBIAN_FRONTEND=noninteractive
ARG HOST_UID=1000
ARG HOST_GID=1000

RUN apt-get update && apt-get install -y software-properties-common curl zip git
RUN apt-get install -y nginx gettext-base
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update
RUN apt-get install -y php7.4 php7.4-fpm php7.4-dev php7.4-curl

RUN mkdir -p /run/php && touch /run/php/php7.4-fpm.sock && touch /run/php/php7.4-fpm.pid

WORKDIR /root
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN git clone https://github.com/bitcoin-core/secp256k1
WORKDIR /root/secp256k1
RUN ./autogen.sh
RUN ./configure --enable-experimental --enable-module-ecdh --enable-module-recovery
RUN make && make install

WORKDIR /root
RUN git clone https://github.com/furqansiddiqui/secp256k1-php
WORKDIR /root/secp256k1-php/secp256k1
RUN phpize
RUN ./configure --with-secp256k1
RUN make && make install
RUN echo 'extension=secp256k1.so' > /etc/php/7.4/mods-available/secp256k1.ini
RUN phpenmod secp256k1

WORKDIR /root
COPY ./etc/nginx.conf /etc/nginx/nginx.template.conf
COPY ./etc/php7.4-fpm.conf /etc/php/7.4/fpm/pool.d/www.conf
COPY ./etc/entrypoint.sh /root/entrypoint.sh

WORKDIR /etc/nginx
RUN rm -rf sites-available sites-enabled nginx.conf

RUN groupadd -g $HOST_GID furqansiddiqui
RUN adduser --disabled-password --gecos '' -u $HOST_UID -gid $HOST_GID furqansiddiqui

USER furqansiddiqui
WORKDIR /home/furqansiddiqui/
COPY server secp256k1-rpc/

USER root
WORKDIR /home/furqansiddiqui/secp256k1-rpc/
RUN mkdir log && touch access.log && touch error.log
WORKDIR /root
RUN chmod +x entrypoint.sh
RUN chown -R furqansiddiqui:furqansiddiqui /home/furqansiddiqui/*
RUN chmod +x /home/furqansiddiqui
ENTRYPOINT ["./entrypoint.sh"]
