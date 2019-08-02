#!/bin/bash

echo $1

# This probably won't export the image
gimp --no-interface --batch '(python-fu-tile RUN-NONINTERACTIVE)' '(gimp-quit TRUE)'
