<?php

namespace Boilr\BoilrBundle\Extension;

/**
 * Description of DirectionsTwigExtension
 *
 * @author unixo
 */
class DirectionsTwigExtension extends \Twig_Extension
{

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

    public function renderDirectionsFor($origin, $destination)
    {
        $origin = $this->getLatLng($origin);
        $destination = $this->getLatLng($destination);

        //$html  = $this->renderContainer();
        $html = $this->renderOpenScriptTag();
        $html .= $this->renderVars($origin, $destination);
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
        return '<script type="text/javascript">';
    }

    protected function renderCloseScriptTag()
    {
        return '</script>';
    }

    protected function renderVars($src, $dst)
    {
        list($oLat, $oLng) = $src;
        list($dLat, $dLng) = $dst;

        $vars = sprintf(
               "    var directionsDisplay = new google.maps.DirectionsRenderer();
                    var directionsService = new google.maps.DirectionsService();
                    var src = new google.maps.LatLng(%s, %s);
                    var dst = new google.maps.LatLng(%s, %s);
                    var request = {
                        origin: src, destination: dst,
                        travelMode: google.maps.DirectionsTravelMode['%s']
                    };
                    var myOptions = {
                    zoom: 14, mapTypeId: google.maps.MapTypeId.ROADMAP, center: src
                    }
                    var map;
                    var duration;

                (function($) {
                    map = new google.maps.Map(document.getElementById('map'), myOptions);
                    directionsDisplay.setMap(map);
                    directionsService.route(request, function(response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                            duration = response.routes[0].legs[0].duration;
                            document.getElementById('time-length').innerHTML = duration.text;
                        }
                    });
                })(jQuery);",
                $oLat, $oLng, $dLat, $dLng, "DRIVING"
        );

        return $vars;
    }

}

