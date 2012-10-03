FF-Silex-Less service provider
================

Simple less php service provider for Silex that uses https://github.com/leafo/lessphp as parser.
Simply specify your .less files paths and target .css file and if your .less files are newer than final .css file, it file will be regenerated

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

You must specify two required parameters:
1) less.sources - Single path or array of paths of source - less files. Keep in mind that if .less file @import other .less files, you have to specify only main .less file
2) less.target - Path to target .css file
2) less.target - Optionally you can specify file mode mask

``` php
<?php
use FF\ServiceProvider\LessServiceProvider;

// Register FF Silex Less service provider
$this->register(new LessServiceProvider(), array(
    'less.sources'     => array(__DIR__.'../../Resources/less/style.less'), // specify one or serveral .less files
    'less.target'      => $app['root'].'web/css/style.css', // specify .css target file
    'less.target_mode' => 0775, // Optional
));
```

License
-------

'FF-Silex-Less' is licensed under the MIT license.