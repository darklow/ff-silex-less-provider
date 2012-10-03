FF-Silex-Less service provider
================

Simple less php service provider for Silex that uses https://github.com/leafo/lessphp as parser.

Simply specify paths for your .less files and target .css and if your .less files are newer than final .css file, final .css will be regenerated

Installation
------------

Create a composer.json in your projects root-directory:

    {
        "require": {
            "darklow/ff-silex-less-provider": "*"
        }
    }

and run:

    curl -s http://getcomposer.org/installer | php
    php composer.phar install


Register provider
-----------------

You must specify two required parameters and one optional:
* **less.sources** - Single path or array of paths of source - less files. Keep in mind that if .less file @import other .less files, you have to specify only main .less file
* **less.target** - Path to target .css file
* **less.target_mode** - Optionally you can specify file mode mask

``` php
<?php
use FF\ServiceProvider\LessServiceProvider;

// Register FF Silex Less service provider
$this->register(new LessServiceProvider(), array(
    'less.sources'     => array(__DIR__.'/../../Resources/less/style.less'), // specify one or serveral .less files
    'less.target'      => __DIR__.'/../../web/css/style.css', // specify .css target file
    'less.target_mode' => 0775, // Optional
));
```

License
-------

'FF-Silex-Less' is licensed under the MIT license.