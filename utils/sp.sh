#!/bin/bash
chown -R root:www-data /var/www/skilldev
find /var/www/skilldev/ -type d -print0 | xargs -0 chmod 0775
find /var/www/skilldev/ -type f -print0 | xargs -0 chmod 0664
