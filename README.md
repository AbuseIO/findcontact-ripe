# findcontact-ripe
findcontact module for IP lookups in the Ripe DB

## Installation
    
    composer require abuseio/findcontact-ripe
     
## Use the findcontact-ripe module
copy the ```extra/config/main.php``` to the config override directory of your environment (eg. production)

#### production

    cp vendor/abuseio/findcontact-ripe/extra/config/main.php config/production/main.php
    
#### development

    cp vendor/abuseio/findcontact-ripe/extra/config/main.php config/development/main.php
    
add the following line to providers array in the file config/app.php:

    'AbuseIO\FindContact\Ripe\RipeServiceProvider'
    

