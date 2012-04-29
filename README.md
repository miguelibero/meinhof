Meinhof
=======

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

TODO
----
* Add tests (!)
* Add page support
* Add category support
* Add documentation
* Add additional dependency injection from site config

Installation
------------

    $ git clone 
    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install
    $ ./bin/compile
    $ php meinhof.phar generate test/source/default

Now the generated site should be in `test/source/default/site`.

See this example for additional configuration until I write the documentation.