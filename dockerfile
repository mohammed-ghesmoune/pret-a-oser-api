# Set the base image for subsequent instructions
FROM php:8.1.2-fpm


# Update packages + Install PHP and composer dependencies
RUN apt-get update -qq && apt-get install -qqy --no-install-recommends \
wget \
git \
curl \
zip \
unzip \
g++ \
libonig-dev \
libxml2-dev \
zlib1g-dev \
libpng-dev \
libicu-dev \
libfreetype6-dev \
libjpeg62-turbo-dev  \
# Remove cache
&& apt-get clean && rm -rf /var/lib/apt/lists/*


# Install needed extensions
# Here you can install any other extension that you need during the test and deployment process
RUN pecl install xdebug  && docker-php-ext-enable xdebug

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
&& docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-configure intl \
&& docker-php-ext-install intl

RUN docker-php-ext-install \
pdo_mysql


# Configure php-cli
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Install Composer
RUN curl --silent --show-error "https://getcomposer.org/installer" | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel Envoy
RUN composer global require "laravel/envoy=~1.0"

# Install symfony-cli
RUN wget https://get.symfony.com/cli/installer -O - | bash \
&& mv /root/.symfony/bin/symfony /usr/local/bin/symfony
