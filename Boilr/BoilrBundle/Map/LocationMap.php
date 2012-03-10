<?php

namespace Boilr\BoilrBundle\Map;

use Vich\GeographicalBundle\Map\Map;

/**
 * LocationMap.
 */
class LocationMap extends Map
{
    /**
     * Constructs a new instance of LocationMap.
     */
    public function __construct()
    {
        parent::__construct();

        // configure your map in the constructor
        // by setting the options

        $this->setHeight(400);
        $this->setAutoZoom(true);
        $this->setContainerId('addressMapContainer');
        $this->setContainerAttributes(array('class' => 'map embossin'));
        $this->setVarName("addressesMap");
        $this->setShowZoomControl(true);
        $this->setShowMapTypeControl(true);
        $this->setShowInfoWindowsForMarkers(true);

        //$this->addMarker(new \Vich\GeographicalBundle\Map\Marker\MapMarker(41.6298297, 12.4738096));
        //$this->addMarker(new \Vich\GeographicalBundle\Map\Marker\MapMarker(41.7403131, 12.2676940));
    }
}