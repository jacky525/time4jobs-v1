Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/xenial64"
    config.vm.network "forwarded_port", guest: 80, host: 8080
    config.vm.synced_folder ".", "/var/www/html/jobs/slim",
        :owner => "vagrant", :group => "vagrant",
        :mount_options => ["dmode=777,fmode=777"]
    config.vm.provider "virtualbox" do |vb|
      vb.memory = "1536"
    end

  config.vm.provision "shell", inline: <<-SHELL

 # Variables
 DBPASSWD=ruser

  # Install
  # Install dependencies
  sudo LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php -y
  sudo apt-get update
  debconf-set-selections <<< "mysql-server mysql-server/root_password password $DBPASSWD"
  debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DBPASSWD"


  sudo apt-get -y install zip unzip apache2 mysql-server php7.2 libapache2-mod-php7.2 sysv-rc-conf php-zip php-xml php-mbstring php7.2-curl php7.2-mysql php7.2-soap php7.2-opcache libapache2-mod-fastcgi php7.2-fpm php7.2-apcu
  sudo add-apt-repository "deb http://ftp.de.debian.org/debian sid main"
  sudo apt-get update

  #sudo apt-get -y --allow-unauthenticated install php-apcu php-apcu-bc
  export DEBIAN_FRONTEND=noninteractive
  sudo DEBIAN_FRONTEND=noninteractive apt-get -y --allow-unauthenticated install php7.2-dev
  sudo apt-get -y --allow-unauthenticated install php-pear
  sudo pecl channel-update pecl.php.net
  sudo echo '' | pecl install apcu
  sudo echo "extension=apcu.so" >> /etc/php/7.2/mods-available/cache.ini
  sudo ln -fs /etc/php/7.2/mods-available/cache.ini /etc/php/7.2/apache2/conf.d/25-cache.ini
  sudo service apache2 reload

  # Setup Apache Settings Here
  # 1. Replace Apache2.conf with our setup
  # 2. Use .conf to setup

  sudo rm /etc/apache2/apache2.conf
  sudo cp /var/www/html/jobs/slim/scripts/apache2.conf /etc/apache2/
  sudo cp /var/www/html/jobs/slim/scripts/slim.conf /etc/apache2/sites-enabled
  sudo a2ensite slim.conf
  sudo a2dissite 000-default.conf
  sudo a2enmod rewrite

  curl -Ss https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/bin/composer

  sudo service apache2 restart


  SHELL
  

end
