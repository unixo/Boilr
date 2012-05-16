<?php

namespace Boilr\BoilrBundle\Service;

/**
 * Description of GeoPosition
 *
 * @author unixo
 */
class GeoPosition
{
    protected $latitude;

    protected $longitude;

    function __construct($latitude = null, $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return sprintf("%s,%s", $this->latitude, $this->longitude);
    }

}
