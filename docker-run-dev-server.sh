#!/bin/bash
rootDir=$(realpath `dirname ${0}`)/WebInterface
docker run --rm --name handelssimulator-dev -v ${rootDir}:/var/www -v ${rootDir}/public:/var/www/html -p 4000:80 php:apache
