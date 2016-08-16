# findcontact-ripe
findcontact module for IP lookups using the Ripe Stat Api

## Installation
    
    composer require abuseio/findcontact-ripe
     
## Use the findcontact-ripe module
copy the ```extra/config/main.php``` to the config override directory of your environment (e.g. production)

#### production

    cp vendor/abuseio/findcontact-ripe/extra/config/main.php config/production/main.php
    
#### development

    cp vendor/abuseio/findcontact-ripe/extra/config/main.php config/development/main.php
    
add the following line to providers array in the file config/app.php:

    'AbuseIO\FindContact\Ripe\RipeServiceProvider'
    
## Configuration
It is highly recommended to use an RIPEStat application id for the API requests, see [RIPEStat rules of usage](https://stat.ripe.net/docs/data_api#RulesOfUsage).
You can config it in  ````$ABUSEIOPATH/vendor/abuseio/findcontact-ripe/config````.

Replace the null value in ````'appid' => null,```` with your application id, e.g.
    
    <?php
    
    return [
        'findcontact-ripe' => [
            // it is highly recommended to use an application id in production environments
            // see https://stat.ripe.net/docs/data_api
            'appid'          => 'MyAppId,
            'enabled'        => true,
            'auto_notify'    => false,
        ],
    ];

