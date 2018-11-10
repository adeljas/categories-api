#!/usr/bin/env bash

Update () {
    echo "-- Update packages --"
    sudo apt-get update
}

echo "-- Install tools and helpers --"
sudo apt-get install -y vim htop curl git npm build-essential libssl-dev

echo "-- Install PPA's --"
sudo add-apt-repository ppa:ondrej/php
Update


echo "-- Prepare configuration for MySQL --"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password root"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password root"


echo "-- Install packages --"
sudo apt-get install -y apache2 mysql-server git-core
sudo apt-get install -y php7.1-common php7.1-dev php7.1-json php7.1-opcache php7.1-cli libapache2-mod-php7.1
sudo apt-get install -y php7.1 php7.1-mysql php7.1-fpm php7.1-curl php7.1-gd php7.1-mcrypt php7.1-mbstring
sudo apt-get install -y php7.1-bcmath php7.1-zip
sudo apt-get install -y php7.1-xml
Update

echo "-- Configure PHP &Apache --"
sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.1/apache2/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.1/apache2/php.ini
sudo a2enmod rewrite



echo "-- Creating virtual hosts --"
cat << EOF | sudo tee /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    DocumentRoot /var/www/categories-api/api/web
    <Directory /var/www/categories-api/api/web>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>
    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>
EOF
sudo a2ensite default.conf

echo "prepare mysql db"
mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION; FLUSH PRIVILEGES;"
mysql -uroot -proot -e "CREATE DATABASE categories";

echo "-- Restart Apache --"

sudo /etc/init.d/apache2 restart

echo "-- Install Composer --"
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer


cd /var/www/

git clone https://github.com/adeljas/categories-api.git

sudo /etc/init.d/apache2 restart

cd /var/www/categories-api/api

composer install

php bin/console cache:clear --env=prod
php bin/console cache:clear --env=dev
php bin/console doctrine:schema:update --force


echo "-- running phpunit --"
./vendor/bin/simple-phpunit

sudo chown vagrant:vagrant /var/www/categories-api -R
sudo chmod a+wx /var/www/categories-api -R

echo "-- navigate to http://localhost:8080/ --"
######################