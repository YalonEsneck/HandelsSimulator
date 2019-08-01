#!/bin/bash
rootDir=$(realpath `dirname ${0}`)
webDir=${rootDir}/WebInterface
docker run \
  --rm \
  --name handelsSimulator-dev \
  -v ${rootDir}/docker/dev-server/files/config/sites-enabled:/etc/apache2/sites-enabled \
  -v ${webDir}:/var/www/html \
  -p 4000:80 \
  php:apache
