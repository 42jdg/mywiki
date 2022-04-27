# My Wiki
Place this app in **nextcloud/apps/**


## Disclaimer: 
**This is not a real wiki**
This app allow me to display the folders where I organize some documentation within a browsable struture  
Many things could be wrong (this is my first app for nextcloud), I would appreciate any comments/help





## Building the app

The app can be built by using the provided Makefile by running:

    make

This requires the following things to be present:
* make
* which
* tar: for building the archive
* curl: used if phpunit and composer are not installed to fetch them from the web
* npm: for building and testing everything JS, only required if a package.json is placed inside the **js/** folder

The make command will install or update Composer dependencies if a composer.json is present and also **npm run build** if a package.json is present in the **js/** folder. The npm **build** script should use local paths for build systems and package managers, so people that simply want to build the app won't need to install npm libraries globally, e.g.:

**package.json**:
```json
"scripts": {
    "test": "node node_modules/gulp-cli/bin/gulp.js karma",
    "prebuild": "npm install && node_modules/bower/bin/bower install && node_modules/bower/bin/bower update",
    "build": "node node_modules/gulp-cli/bin/gulp.js"
}
```


## Publish to App Store

First get an account for the [App Store](http://apps.nextcloud.com/) then run:

    make && make appstore

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

## Running tests
You can use the provided Makefile to run all tests by using:

    make test

This will run the PHP unit and integration tests and if a package.json is present in the **js/** folder will execute **npm run test**

Of course you can also install [PHPUnit](http://phpunit.de/getting-started.html) and use the configurations directly:

    phpunit -c phpunit.xml

or:

    phpunit -c phpunit.integration.xml

for integration tests


// Markdown editor:  https://simplemde.com/
// https://github.com/Ionaru/easy-markdown-editor


//---
https://docs.nextcloud.com/server/latest/developer_manual/app_development/tutorial.html

cd nextcloud
php -S localhost:8080

podman run --name=nextcloud --replace=true -p 8080:80 -v /absolute/path/to/apps:/var/www/html/custom_apps docker.io/nextcloud

sudo docker run --name=nextcloud -p 8080:80 -v /absolute/path/to/apps:/var/www/html/custom_apps nextcloud


Version000000Date20220302210900
//---
php ./occ migrations:execute <appId> <versionNumber>

Example: sudo -u www-data php ./occ migrations:execute mywiki 000000Date20220302210900

https://c.infdj.com/apps/files/?dir=/Documents/Manuals%20-%20Drivers/drivers/MAD&fileid=19227

https://docs.nextcloud.com/server/latest/developer_manual/digging_deeper/api.html

phpunit -c phpunit.integration.xml

https://github.com/nextcloud/nextcloud-vue