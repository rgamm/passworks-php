PHP bindings for the Passworks API
================================================

Passworks PHP API client can be installed via [Composer](https://github.com/composer/composer) or [PHAR](http://php.net/manual/en/intro.phar.php) file. (*Composer is the recommended method for using Passworks API client*).



Installing using Composer
-----------------

The API client can be installed through Composer.
It's been made available as a [Packagist](https://packagist.org/) package, available [here](https://packagist.org/packages/passworks/passworks-php).
In order to use it, first add a file named  **composer.json** in your project root:

```javascript
{
    "require": {
        "passworks/passworks-php": "0.0.4-beta"
    }
}
```

Once the composer.json file is created, you need to download the composer.phar executable. To do so, run the following curl command on your console of choice:

```shell
curl -sS https://getcomposer.org/installer | php
```

Now you can run the composer install for the initial package install and composer update to update to the latest version of the API client, which is linked to the master branch.

```shell
php composer.phar install
```

Now you're set to go! Just include the following file and you're good to go.

```php
require 'vendor/autoload.php';
```

# Installation using the PHAR file
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

use Passworks\Client;

// Instantiate the Passworks client
$api = new Passworks\Client('your api username', 'your api key');

// upload a asset (background image)
$api->createAsset('background', '/local-path-to-a-image/image.png');

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

[Creating and Listing assets](https://github.com/passworks/passworks-php/wiki/Creating-and-Listing-assets)

[Listing certificates](https://github.com/passworks/passworks-php/wiki/Listing-certificates)

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
