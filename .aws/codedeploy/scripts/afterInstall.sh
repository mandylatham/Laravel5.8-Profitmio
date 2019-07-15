#!/bin/bash

export HOME=/root
export COMPOSER_HOME=/root
cd  /home/forge/app.profitminer.io
echo '{"github-oauth": {"github.com": "5cd9cd2879e7f6c642c0fdceedfd68b9dc6345fb"}}' > auth.json
cp .env.staging .env
cp .env.dusk.example .env.dusk

# Environment-Specific configuration
if [[ $CODEDEPLOY_ENVIRONMENT == "production" ]]; then
    aws ssm get-parameter --name /Pm/Production/WebServers/environment --region us-east-1 --query Parameter.Value | sed -e 's/^"//' -e 's/"$//' | awk '{gsub(/\\n/,"\n")}1' >> .env
elif [[ $CODEDEPLOY_ENVIRONMENT == "testing" ]]; then
    aws ssm get-parameter --name /Pm/Staging/WebServers/environment --region us-east-1 --query Parameter.Value | sed -e 's/^"//' -e 's/"$//' | awk '{gsub(/\\n/,"\n")}1' >> .env
else
    echo "This server is misconfigured for code deployment";
    exit 126;
fi

composer install --no-dev
chown forge:forge -R /home/forge/app.profitminer.io
php artisan key:generate
php artisan queue:restart

exit 0;
