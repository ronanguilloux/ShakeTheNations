<?php

namespace ShakeTheNations\Feeders;

use ShakeTheNations\DependencyInjection\Application;

/**
 * Interface implemented by content feeder classes.
 */
interface FeederInterface
{

    /**
     * Fetch remote content into the appropriate content
     *
     * @param array $args
     * @return void
     */
    public function fetch($args = array())
    {

    }
}

