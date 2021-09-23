# FROM node:15.5-alpine AS assets-build

# WORKDIR /var/www/

# COPY . /var/www/

# RUN npm ci
# RUN npm run production

FROM nginx:1.19-alpine AS nginx

COPY vhost.conf /etc/nginx/conf.d/default.conf
# COPY --from=assets-build /var/www/public /var/www/