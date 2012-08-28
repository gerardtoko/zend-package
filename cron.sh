#!/bin/bash

# php cron.sh and binary php
php_cron=cron.php
php_bin=`which php`

# excution script
if [ -f "$php_cron" ];then
    $php_bin $php_cron $1 $2
fi
