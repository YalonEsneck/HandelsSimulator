#!/bin/bash
rootDir=$(realpath `dirname ${0}`/..)
webDir=${rootDir}/WebInterface

docker build --pull -t handelssimulator:dev docker/dev-server && \
\
docker run \
  --rm \
  --name handelsSimulator-dev \
  -v ${rootDir}/docker/dev-server/files/config/sites-enabled:/etc/apache2/sites-enabled \
  -v ${webDir}:/var/www/html \
  -p 4000:80 \
  handelssimulator:dev
