PHP bindings for the Passworks API
================================================

Passworks PHP API client can be installed via [Composer](https://github.com/composer/composer) or [PHAR](http://php.net/manual/en/intro.phar.php) file.



Installing using Composer
-----------------

The API client can be installed through Composer. When a final public release is available it will be added to the [Packagist](https://packagist.org/) repository. 
For now you'll have to add the following to a composer.json file in the project root:

```javascript
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/passworks/passworks-php"
        }
    ],
    "require": {
        "passworks/passworks-php": "dev-master"
    }
}
```

Once the composer.json file is created you can run composer install for the initial package install and composer update to updated to the latest version of the API client, which is linked to the master branch.


```php
require 'vendor/autoload.php';
```

# Installation using the PHAR
------------------

Download the lastest version of [Passworks.phar](https://github.com/passworks/passworks-php/releases/latest) and require it as usual.

```php
include 'Passworks.phar';
```

Example
-----------------

```php
<?php

require 'vendor/autoload.php';

use Passworks;

// Instantiate the Passworks client
$api = new Passworks\Client('your api username', 'your api key');

// upload a asset (background image)
$api->createAsset('background', '~/images/background-image.png');

// Fetch the asset list
$assets = $api->getAssets();

// Iterate through asset list
foreach($assets as $asset)
{
  print_r($asset);
}
```

More Examples:
---------------------
[Creating, Listing and Editing Coupons](https://github.com/passworks/passworks-php/wiki/Creating,-Listing-and-Editing-Coupons) 

[Creating, Listing and Editing Event Tickets](https://github.com/passworks/passworks-php/wiki/Creating,-Listing-and-Editing-Event-Tickets) 

[Creating, Listing and Editing Boarding Passes](https://github.com/passworks/passworks-php/wiki/Creating,-Listing-and-Editing-Boarding-Passes) 

[Creating, Listing and Editing Store Cards](https://github.com/passworks/passworks-php/wiki/Creating,-Listing-and-Editing-Store-Cards) 

[Creating, Listing and Editing Generic Passes](https://github.com/passworks/passworks-php/wiki/Creating,-Listing-and-Editing-Generic-Passes)

    
Documentation
----------------------
For more information about the API please please refere to [https://github.com/passworks/passworks-api](https://github.com/passworks/passworks-api)

For more examples about the PHP client try browsing the [wiki](https://github.com/passworks/passworks-php/wiki)

Help us make it better
----------------------

Please tell us how we can make the PHP client better. If you have a specific feature request or if you found a bug, please use GitHub issues. Fork these docs and send a pull request with improvements.

To talk with us and other developers about the API [open a support ticket](https://github.com/passworks/passworks-php/issues) or mail us at `api at passworks.io` if you need to talk to us.
