#!/bin/bash
rootDir=$(realpath `dirname ${0}`/..)
webDir=${rootDir}/WebInterface
imgExportDir=${rootDir}/image-workshop/exports

docker build --pull -t handelssimulator:dev docker/dev-server && \
\
docker run \
  --rm \
  --name handelsSimulator-dev \
  -v ${rootDir}/docker/dev-server/files/config/sites-enabled:/etc/apache2/sites-enabled \
  -v ${webDir}:/var/www/html \
  -v ${imgExportDir}:/var/www/html/src/HandelsSimulator/GraphicsEngine/Resources/images \
  -p 4000:80 \
  handelssimulator:dev
