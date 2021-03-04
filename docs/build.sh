#!/bin/sh

# set these paths to match your environment
rm -rf ./apidoc/*
node_modules/apidoc/bin/apidoc -c "./frontend" -f ".*\\.php$" -i ../app/Controller -o ./apidoc/web/v1

