Meinhof
=======

[![Build Status](https://secure.travis-ci.org/miguelibero/meinhof.png?branch=master)](http://travis-ci.org/miguelibero/meinhof)

Meinhof is a minimal static html site generator, much like [jekyll](https://github.com/mojombo/jekyll).

The difference is that it is written in PHP 5.3 using [symfony components](http://symfony.com/components).

I'm trying to maintain jekyll format conventions where it makes sense.

It's currently still a work in progress.

Working
-------
* twig templates
* markdown post contents
* assetic assets
* yaml front matter (like in [jekyl](https://github.com/mojombo/jekyll/wiki/YAML-Front-Matter))
* Dependency injection and composer autoload from site config directory
* pages
* categories

TODO
----
* Add tests
* Add post archive support (pagination, calendar, etc...)
* Add documentation

Installation
------------

This is only a library, to setup your site please see [meinhof-standard](https://github.com/miguelibero/meinhof-standard).

Thanks
------

* Fabien Potencier for creating symfony
* Nils Adermann and Jordi Boggiano for creating composer and the packagist repo
* Rasmus Lerdorf for creating php
