#! /bin/bash

MW_BRANCH=$1
EXTENSION_NAME=$2

wget https://github.com/wikimedia/mediawiki/archive/$MW_BRANCH.tar.gz -nv

tar -zxf $MW_BRANCH.tar.gz
mv mediawiki-$MW_BRANCH mediawiki

cd mediawiki

composer install
maintenance/run install --dbtype sqlite --dbuser root --dbname mw --dbpath $(pwd) --pass AdminPassword WikiName AdminUser

echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgShowDBErrorBacktrace = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php

echo 'wfLoadExtension( "TemplateStyles" );' >> LocalSettings.php
echo 'wfLoadExtension( "TemplateStylesUnlimited" );' >> LocalSettings.php

cat <<EOT >> composer.local.json
{
	"require": {},
	"extra": {
		"merge-plugin": {
			"merge-dev": true,
			"include": []
		}
	}
}
EOT
