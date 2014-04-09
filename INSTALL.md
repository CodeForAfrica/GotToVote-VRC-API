### Requirements

- Debian 7

### Install with Nginx

sudo apt-get install git mysql-server php5-mysql php5-mcrypt php-pear

sudo mysql_install_db
sudo mysql_secure_installation

sudo apt-get install nginx
sudo nano /etc/nginx/sites-available/default
sudo service nginx start


sudo apt-get install php5-fpm
sudo service php5-fpm restart

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

git clone https://github.com/CodeForAfrica/GotToVote-VRC-API.git
cd GotToVote-VRC-API

composer install

chmod -R 777 app/storage

sudo apt-get install beanstalkd
sudo apt-get install supervisor

sudo apt-get install memcached php5-memcached
sudo service memcached restart
sudo service nginx restart



sudo unlink /var/run/supervisor.sock
supervisord -c /etc/supervisor.conf


### Install with Apache2

sudo apt-get update
sudo apt-get install git apache2 mysql-server
mysql_secure_installation
sudo apt-get install php5 php-pear php5-mysql php5-mcrypt php-apc memcached php5-memcache
sudo service apache2 restart

mysql -u root -p
mysql> SHOW DATABSES;
mysql> CREATE DATABASE gtv_vrc_api;
mysql> SHOW DATABSES;
mysql> CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
mysql> GRANT ALL PRIVILEGES ON gtv_vrc_api.* TO 'newuser'@'localhost';
mysql> FLUSH PRIVILEGES;
mysql> \q

curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

git clone https://github.com/CodeForAfrica/GotToVote-VRC-API.git
cd GotToVote-VRC-API

composer install

chmod -R 777 app/storage

mkdir app/config/local
cp app/config/app.php app/config/local/
cp app/config/database.php app/config/local/
php artisan key:generate --env=local
nano app/config/local/app.php
nano app/config/local/database.php
php artisan migrate --env=local

sudo cp /etc/apache2/sites-available/default /etc/apache2/sites-available/gtv-vrc-api
sudo nano /etc/apache2/sites-available/gtv-vrc-api
sudo a2ensite gtv-vrc-api
sudo a2dissite default
sudo service apache2 reload

#### References
- https://www.digitalocean.com/community/articles/how-to-install-linux-nginx-mysql-php-lemp-stack-on-debian-7
- https://www.digitalocean.com/community/articles/how-to-install-linux-apache-mysql-php-lamp-stack-on-debian
- https://www.digitalocean.com/community/articles/how-to-create-a-new-user-and-grant-permissions-in-mysql
- https://www.digitalocean.com/community/articles/how-to-install-and-manage-supervisor-on-ubuntu-and-debian-vps
- http://glenntaylor.co.uk/blog/read/laravel-queues-with-beanstalkd
- https://rtcamp.com/tutorials/php/increase-script-execution-time/
- http://www.pontikis.net/blog/install-memcached-php-debian
