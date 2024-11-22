FROM php:8.3-apache

# 필요한 패키지 및 PHP 확장 모듈 설치
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install mysqli zip

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
    
# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
    
# Copy application files
COPY ./web /var/www/html/
    
EXPOSE 8080
    
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]