<?php

namespace ShakeTheNations\Feeders;

use ShakeTheNations\Helpers\Geo;
use ShakeTheNations\Feeders\BaseFeeder;
use ShakeTheNations\Feeders\FeederInterface;
use ShakeTheNations\Parsers\Parser;

class EMSCFeeder extends BaseFeeder implements FeederInterface
{

    const DEFAULT_UNIT = "km"; // kilometers
    const DEFAULT_DISTANCE = 500; // 500 km
    const DEFAULT_LIMIT = 10; // fetched items

    public static function fetch($location, $lat, $lng, $distance = SHAKE::DEFAULT_DISTANCE)
    {
        $southWest = Geo::getBoundingBoxAngle($lat, $lng, 225, $distance, 'km', true);
        $northEast = Geo::getBoundingBoxAngle($lat, $lng, 45, $distance, 'km', true);

        $rss = "http://www.emsc-csem.org/service/rss/rss.php?typ=emsc";
        $rss .= "&min_lat=" . $southWest['lat'];
        $rss .= "&min_long=" . $southWest['lng'];
        $rss .= "&max_lat=" . $northEast['lat'];
        $rss .= "&max_long=" . $northEast['lng'];
        //echo  sprintf("RSS url: %s",$rss);
        $sxe = simplexml_load_string(file_get_contents($rss));
        $json = Parser::transform(file_get_contents($rss));
        //echo sprintf("JSON txt: %s",$json);
        $arr = json_decode($json);
        $events = array();
        $index = 0;
        foreach ($sxe->channel->item as $key=>$item) {
            // TODO : add distance from location
            var_export($item);
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
