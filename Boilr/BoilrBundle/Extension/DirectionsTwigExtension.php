<?php

namespace Boilr\BoilrBundle\Extension;

/**
 * Description of DirectionsTwigExtension
 *
 * @author unixo
 */
class DirectionsTwigExtension extends \Twig_Extension
{

    /**
     * Google API Key for browser apps.
     *
     * @var string
     * @see https://developers.google.com/maps/documentation/directions/
     */
    protected $googleApiKey;

    function __construct($googleApiKey)
    {
        $this->googleApiKey = $googleApiKey;
    }

    public function getFunctions()
    {
        $names = array(
            'google_directions_for' => 'renderDirectionsFor',
        );

        $funcs = array();
        foreach ($names as $twig => $local) {
            $funcs[$twig] = new \Twig_Function_Method($this, $local, array('is_safe' => array('html')));
        }

        return $funcs;
    }

    public function getName()
    {
        return "boilr_twig_directions";
    }

    public function renderDirectionsFor($mapID, $origin, $destination, $lengthID)
    {
        $origin = $this->getLatLng($origin);
        $destination = $this->getLatLng($destination);

        $html = $this->renderOpenScriptTag();
        $html .= $this->renderVars($mapID, $origin, $destination, $lengthID);
        $html .= $this->renderCloseScriptTag();

        return $html;
    }

    /**
     * Gets the latitude and longitude values of the object based on annotations.
     *
     * @param type $obj The object
     * @return array An array
     */
    protected function getLatLng($obj)
    {
        $latMethod = "getLatitude"; //sprintf('get%s', $annot->getLatitude());
        $lngMethod = "getLongitude"; //sprintf('get%s', $annot->getLongitude());

        $lat = $obj->$latMethod();
        $lng = $obj->$lngMethod();

        return array($lat, $lng);
    }

    protected function renderContainer()
    {
        return '<div id="map" style="width: 100%; height: 400px"></div>';
    }

    protected function renderOpenScriptTag()
    {
        $html = sprintf('<script type="text/javascript" ' .
                'src="http://maps.googleapis.com/maps/api/js?' .
                'key=%s&amp;sensor=false"></script>', $this->googleApiKey);
        $html .= '<script type="text/javascript">';

        return $html;
    }

    protected function renderCloseScriptTag()
    {
        return '</script>';
    }

    protected function renderVars($mapID, $src, $dst, $lengthID = null)
    {
        list($oLat, $oLng) = $src;
        list($dLat, $dLng) = $dst;

        $vars = sprintf(
            "$(function () {
                var directionDisplay;
                var directionsService = new google.maps.DirectionsService();
                var start = '%s, %s';
                var end = '%s, %s';
                var request = {
                    origin:start, destination:end,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                };
                var map;

                directionsDisplay = new google.maps.DirectionsRenderer();
                var chicago = new google.maps.LatLng(41.850033, -87.6500523);
                var myOptions = {
                        zoom:7,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: chicago
                    };
                map = new google.maps.Map(document.getElementById('%s'), myOptions);
                directionsDisplay.setMap(map);
                directionsService.route(request, function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                            document.getElementById('%s').innerHTML = response.routes[0].legs[0].distance.text;
                        }
                    });
                });", $oLat, $oLng, $dLat, $dLng, $mapID, $lengthID
        );

        return $vars;
    }

}

