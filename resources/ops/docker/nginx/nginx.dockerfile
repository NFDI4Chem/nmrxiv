FROM node:15.5-alpine AS assets-build

WORKDIR /var/www/html

COPY . /var/www/html/

RUN npm ci
RUN npm run production

FROM nginx:1.19-alpine AS nginx

COPY /resources/ops/docker/nginx/vhost.conf /etc/nginx/conf.d/default.conf
COPY --from=assets-build /var/www/html/public /var/www/html/