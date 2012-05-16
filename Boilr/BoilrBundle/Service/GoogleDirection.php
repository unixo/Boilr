<?php

namespace Boilr\BoilrBundle\Service;

/**
 * Description of GoogleDirection
 *
 * @author unixo
 */
class GoogleDirection
{

    const BASE_URL = "http://maps.googleapis.com/maps/api/distancematrix";

    protected $format;
    protected $language;
    protected $mode;
    protected $key;

    function __construct()
    {
        $this->format = "json";
        $this->language = "en";
        $this->mode = "driving";
        $this->key = "AIzaSyBxynXehOTkCM1FpypIlrZzqZQfAwNNvFE";
    }

    public function getDirections(GeoPosition $origin, GeoPosition $destination)
    {
        $url = sprintf("%s/%s?", self::BASE_URL, $this->format);
        $url .= sprintf("origins=%s&destinations=%s", $origin, $destination);
        $url .= sprintf("&language=%s", $this->language);
        $url .= sprintf("&mode=%s", $this->mode);
        $url .= sprintf("&key=%s", $this->key);
        $url .= "&sensor=false";

        $result = $this->getContents($url);
        if ($result->status !== 'OK') {
            return null;
        }

        $response = array();
        $response['sourceAddress'] = $result->origin_addresses[0];
        $response['destAddress'] = $result->destination_addresses[0];
        $response['length'] = $result->rows[0]->elements[0]->duration->text;

        return $response;
    }

    public function getMultipleDestinations(GeoPosition $origin, array $destinations)
    {
        $url = sprintf("%s/%s?", self::BASE_URL, $this->format);
    }

    protected function getContents($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($output === false || $info != 200) {
            return null;
        }

        return json_decode($output);
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

}
