#!/usr/bin/env bash

sudo apt-get update

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

sudo apt-get install -y apache2 php5 mysql-server php5-mysql php5-mcrypt

sudo php5enmod mcrypt

sudo a2dissite 000-default

sudo a2enmod rewrite

sudo sh -c 'cat > /etc/apache2/sites-available/api.conf <<EOL
<VirtualHost *:80>
    DocumentRoot /var/www/public
    <Directory /var/www/public>
        AllowOverride All
    </Directory>
</VirtualHost>
EOL'

sudo a2ensite api

sudo sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/apache2/php.ini

sudo service apache2 restart

mysql -u root -proot -e "CREATE DATABASE api"