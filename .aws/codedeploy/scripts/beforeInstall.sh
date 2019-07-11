#!/bin/bash

pip install --upgrade awscli
if [[ -d /home/forge/app.profitminer.io ]]; then
    rm -R /home/forge/app.profitminer.io
fi
