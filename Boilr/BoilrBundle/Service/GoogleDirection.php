<?php

namespace Boilr\BoilrBundle\Service;

/**
 * Description of GoogleDirection
 *
 * @author unixo
 */
class GoogleDirection implements GeoDirectionInterface
{

    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    // Specifies what mode of transport to use when calculating directions
    const MODE_DRIVING = 'driving';
    const MODE_WALKING = 'walking';
    const MODE_BICYCLING = 'bicycling';

    const BASE_URL_MATRIX = "http://maps.googleapis.com/maps/api/distancematrix";
    const BASE_URL_WAYPOINT = "http://maps.googleapis.com/maps/api/directions";

    protected $format;
    protected $language;
    protected $mode;
    protected $key;

    function __construct($key)
    {
        $this->format = self::FORMAT_JSON;
        $this->language = "it";
        $this->mode = self::MODE_DRIVING;
        $this->key = $key;
    }

    protected function buildMatrixQueryString($origins, $destinations)
    {
        $url = sprintf("%s/%s?", self::BASE_URL_MATRIX, $this->format);
        $url .= sprintf("origins=%s&destinations=%s", $origins, $destinations);
        $url .= sprintf("&language=%s", $this->language);
        $url .= sprintf("&mode=%s", $this->mode);
        $url .= "&sensor=false";

        return $url;
    }

    protected function buildWaypointQueryString($origin, $destination, $waypoints = null)
    {
        $url = sprintf("%s/%s?", self::BASE_URL_WAYPOINT, $this->format);
        $url .= sprintf("origin=%s&destination=%s", $origin, $destination);
        $url .= sprintf("&language=%s", $this->language);
        $url .= sprintf("&mode=%s", $this->mode);

        if (null !== $waypoints) {
            $url .= sprintf("&waypoints=%s", $waypoints);
        }

        $url .= "&sensor=false";

        return $url;
    }

    public function getSingleDirections(GeoPosition $origin, GeoPosition $destination)
    {
        $result = $this->getMultipleDirections(array($origin), array($destination));

        $response = array();
        $response['sourceAddress'] = $result->origin_addresses[0];
        $response['destAddress'] = $result->destination_addresses[0];
        $response['length'] = $result->rows[0]->elements[0]->duration->text;

        return $response;
    }

    public function getMultipleDirections(array $origins, array $destinations)
    {
        $_origins = array();
        foreach ($origins as $geoPoint) {
            $_origins[] = sprintf("%s,%s", $geoPoint->getLatitude(), $geoPoint->getLongitude());
        }
        $src = implode('|', $_origins);

        $_destinations = array();
        foreach ($destinations as $geoPoint) {
            $_destinations[] = sprintf("%s,%s", $geoPoint->getLatitude(), $geoPoint->getLongitude());
        }
        $dst = implode('|', $_destinations);

        $url = $this->buildMatrixQueryString($src, $dst);
        $result = $this->getContents($url);
        if ($result->status !== 'OK') {
            return null;
        }

        return $result;
    }

    public function findBestRoute(GeoPosition $source, GeoPosition $destination, $waypoints = array())
    {
        // get source coordinate
        $src = sprintf("%s,%s", $source->getLatitude(), $source->getLongitude());
        // get destination coordinate
        $dst = sprintf("%s,%s", $destination->getLatitude(), $destination->getLongitude());
        // convert waypoint coordinates, if any
        $_waypoints = array();
        foreach ($waypoints as $geoPoint) {
            $_waypoints[] = sprintf("%s,%s", $geoPoint->getLatitude(), $geoPoint->getLongitude());
        }
        $constraints = (count($_waypoints) ? implode('|', $_waypoints) : null);

        $url = $this->buildWayPointQueryString($src, $dst, $constraints);
        $result = $this->getContents($url);
        if ($result->status !== 'OK') {
            return null;
        }

        $route = array($source);
        $waypointIndex = $result->routes[0]->waypoint_order;
        foreach ($waypointIndex as $index) {
            $route[] = $waypoints[$index];
        }
//        $route[] = $destination;

        return $route;
    }

    /**
     *
     *
     * @param string $url
     * @return array|null
     */
    protected function getContents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($output === false || $info != 200) {
            return null;
        }

        return json_decode($output);
    }

    /**
     * Return format of request, json and xml only are supported
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set output type
     *
     * @param string $format
     * @throws \InvalidArgumentException
     */
    public function setFormat($format)
    {
        if (!in_array($format, array(self::FORMAT_JSON, self::FORMAT_XML))) {
            throw new \InvalidArgumentException();
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Get language of result
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language of result
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Returns mode of transport used for calculation
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set mode of transport used for calculation
     *
     * @param string $mode
     * @throws \InvalidArgumentException
     */
    public function setMode($mode)
    {
        if (!in_array($mode, array(self::MODE_BICYCLING, self::MODE_DRIVING, self::MODE_WALKING))) {
            throw new \InvalidArgumentException;
        }

        $this->mode = $mode;

        return $this;
    }

}
