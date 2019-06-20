# 104jb-c-slim
learning slim

#### Start up by Vagrant
`vagrant up --provision`

#### VM BOX default
```
default: SSH address: 127.0.0.1:2200
default: SSH username: ubuntu
default: SSH auth method: password
```
#### login VM BOX
```
Vagrant up && vagrant ssh   
```
#### 安裝 104 套件

請參考 [104-php-combuilder](https://github.com/104corp/104-php-combuilder/blob/master/README.md#%E5%A6%82%E4%BD%95%E4%BD%BF%E7%94%A8)

依照 104-php-combuilder 設定完後，即可引用套件：

```
$ composer require 104corp/jblog
```

#### run composer install/update
```
cd /var/www/html/jobs/search/ 
composer update   
```

#### change root
```
sudo -i  
```

