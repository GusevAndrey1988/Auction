FROM nginx:1.23-alpine

COPY ./docker/common/nginx/snippets /etc/nginx/snippets
COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app