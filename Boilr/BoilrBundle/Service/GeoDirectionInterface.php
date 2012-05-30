<?php

namespace Boilr\BoilrBundle\Service;

/**
 *
 * @author unixo
 */
interface GeoDirectionInterface
{

    public function getSingleDirections(GeoPosition $origin, GeoPosition $destination);

    public function getMultipleDirections(array $origins, array $destinations);
}
