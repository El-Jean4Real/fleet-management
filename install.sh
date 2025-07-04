#!/bin/bash
set -e

echo "=== Mise à jour du système ==="
sudo apt update && sudo apt upgrade -y

echo "=== Installation Apache, MariaDB, PHP et extensions ==="
sudo apt install -y apache2 mariadb-server php php-mysql libapache2-mod-php php-xml php-mbstring php-curl php-zip php-gd php-bcmath git unzip

echo "=== Démarrage des services ==="
sudo systemctl enable apache2
sudo systemctl start apache2
sudo systemctl enable mariadb
sudo systemctl start mariadb

echo "=== Configuration MariaDB ==="
sudo mysql -e "CREATE DATABASE IF NOT EXISTS fleet_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'fleet_user'@'localhost' IDENTIFIED BY 'P@ssw0rdERPNEXT';"
sudo mysql -e "GRANT ALL PRIVILEGES ON fleet_db.* TO 'fleet_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "=== Clonage du dépôt Fleet Management ==="
cd /var/www/
sudo git clone https://github.com/El-Jean4Real/fleet-management.git fleet
sudo chown -R www-data:www-data fleet

echo "=== Import de la base de données ==="
sudo mysql -u fleet_user -pP@ssw0rdERPNEXT fleet_db < /var/www/fleet/install/assets/install.sql

echo "=== Configuration Apache ==="
sudo bash -c 'cat > /etc/apache2/sites-available/fleet.conf <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/fleet
    <Directory /var/www/fleet>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/fleet_error.log
    CustomLog \${APACHE_LOG_DIR}/fleet_access.log combined
</VirtualHost>
EOF'

echo "=== Activation du site Fleet et module rewrite ==="
sudo a2ensite fleet.conf
sudo a2enmod rewrite
sudo systemctl reload apache2

echo "=== Installation terminée ==="
echo "Tu peux maintenant accéder à l'application via http://<IP_SERVEUR>/fleet"
