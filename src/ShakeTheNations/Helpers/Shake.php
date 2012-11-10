<?php

namespace ShakeTheNations\Helpers;

use ShakeTheNations\Helpers\Geo;
use ShakeTheNations\Parsers\Parser;

class Shake
{

    const DEFAULT_DISTANCE = 500;
    const DEFAULT_NUMBER = 10;

    public static function getAround($location, $lat, $lng, $distance = SHAKE::DEFAULT_DISTANCE)
    {
        $southWest = Geo::getBoundingBoxAngle($lat, $lng, 225, $distance, 'km', true);
        $northEast = Geo::getBoundingBoxAngle($lat, $lng, 45, $distance, 'km', true);

        $rss = "http://www.emsc-csem.org/service/rss/rss.php?typ=emsc";
        $rss .= "&min_lat=" . $southWest['lat'];
        $rss .= "&min_long=" . $southWest['lng'];
        $rss .= "&max_lat=" . $northEast['lat'];
        $rss .= "&max_long=" . $northEast['lng'];
        echo  sprintf("RSS url: %s",$rss);
        $sxe = simplexml_load_string(file_get_contents($rss));
        $json = Parser::transform(file_get_contents($rss));
        //echo sprintf("JSON txt: %s",$json);
        $arr = json_decode($json);
        $events = array();
        $index = 0;
        foreach ($sxe->channel->item as $key=>$item) {
            // TODO : add distance from location
            //var_export($item);
            $distanceFromLocation = '';
            $events[$index] = $item;
            if ($index >= Shake::DEFAULT_NUMBER) {
                break;
            }
            $index++;
        }
        // TODO: sort array over distance from location
        return array(
            'location'=>$location,
            'arr' => $arr,
        );
    }
}
