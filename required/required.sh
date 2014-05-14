#!/bin/bash

# Copy needed files for Nginx, mysql and Yehoodi
sudo cp /var/www/required/my.cnf /etc/mysql/my.cnf
sudo cp /var/www/required/config.ini /var/www/application/config/config.ini
sudo cp /var/www/required/www.conf /etc/php5/fpm/pool.d/www.conf 

# Create temp dirs
mkdir -p /tmp/data/templates_c
mkdir -p /tmp/data/logs
sudo chmod 777 -R /tmp/data

# Extras?
#sudo cp /var/www/vagrant/php.ini /etc/php5/apache2/php.ini
