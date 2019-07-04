#!/bin/sh
cd  /var/www/html/
# delete monitor and sleep, wait lb connect break
test -d  /var/www/html/jobs/monitor/ && rm /var/www/html/jobs/monitor/ -rf
sleep 30

# make empty folder
service apache2 stop
test -d /var/www/html/jobs/slim && rm /var/www/html/jobs/slim/ -rf
mkdir -p  /var/www/html/jobs/slim