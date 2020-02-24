#!/bin/bash

export HOME=/root
export COMPOSER_HOME=/root
if [[ $DEPLOYMENT_GROUP_NAME == "production" || $DEPLOYMENT_GROUP_NAME == "pm-prod-web-server-asg" ]]; then
    path=/home/profitminer/app.profitminer.io
elif [[ $DEPLOYMENT_GROUP_NAME == "testing" ]]; then
    path=/home/profitminer/testing.profitminer.io
fi
mv /home/profitminer/profitminer $path
cd $path
chown profitminer:profitminer -R $path
touch .env
cp .env.dusk.example .env.dusk
# Environment-Specific configuration
if [[ $DEPLOYMENT_GROUP_NAME == "production" || $DEPLOYMENT_GROUP_NAME == "pm-prod-web-server-asg" ]]; then
    /usr/local/bin/aws ssm get-parameter --name /Pm/Production/WebServers/environment --region us-east-1 --query Parameter.Value | sed -e "s/\\\\\\\/\\\/g" | sed -e 's/^"//' -e 's/"$//' | awk '{gsub(/\\n/,"\n")}1' >> .env
    /usr/local/bin/composer install --no-dev
elif [[ $DEPLOYMENT_GROUP_NAME == "testing" ]]; then
    /usr/local/bin/aws ssm get-parameter --name /Pm/Staging/WebServers/environment --region us-east-1 --query Parameter.Value | sed -e "s/\\\\\\\/\\\/g" | sed -e 's/^"//' -e 's/"$//' | awk '{gsub(/\\n/,"\n")}1' >> .env
    /usr/local/bin/composer install
else
    echo "This server is misconfigured for code deployment";
    exit 126;
fi

find $path -type f -exec chmod 664 {} \;
find $path -type d -exec chmod 755 {} \;
chmod -R 775 $path/storage $path/bootstrap/cache

semanage fcontext --add --type httpd_sys_content_t "${path}(/.*)?"
semanage fcontext --add --type httpd_sys_rw_content_t "${path}/storage(/.*)?"
semanage fcontext --add --type httpd_sys_rw_content_t "${path}/cache(/.*)?"
restorecon -Rv $path

# Run as root
chown profitminer:profitminer -R $path

su - profitminer

#php artisan key:generate
php artisan queue:restart

exit 0;
