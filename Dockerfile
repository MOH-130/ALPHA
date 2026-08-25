FROM php:8.3-cli

WORKDIR /app

COPY . .

RUN chmod -R 775 storage bootstrap/cache 2>$null

EXPOSE 10000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t public"]
