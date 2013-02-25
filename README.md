ShakeTheNations
===============


Summary
-------

ShakeTheNations: Latest Earthquakes Feeds Parser & Analyzer. Makes use of the [European Mediterranean Seismological Centre feeds](http://www.emsc-csem.org)

Example of parsed RSS feed: http://www.emsc-csem.org/service/rss/rss.php?typ=emsc&min_lat=10&min_long=-30&max_long=65

Quick install
-------------

    $ curl http://getcomposer.org/installer | php
    $ php composer.phar install

Usage
-----

    $ shake

Tests
-----

@TODO ShakeTheNations is BDD-tested using [Behat](http://behat.org)

    $ php bin/behat --no-snippets

@TODO ShakeTheNations is TDD-tested using [PHPUnit](https://github.com/sebastianbergmann/phpunit/)

    $ php bin/phpunit


Inspirations, sources & thanks
------------------------------

* http://archive.plugins.jquery.com/project/jFeed
* https://github.com/javiereguiluz/easybook

Where to find Sismicity information online ?
--------------------------------------------

* http://www.planseisme.fr
* http://www.seismes.fr/
* http://www.tsunamis.fr/
* http://www.afps-seisme.org/index.php/fre/Seismes/Derniers-seismes
* http://www-dase.cea.fr/


Credits
-------
* Aaron Ogle (https://github.com/atogle) for its [forked openplans/Leaflet.AnimatedMarker project](https://github.com/openplans/Leaflet.AnimatedMarker)
* Ronan Guilloux <ronan.guilloux@gmail.com>
* [All contributors](https://github.com/ronanguilloux/ShakeTheNations/contributors)

