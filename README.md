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

TODO
----
* Add tests (!)
* Add page support
* Add category support
* Add translator support
* Add documentation

Installation
------------

    $ git clone git://github.com/miguelibero/meinhof.git
    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install
    $ ./bin/compile
    $ ./meinhof.phar init /tmp/meinhof_site

After answering the questions a new site configuration will be created,
the generated html files should be in the site directory

Thanks
------

Fabien Potencier for creating symfony. Nils Adermann and Jordi Boggiano for
creating composer and the packagist repo. Rasmus Lerdorf for creating php.
