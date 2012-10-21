<?php

namespace ShakeTheNations\Helpers;

class Geo
{
    const GOOGLE_GEOCODE_API = "http://maps.googleapis.com/maps/api/geocode";
    const GOOGLE_MATRIX_API = "http://maps.googleapis.com/maps/api/distancematrix";
    const GOOGLE_QUERY = "address=";
    const GOOGLE_GEOCODE_OPTIONS = "&sensor=false";
    const GOOGLE_MATRIX_OPTIONS = "&mode=driving&sensor=false";
    const GOOGLE_SESSION_QUERIESMAX = 50; // each geocode/geotravel max queries

    public static $geocodeInvalidStatus = array(
        'ZERO_RESULTS',
        'REQUEST_DENIED',
        'INVALID_REQUEST',
        'OVER_QUERY_LIMIT'
    );

    public static $matrixInvalidStatus = array(
        'INVALID_REQUEST',
        'MAX_ELEMENTS_EXCEEDED',
        'OVER_QUERY_LIMIT',
        'REQUEST_DENIED',
        'UNKNOWN_ERROR'
    );

    /**
     * Geocode
     *
     * @param string $rawAddress : complete address to resolve as lat/long
     * @param string $provider : the API to ask, google by default
     * @param string $output : output format, xml by default
     * @return array('anwser'=>array(lat,lng), 'status'=>OK/BAD)
     */
    public function geocode($rawAddress, $provider = 'google', $output = 'xml')
    {
        $rawAddress = $this->prepareForUrlApiCalls($rawAddress);
        $result = false;
        switch($provider){
            // other possible provider : Yahoo place
        default:
            $url = static::GOOGLE_GEOCODE_API . "/$output?" . static::GOOGLE_QUERY;
            $url .= $rawAddress . static::GOOGLE_GEOCODE_OPTIONS;
            $url .= '&channel=geocode';
            $result = $this->askGoogleGeocode(self::signUrl($url), $output);
            break;
        }

        return $result;
    }

    public function geotravel($origin, $destinations = array(),
        $provider = 'google', $output = 'xml')
    {
        $result = false;
        $separator = '|';
        $untouchedOrigin = $origin;


        // By packet of 20 units
        $destinationsQueries = array();
        $destinationsQuery = '';
        foreach($destinations as $index => $destination) {
            if($index % 20 == 0) {
                if ($destinationsQuery) {
                    $destinationsQueries[] = $destinationsQuery;
                    $destinationsQuery = '';
                }
            }
            $destination = $destination['lat'] . ','  . $destination['lng'];
            $destinationsQuery .= $this->prepareForUrlApiCalls($destination) . $separator;
        }
        if ($destinationsQuery) {
            $destinationsQueries[] = $destinationsQuery . $separator;
            $destinationsQuery = '';
        }

        switch($provider){
            // other possible provider : Yahoo place
        default:
            $urls = array();
            foreach($destinationsQueries as $destinationsQuery)
            {
                $url = static::GOOGLE_MATRIX_API . "/$output?";
                $url .= "origins=" . $this->prepareForUrlApiCalls($origin);
                $url .= "&destinations=$destinationsQuery";
                $url .= static::GOOGLE_MATRIX_OPTIONS;
                $url .= '&client=' . self::ATLANTIC_ID;
                $url .= '&channel=geotravel';
                $urls[] = self::signUrl($url);
            }
            $result = $this->askGoogleDistance($urls, $output);
            break;
        }
        //var_dump($result);
        return $result;
    }

    protected function prepareForUrlApiCalls($str)
    {
        // convert ALL whitespace to a single space,
        // including lone \t and \n - see http://goo.gl/ZlD0
        //$rawAddress = urlencode($rawAddress);
        $str = preg_replace("'\s+'", ' ', $str);

        // convert ALL whitespace to a '+', since google map api need valid url
        return str_replace(' ', '+', strip_tags($str));
    }


