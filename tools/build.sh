#!/bin/sh

echo "Build W7 Document System."

date=$(date "+%Y%m%d%H%M%S")

mkdir ./w7-document
cd ./w7-document
git clone https://gitee.com/we7coreteam/document-apiserver.git .
composer install --no-dev  && composer clearcache
rm -rf composer.* .* LICENSE ./tools ./tests

cd ../
zip -r ./w7-document-$date.zip ./w7-document

rm -rf ./w7-document