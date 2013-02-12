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
     * @param string $location
     * @param float $lat
     * @param float $lng
     * @param float $distance
     * @param string $unit
     * @return void
     */
    public function fetch($location, $lat, $lng, $distance = self::DEFAULT_DISTANCE, $unit= self::DEFAULT_UNIT);


    /**
     * defineBoundingBox : define southWest/northEast limits for a square bounding box
     *
     * @param float $lat
     * @param float $lng
     * @param float $distance
     * @param string $unit : 'km' or 'miles'
     * @return BaseFeeder $this
     */
    public function defineBoundingBox($lat, $lng, $distance = self::DEFAULT_DISTANCE, $unit = self::DEFAULT_UNIT);

}

