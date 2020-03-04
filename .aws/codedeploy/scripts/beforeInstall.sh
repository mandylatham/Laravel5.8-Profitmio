#!/bin/bash

if [[ -d /home/profitminer/profitminer ]]; then
    rm -R /home/profitminer/profitminer
fi
if [[ -d /home/profitminer/app.profitminer.io && $DEPLOYMENT_GROUP_NAME == "production" ]]; then
    rm -R /home/profitminer/app.profitminer.io
fi
if [[ -d /home/profitminer/testing.profitminer.io && $DEPLOYMENT_GROUP_NAME == "testing" ]]; then
    rm -R /home/profitminer/testing.profitminer.io
fi
