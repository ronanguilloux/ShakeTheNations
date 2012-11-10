<?php

namespace ShakeTheNations\Feeders;

use ShakeTheNations\DependencyInjection\Application;
use ShakeTheNations\Feeders\FeederInterface;

abstract class BaseFeeder implements FeederInterface
{
    const DEFAULT_UNIT = "km"; // kilometers
    const DEFAULT_DISTANCE = 500; // 500 km
    const DEFAULT_LIMIT = 10; // fetched items
    protected $application;

    public function __construct($app = null)
    {
        if(!empty($app)) {
            $this->setApplication($app);
        }
    }

    /**
     * Sets the application instance for this feeder.
     *
     * @retddurn Application An Application instance
     */
    public function setApplication($app)
    {
         $this->application = $app;
    }

    /**
     * Gets the application instance for this feeder.
     *
     * @return Application An Application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

}
