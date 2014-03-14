### Requirements

- Debian 7

### Install Commands

sudo apt-get update
sudo apt-get install git apache2 mysql-server
mysql_secure_installation
sudo apt-get install php5 php-pear php5-mysql
sudo service apache2 restart

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

git clone https://github.com/CodeForAfrica/GotToVote-VRC-API.git
cd GotToVote-VRC-API