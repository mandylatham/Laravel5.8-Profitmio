#!/bin/bash

export HOME=/root
export COMPOSER_HOME=/root
cd  /home/forge/migration-test.profitminer.io
cp .env.staging .env
composer install
php artisan key:generate
chown forge:forge -R /home/forge/migration-test.profitminer.io
aws ssm get-parameter --name /Pm/Staging/WebServers/environment --region us-east-1 --query Parameter.Value | sed -e 's/^"//' -e 's/"$//' | awk '{gsub(/\\n/,"\n")}1' >> .env
