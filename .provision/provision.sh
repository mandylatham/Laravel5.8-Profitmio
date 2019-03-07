#!/bin/bash

# Resolves apt interactivity issues
export DEBIAN_FRONTEND=noninteractive

exe () {
    MESSAGE_PREFIX="\b\b\b\b\b\b\b\b\b\b"
    echo -e "$MESSAGE_PREFIX Execute: $1"
    LOOP=0
    while true;
    do
        if ! [ $LOOP == 0 ]; then echo -ne "$MESSAGE_PREFIX ...     \r"; fi;
        sleep 3;
        LOOP=$((LOOP+1))
    done & ERROR=$("${@:2}" 2>&1)
    status=$?
    kill $!; trap 'kill $!' SIGTERM

    if [ $status -ne 0 ];
    then
        echo -e "$MESSAGE_PREFIX ✖ Error" >&2
        echo -e "$ERROR" >&2
    else
        echo -e "$MESSAGE_PREFIX ✔ Success"
    fi
    return $status
}

exe 'Update apt indexes' \
    sudo apt update

exe 'Install additional php packages' \
    sudo DEBIAN_FRONTEND=noninteractive apt -o Dpkg::Options::="--force-confnew" install -y --force-yes php7.1-gd php7.1-json php7.1-xml php7.1-zip php-imagick php7.1-mcrypt

exe 'Install chrome dependencies for chromium and laravel dusk' \
    sudo apt-get -y install libxpm4 libxrender1 libgtk2.0-0 libnss3 libgconf-2-4

exe 'Install chromium' \
    sudo apt-get install chromium-browser

exe 'Install XVFB for chromium and laravel dusk' \
    sudo apt-get -y install xvfb gtk2-engines-pixbuf

exe 'Install chrome fonts' \
    sudo apt-get -y install xfonts-cyrillic xfonts-100dpi xfonts-75dpi xfonts-base xfonts-scalable

exe 'Support for screen capturing' \
    sudo apt-get -y install imagemagick x11-apps

exe 'Install ghostscript' \
    sudo apt install -y ghostscript

exe 'Create nginx ssl directory' \
    sudo mkdir -p /etc/nginx/ssl

exe 'Create self-signed certificate' \
    sudo openssl req -x509 -nodes -days 2365 -newkey rsa:2048 -keyout /etc/nginx/ssl/self-signed.key -out \
    /etc/nginx/ssl/self-signed.crt -subj \
    '/C=GB/ST=Location/L=Location/O=Company/OU=IT Department/CN=example.tld'

exe 'Removing any enabled sites from /etc/nginx/sites-enabled' \
    sudo rm -f /etc/nginx/sites-enabled/*

exe 'Copying vhost file to /etc/nginx/sites-available' \
    sudo cp /home/vagrant/app.profitminer.io/.provision/app-profitminer-io.conf /etc/nginx/sites-available/

exe 'Creating symlink to sites-enabled' \
    sudo ln -s /etc/nginx/sites-available/app-profitminer-io.conf /etc/nginx/sites-enabled/app-profitminer-io

exe 'Restarting nginx' \
    sudo service nginx restart

exe 'Fire up xvfb' \
    Xvfb -ac :0 -screen 0 1280x1024x16 &

