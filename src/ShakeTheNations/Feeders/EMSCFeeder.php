<?php

namespace ShakeTheNations\Feeders;

use ShakeTheNations\Helpers\Geo;
use ShakeTheNations\Feeders\BaseFeeder;
use ShakeTheNations\Feeders\FeederInterface;
use ShakeTheNations\Parsers\Parser;
use ShakeTheNations\Helpers\Shake;

class EMSCFeeder extends BaseFeeder implements FeederInterface
{

    public function fetch($location, $lat, $lng,
        $distance = self::DEFAULT_DISTANCE, $unit= self::DEFAULT_UNIT)
    {
        $this->defineBoundingBox($lat, $lng, $distance, $unit);

        $rss = "http://www.emsc-csem.org/service/rss/rss.php?typ=emsc";
        $rss .= "&min_lat=" . $this->minLat;
        $rss .= "&min_long=" . $this->minLng;
        $rss .= "&max_lat=" . $this->maxLat;
        $rss .= "&max_long=" . $this->maxLng;
        $sxe = simplexml_load_string(file_get_contents($rss));
        $json = Parser::transform(file_get_contents($rss));
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
