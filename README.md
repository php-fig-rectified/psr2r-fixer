# PSR-2-R Fixer
[![Build Status](https://api.travis-ci.org/php-fig-rectified/psr2r-fixer.svg)](https://travis-ci.org/php-fig-rectified/psr2r-fixer)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/fig-r/psr2r-fixer/license.svg)](https://packagist.org/packages/fig-r/psr2r-fixer)
[![Total Downloads](https://poser.pugx.org/fig-r/psr2r-fixer/d/total.svg)](https://packagist.org/packages/fig-r/psr2r-fixer)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-yellow.svg)](https://github.com/php-fig-rectified/fig-rectified-standards)

For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

Full documentation @ [/docs/](docs).

## Deprecated
This tool is not used/maintained anymore. Please use the [sniffer](https://github.com/php-fig-rectified/psr2r-sniffer) which can both detect and fix cs issues as well as tokenize PHP files.


## PHP-CS-FIXER Fixers

This uses [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).
It can detect and fix some issues automatically and is ideal for integration into the IDE.

### Configuration
You can use PSR-2-R by default.
Create your own `.phpcs` configuration in the root directory of your project.
You can copy-and-paste this one and adjust the path to the psr2r standard definition:
```php
$finder = Symfony\CS\Finder\DefaultFinder::create()
	->in(__DIR__)
	->exclude('bin')
	->exclude('vendor') // add anything you want to omit
;

return require_once('vendor/fig-r/psr2r-fixer/.php_cs_psr2r');
```

In case you want to further modify it assign it to a variable and then you
can continue to work on the returned object, before finally returning it.

### Usage
You can now run it from your root directory as
```
vendor/bin/php-cs-fixer fix
```
It will automatically pull your config (confirm that by looking for that info in the first line when starting the fixer).

Of course you can also use any of the custom fixers in this repo in your config, or extend/modify them as you like.

```php
...
->finder($finder)
->addCustomFixer(new \Psr2rFixer\Fixer\NoSpacesCastFixer())
...
```

## License
MIT
