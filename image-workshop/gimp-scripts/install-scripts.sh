#!/bin/bash
rootDir=$(realpath `dirname ${0}`)
ln -fs ${rootDir}/*.py ~/.gimp-*/plug-ins
