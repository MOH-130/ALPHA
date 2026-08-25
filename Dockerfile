FROM php:8.3-cli

WORKDIR /app

COPY . .

RUN mkdir -p storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t public"]
