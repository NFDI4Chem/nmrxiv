FROM nginx:1.19-alpine AS nginx

COPY /resources/ops/docker/nginx/vhost.conf /etc/nginx/conf.d/default.conf
COPY --from=assets-build /var/www/html/public /var/www/html