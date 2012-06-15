<?php

namespace Boilr\BoilrBundle\Tests\Service;

use Boilr\BoilrBundle\Tests\KernelAwareTest,
    Boilr\BoilrBundle\Service\GeoPosition;

class GoogleDirectionTest extends KernelAwareTest
{
    public function testDirection()
    {
        $service = $this->container->get('google_direction');

        $origin = new GeoPosition(41.895592, 12.514301);
        $destination = new GeoPosition(41.669318, 12.502332);

        $result = $service->getSingleDirections($origin, $destination);

        $this->assertTrue(is_array($result));
        $this->assertCount(3, $result);
    }

    public function testWaypoints()
    {
        $service = $this->container->get('google_direction');

        $origin = new GeoPosition(41.895592, 12.514301);
        $destination = new GeoPosition(41.621699, 12.461698);
        $waypoints[] = new GeoPosition(41.669318, 12.502332);
        $waypoints[] = new GeoPosition(41.629914, 12.474227);

        $result = $service->findBestRoute($origin, $destination, $waypoints);

        $this->assertTrue(is_array($result));
        $this->assertCount(3, $result);
    }

    public function testMultipleDirections()
    {
        $service = $this->container->get('google_direction');

        $origins[] = new GeoPosition(41.895592, 12.514301);
        $origins[] = new GeoPosition(41.621699, 12.461698);
        $destinations[] = new GeoPosition(41.669318, 12.502332);
        $destinations[] = new GeoPosition(41.629914, 12.474227);

        $result = $service->getMultipleDirections($origins, $destinations);

        $this->assertTrue(is_object($result));
        $this->assertCount(2, $result->rows);
        $this->assertEquals("OK", $result->status);
    }
}