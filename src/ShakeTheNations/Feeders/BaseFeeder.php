<?php

namespace ShakeTheNations\Feeders;

use ShakeTheNations\DependencyInjection\Application;
use ShakeTheNations\Feeders\FeederInterface;
use Geocoder\Result\Geocoded;
use GeocoderToolkit\Geometry\BoundingBoxGeometry;

abstract class BaseFeeder implements FeederInterface
{
    const DEFAULT_UNIT = "km"; // kilometers
    const DEFAULT_DISTANCE = 500; // 500 km
    const DEFAULT_LIMIT = 10; // fetched items

    protected $application;

    protected $southWest;
    protected $northEast;
    protected $minLat;
    protected $minLng;
    protected $maxLat;
    protected $maxLng;

    public function __construct($app)
    {
        if (empty($app)) {
            throw new \InvalidArgumentException(" mandatory app parameter is missing!");
        }
        $this->app = $app;
    }

    public function fetch($location, $lat, $lng, $distance = self::DEFAULT_DISTANCE, $unit= self::DEFAULT_UNIT)
    {
        throw new \Exception(__CLASS__ . " base class does not implements fetch action: use real feeder classes instead.");
    }

    public function defineBoundingBox($lat, $lng, $distance = self::DEFAULT_DISTANCE, $unit = self::DEFAULT_UNIT)
    {
        // the origin
        $geocoded = new Geocoded();
        $geocoded->fromArray(array('latitude'=>$lat, 'longitude'=>$lng));

        // the boundingbox angles
        $this->southWest = BoundingBoxGeometry::getAngle($geocoded, 225, $distance, $unit);
        $this->northEast = BoundingBoxGeometry::getAngle($geocoded, 45, $distance, $unit);

        // the min/max coords
        $this->minLat= $this->southWest['lat'];
        $this->minLng= $this->southWest['lng'];
        $this->maxLat= $this->northEast['lat'];
        $this->maxLng= $this->northEast['lng'];

        return $this;
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
