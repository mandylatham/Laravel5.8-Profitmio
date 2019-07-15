#!/bin/bash

if [[ $CODEDEPLOY_ENVIRONMENT -eq "" ]]; then
    echo "This server is not configured for CodeDeploy script use";
    exit 125;
fi

pip install --upgrade awscli
if [[ -d /home/forge/app.profitminer.io ]]; then
    rm -R /home/forge/app.profitminer.io
fi
