<?php

namespace ShakeTheNations\Helpers;

use ShakeTheNations\Helpers\Geo;

/**
 * Groups several validators used across the application.
 */
class Validator
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Validates that the given $value is not an empty string.
     */
    public static function validateNonEmptyString($name, $value)
    {
        if (null == $value || '' == trim($value)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: The $name cannot be empty.");
        }

        return $value;
    }

    public static function validateDirExistsAndWritable($dir)
    {
        if (null == $dir || '' == trim($dir)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: The directory cannot be empty.");
        }

        if (!is_dir($dir)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: '$dir' directory doesn't exist.");
        }

        if (!is_writable($dir)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException("ERROR: '$dir' directory is not writable.");
        }

        return $dir;
    }

    /**
     * Validates that the given $slug is a valid string for a slug.
     */
    public static function validateSlug($slug)
    {
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $slug)) {
            // it throws an exception for invalid values because it's used in console commands
            throw new \InvalidArgumentException(
                "ERROR: The slug can only contain letters, numbers and dashes (no spaces)"
            );
        }

        return $slug;
    }

    /**
     * Validates that the given location is geolocalizable
     * @TODO
     */
    public function validateGeocodable($location)
    {
        $geo = new Geo();
        $geocoded = $geo->geocode($location);
        if ("OK" !== $geocoded['status']) { // Cf. Status values in API response
            throw new \InvalidArgumentException("ERROR: The $location cannot be empty.");
        }

        return $geocoded;
    }

}