    /**
     * Ask a remote API & return a ['result'=>foo, 'status'=>bar] array
     *
     * @param string $url : complete URL to be cUrled
     * @param string $output : output format, xml by default
     * @return array('answer'=>foo, 'status'=>bar)
     */
    protected function askGoogleGeocode($url, $output = 'xml')
    {
        $status = false;
        $answer = array();

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $content = trim(curl_exec($c));
        curl_close($c);
        switch($output){
            // other possible way : json
        default: // 'xml'
            $content = simplexml_load_string($content);
            // raw string typecasting required on SXE object
            $status = (string)$content->status;
            if(!empty($content->result->geometry->location)){
                $answer = (array)$content->result->geometry->location;
                //$answer = $content->result;
            }
            break;
        }

        return array(
            'url' => $url,
            'answer'=>$answer,
            'status'=>$status
        );
    }

    /**
     * Ask a remote API & return a ['result'=>foo, 'status'=>bar] array
     * Ex : http://goo.gl/3Mf35
     *
     * @param string/array $urls : complete URL to be cUrled
     * @param string $output : output format, xml by default
     * @return array('answer'=>foo, 'status'=>bar)
     */
    protected function askGoogleDistance($urls, $output = 'xml')
    {
        $status = false;

        if (is_string($urls)) $urls = array($urls);

        $answers = array();

        foreach($urls as $url)
        {

            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);

            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            $content = trim(curl_exec($c));
            curl_close($c);
            switch($output){
                // other possible way : json
            default: // 'xml'
                $content = @simplexml_load_string($content);
                // raw string typecasting required on SXE object
                $status = (string)$content->status;
                if(!empty($content->row)){
                    $answers[] = $content->row;
                    break;
                }

            }
        }


