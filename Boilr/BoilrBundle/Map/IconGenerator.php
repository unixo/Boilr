<?php

namespace Boilr\BoilrBundle\Map;

use Boilr\BoilrBundle\Entity\Address as MyAddress;

use Vich\GeographicalBundle\Map\Marker\Icon\IconGeneratorInterface;

/**
 * IconGenerator.
 */
class IconGenerator implements IconGeneratorInterface
{
    static private $type = array(
            MyAddress::TYPE_HOME   => 'C.png',
            MyAddress::TYPE_OFFICE => 'U.png',
            MyAddress::TYPE_OTHER  => 'A.png'
    );

    /**
     * {@inheritDoc}
     */
    public function generateIcon($obj)
    {
        if ($obj instanceof MyAddress) {
            $img = self::$type[$obj->getType()];

            return "/bundles/boilr/img/".$img;
        }

        return null;
    }
}
