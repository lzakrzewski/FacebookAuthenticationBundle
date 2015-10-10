#!/usr/bin/env bash

echo "symfony version variable value: ";
echo $SYMFONY_VERSION;

SYMFONY_VERSION="$1";

if [ "$SYMFONY_VERSION" = "2.3.*" ];
    then
        sed -i 's/security.csrf.token_manager/form.csrf_provider/g' Tests/Integration/app/security.yml;
fi;

if [ "$SYMFONY_VERSION" != "" ];
    then
        composer require --no-update symfony/symfony:$SYMFONY_VERSION;
fi;