#!/bin/bash
export ESC='$'
envsubst < /etc/nginx/nginx.template.conf > /etc/nginx/nginx.conf
service php7.4-fpm restart
cd /home/furqansiddiqui/secp256k1-rpc/
composer update
chown -R furqansiddiqui:furqansiddiqui /home/furqansiddiqui/secp256k1-rpc/vendor
cd ~
/usr/sbin/nginx -g 'daemon off;'
