<?php

namespace Boilr\BoilrBundle\Service;

/**
 *
 * @author unixo
 */
interface GeoDirectionInterface
{
    public function getDirections(GeoPosition $origins, GeoPosition $destinations);
}
