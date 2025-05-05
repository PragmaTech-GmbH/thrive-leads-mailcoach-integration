#!/bin/bash
# Install and configure Xdebug in the Docker container for code coverage generation

set -e

echo "Installing Xdebug for code coverage..."

# Install required packages
apt-get update && apt-get install -y \
    build-essential \
    autoconf \
    libxml2-dev \
    zlib1g-dev

# Install Xdebug via PECL
pecl install xdebug

# Configure Xdebug for code coverage
echo "zend_extension=xdebug.so
xdebug.mode=coverage" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Verify installation
echo "Verifying Xdebug installation..."
php -m | grep -i xdebug

echo "Xdebug installed successfully for code coverage generation."