        return array(
            'url' => '',
            'answer'=>$answers,
            'status'=>true // WTF ???
        );
    }

    /**
     * add signature (cf http://gmaps-samples.googlecode.com/svn/trunk/urlsigning/UrlSigner.php-source)
     *
     * @param string $myUrlToSign url complète
     * @return string nouvelle url avec signature
     */
    public static function signUrl($urlToSign)
    {
        // add your own private signature here if you use the Business version of Google Map API
        return $urlToSign;
    }

    /**
    Get a boundingBox angle from a lat;long tuple, a bearing angle & a distance in km/miles

    From richardpeacock.com

    $lat = 47.2103350;
    $lng = -1.6514440;

    // Example: Create the static map api image.
    $static_maps_url = "http://maps.googleapis.com/maps/api/staticmap";
    $static_maps_url .= "?center=$lat,$lng";
    $static_maps_url .= "&zoom=5";
    $static_maps_url .= "&size=300x300";
    $static_maps_url .= "&maptype=roadmap";
    $static_maps_url .= "&sensor=false";
    $static_maps_url .= "&markers=color:blue|$lat,$lng";

    // Figure out the corners of a box surrounding our lat/lng.
    $d = 500;  // distance
    $output->writeln(array(
            '',
            ' Welcome to the ShakeTheNations interactive cli-tool',
            ''
        ));
    // losange
    $path_top_right = getBoundingBoxAngle($lat, $lng, 0, $d);
    $path_bottom_right = getBoundingBoxAngle($lat, $lng, 90, $d);
    $path_bottom_left = getBoundingBoxAngle($lat, $lng, 180, $d);
    $path_top_left = getBoundingBoxAngle($lat, $lng, 270, $d);

    // square
    $path_top_right = getBoundingBoxAngle($lat, $lng, 45, $d);
    $path_bottom_right = getBoundingBoxAngle($lat, $lng, 135, $d);
    $path_bottom_left = getBoundingBoxAngle($lat, $lng, 225, $d);
    $path_top_left = getBoundingBoxAngle($lat, $lng, 315, $d);

    echo "<br /> path_top_right : <a href='https://maps.google.com/maps?f=q&q=$path_top_right&z=5'>$path_top_right</a>";
    echo "<br /> path_bottom_right : <a href='https://maps.google.com/maps?f=q&q=$path_bottom_right&z=5'>$path_bottom_right</a>";
    echo "<br /> path_bottom_left : <a href='https://maps.google.com/maps?f=q&q=$path_bottom_left&z=5'>$path_bottom_left</a>";
    echo "<br /> path_top_left : <a href='https://maps.google.com/maps?f=q&q=$path_top_left&z=5'>$path_top_left</a>";
    echo "<hr />";

    $static_maps_url .= "&path=color:334433|weight:5|fillcolor:0xFFFF0033|";
    $static_maps_url .= "$path_top_left|$path_top_right|$path_bottom_right|";
    $static_maps_url .= "$path_bottom_left|$path_top_left";

    // Now, draw the image from Google Maps API!
    print "<img src='$static_maps_url'>";

    Modified from:
    http://www.sitepoint.com/forums/showthread.php?656315-adding-distance-gps-coordinates-get-bounding-box
    bearing is 0 = north, 180 = south, 90 = east, 270 = west
 */
  public static function getBoundingBoxAngle($latitude, $longitude, $bearing, $distance, $distance_unit = "km", $return_as_array = FALSE) {

    if ($distance_unit == "m") {
      // Distance is in miles.
		  $radius = 3963.1676;
    }
    else {
      // distance is in km.
      $radius = 6378.1;
    }

    //	New latitude in degrees.
    $new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $radius) + cos(deg2rad($latitude)) * sin($distance / $radius) * cos(deg2rad($bearing))));

    //	New longitude in degrees.
    $new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $radius) * cos(deg2rad($latitude)), cos($distance / $radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));

    if ($return_as_array) {
      //  Assign new latitude and longitude to an array to be returned to the caller.
      $coord = array();
      $coord['lat'] = $new_latitude;
      $coord['lng'] = $new_longitude;
      foreach ($coord as $key=> $pos) {
          $coord[$key] = str_replace(',','.',$pos); // dotted floating value
      }

    }
    else {
      $coord = $new_latitude . "," . $new_longitude;
    }

    return $coord;

  }


    /**
     * getBoundingBox
     *
     * given a latitude and longitude in degrees (40.123123,-72.234234) and a distance in kilometers
     * calculates a bounding box with corners $distance_in_kilometers away from the point specified.
     *
     * hacked out by ben brown <ben@xoxco.com>
     *
     * @example list($lat1,$lat2,$lon1,$lon2) = getBoundingBox(47.2183710,-1.5536210,500);
     * @link http://xoxco.com/clickable/php-getboundingbox
     *
     * param float $lat_degrees
     * param float $lon_degrees
     * param float $distance_in_kilometers
     * @return array($min_lat,$max_lat,$min_lon,$max_lon)
     */
    public static function getBoundingBox($lat_degrees,$lon_degrees,$distance_in_kilometers) {

        $radius = 6378.1 ; // of earth in kilometers

        // bearings
        $due_north = 0;
        $due_south = 180;
        $due_east = 90;
        $due_west = 270;

        // convert latitude and longitude into radians
        $lat_r = deg2rad($lat_degrees);
        $lon_r = deg2rad($lon_degrees);

        // find the northmost, southmost, eastmost and westmost corners $distance_in_kilometers away
        // original formula from
        // http://www.movable-type.co.uk/scripts/latlong.html

        $northmost  = asin(sin($lat_r) * cos($distance_in_kilometers/$radius) + cos($lat_r) * sin ($distance_in_kilometers/$radius) * cos($due_north));
        $southmost  = asin(sin($lat_r) * cos($distance_in_kilometers/$radius) + cos($lat_r) * sin ($distance_in_kilometers/$radius) * cos($due_south));

        $eastmost = $lon_r + atan2(sin($due_east)*sin($distance_in_kilometers/$radius)*cos($lat_r),cos($distance_in_kilometers/$radius)-sin($lat_r)*sin($lat_r));
        $westmost = $lon_r + atan2(sin($due_west)*sin($distance_in_kilometers/$radius)*cos($lat_r),cos($distance_in_kilometers/$radius)-sin($lat_r)*sin($lat_r));


        $northmost = rad2deg($northmost);
        $southmost = rad2deg($southmost);
        $eastmost = rad2deg($eastmost);
        $westmost = rad2deg($westmost);

        // sort the lat and long so that we can use them for a between query
        if ($northmost > $southmost) {
            $lat1 = $southmost;
            $lat2 = $northmost;

        } else {
            $lat1 = $northmost;
            $lat2 = $southmost;
        }


        if ($eastmost > $westmost) {
            $lon1 = $westmost;
            $lon2 = $eastmost;

        } else {
            $lon1 = $eastmost;
            $lon2 = $westmost;
        }

        $bbox = array($lat1,$lat2,$lon1,$lon2);
        foreach ($bbox as $key=> $coord) {
            $bbox[$key] = str_replace(',','.',$coord);
        }
        return $bbox;
    }

    /**
     * source : http://php.net/manual/fr/book.simplexml.php
     * Converts a simpleXML element into an array. Preserves attributes.<br/>
     * You can choose to get your elements either flattened, or stored in a custom
     * index that you define.<br/>
     * For example, for a given element
     * <code>
     * <field name="someName" type="someType"/>
     * </code>
     * <br>
     * if you choose to flatten attributes, you would get:
     * <code>
     * $array['field']['name'] = 'someName';
     * $array['field']['type'] = 'someType';
     * </code>
     * If you choose not to flatten, you get:
     * <code>
     * $array['field']['@attributes']['name'] = 'someName';
     * </code>
     * <br>__________________________________________________________<br>
     * Repeating fields are stored in indexed arrays. so for a markup such as:
     * <code>
     * <parent>
     *     <child>a</child>
     *     <child>b</child>
     *     <child>c</child>
     * ...
     * </code>
     * you array would be:
     * <code>
     * $array['parent']['child'][0] = 'a';
     * $array['parent']['child'][1] = 'b';
     * ...And so on.
     * </code>
     * @param simpleXMLElement    $xml            the XML to convert
     * @param boolean|string    $attributesKey    if you pass TRUE, all values will be
     *                                            stored under an '@attributes' index.
     *                                            Note that you can also pass a string
     *                                            to change the default index.<br/>
     *                                            defaults to null.
     * @param boolean|string    $childrenKey    if you pass TRUE, all values will be
     *                                            stored under an '@children' index.
     *                                            Note that you can also pass a string
     *                                            to change the default index.<br/>
     *                                            defaults to null.
     * @param boolean|string    $valueKey        if you pass TRUE, all values will be
     *                                            stored under an '@values' index. Note
     *                                            that you can also pass a string to
     *                                            change the default index.<br/>
     *                                            defaults to null.
     * @return array the resulting array.
     */
    public static function simpleXMLToArray(SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null){

        if($childrenKey && !is_string($childrenKey)){$childrenKey = '@children';}
            if($attributesKey && !is_string($attributesKey)){$attributesKey = '@attributes';}
                if($valueKey && !is_string($valueKey)){$valueKey = '@values';}

                    $return = array();
        $name = $xml->getName();
        $_value = trim((string)$xml);
        if(!strlen($_value)){$_value = null;};

        if($_value!==null){
            if($valueKey){$return[$valueKey] = $_value;}
            else{$return = $_value;}
        }

        $children = array();
        $first = true;
        foreach($xml->children() as $elementName => $child){
            $value = self::simpleXMLToArray($child,$attributesKey, $childrenKey,$valueKey);
            if(isset($children[$elementName])){
                if(is_array($children[$elementName])){
                    if($first){
                        $temp = $children[$elementName];
                        unset($children[$elementName]);
                        $children[$elementName][] = $temp;
                        $first=false;
                    }
                    $children[$elementName][] = $value;
                }else{
                    $children[$elementName] = array($children[$elementName],$value);
                }
            }
            else{
                $children[$elementName] = $value;
            }
        }
        if($children){
            if($childrenKey){$return[$childrenKey] = $children;}
            else{$return = array_merge($return,$children);}
        }

        $attributes = array();
        foreach($xml->attributes() as $name=>$value){
            $attributes[$name] = trim($value);
        }
        if($attributes){
            if($attributesKey){$return[$attributesKey] = $attributes;}
            else{$return = array_merge($return, $attributes);}
        }

        return $return;
    }

}

?>
