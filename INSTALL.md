### Requirements

- Debian 7

### Install Commands

sudo apt-get update
sudo apt-get install git apache2 mysql-server
mysql_secure_installation
sudo apt-get install php5 php-pear php5-mysql php5-mcrypt
sudo service apache2 restart

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

git clone https://github.com/CodeForAfrica/GotToVote-VRC-API.git
cd GotToVote-VRC-API

composer install

chmod -R 777 app/storage

mkdir app/config/local
cp app/config/app.php app/config/local/
cp app/config/database.php app/config/local/
nano app/config/local/app.php
nano app/config/local/database.php

sudo cp /etc/apache2/sites-available/default /etc/apache2/sites-available/gtv-vrc-api
sudo nano /etc/apache2/sites-available/gtv-vrc-api
sudo a2ensite gtv-vrc-api
sudo a2dissite default
sudo service apache2 reload
