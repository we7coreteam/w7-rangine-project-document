#!/bin/sh

# set these paths to match your environment
rm -rf ../public/docs/*
node_modules/apidoc/bin/apidoc -c "./frontend" -f ".*\\.php$" -i ../app/Controller -o ../public/docs/web/v1
