<?php


namespace ShakeTheNations\DependencyInjection;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

use ShakeTheNations\Parsers\Parser;

class Application extends \Pimple
{
    const VERSION = '0.1-DEV';

    public function __construct()
    {
        $app = $this;

        // -- global generic parameters ---------------------------------------
        $this['app.debug']     = false;
        $this['app.charset']   = 'UTF-8';
        $this['app.name']      = 'shakethenations';
        $this['app.signature']      = 'Shake The Nations!';
        $this['app.feed']      =' http://www.emsc-csem.org/service/rss/rss.php?typ=emsc&min_lat=10&min_long=-30&max_long=65';

        // -- global directories location -------------------------------------
        $this['app.dir.base']         = realpath(__DIR__.'/../../../');
        $this['app.dir.cache']        = $this['app.dir.base'].'/app/Cache';
        $this['app.dir.doc']          = $this['app.dir.base'].'/doc';
        $this['app.dir.resources']    = $this['app.dir.base'].'/app/Resources';
        $this['app.dir.translations'] = $this['app.dir.resources'].'/Translations';

        // -- console ---------------------------------------------------------
        $this['console.input']  = null;
        $this['console.output'] = null;
        $this['console.dialog'] = null;

        // -- timer -----------------------------------------------------------
        $this['app.timer.start']  = 0.0;
        $this['app.timer.finish'] = 0.0;



        // -- event dispatcher ------------------------------------------------
        $this['dispatcher'] = $this->share(function () {
            return new EventDispatcher();
        });

        // -- finder ----------------------------------------------------------
        $this['finder'] = function () {
            return new Finder();
        };

        // -- filesystem ------------------------------------------------------
        $this['filesystem'] = $this->share(function ($app) {
            return new Filesystem();
        });

        // -- configurator ----------------------------------------------------
        $this['configurator'] = $this->share(function ($app) {
            return new Configurator($app);
        });


        $this['parser'] = $this->share(function ($app) {
            return new Parser($app);
        });


        // -- slugger ---------------------------------------------------------
        $this['slugger'] = $app->share(function () use ($app) {
            return new Slugger($app);
        });

    }

    public final function getVersion()
    {
        return static::VERSION;
    }

    public function get($id)
    {
        return $this->offsetGet($id);
    }

    public function set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    public function append($id, $value)
    {
        $array = $this->get($id);
        $array[] = $value;
        $this->set($id, $array);

        return $array;
    }


    /**
     * Shortcut method to dispatch events.
     *
     * @param string $eventName   The name of the dispatched event
     * @param mixed  $eventObject The object that stores event data
     */
    public function dispatch($eventName, $eventObject = null)
    {
        $this->get('dispatcher')->dispatch($eventName, $eventObject);
    }
}
