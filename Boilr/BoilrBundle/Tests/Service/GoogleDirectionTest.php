<?php

namespace Boilr\BoilrBundle\Tests\Service;

use Boilr\BoilrBundle\Tests\KernelAwareTest,
    Boilr\BoilrBundle\Service\GeoPosition,
    Boilr\BoilrBundle\Service\GoogleDirection;

class GoogleDirectionTest extends KernelAwareTest
{

    /**
     * @var \Boilr\BoilrBundle\Service\GoogleDirection
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->service = $this->container->get('google_direction');
    }

    public function testDirection()
    {
        $origin = new GeoPosition(41.895592, 12.514301);
        $destination = new GeoPosition(41.669318, 12.502332);

        $result = $this->service->getSingleDirections($origin, $destination);

        $this->assertTrue(is_array($result));
        $this->assertCount(3, $result);
    }

    public function testWaypoints()
    {
        $origin = new GeoPosition(41.895592, 12.514301);
        $destination = new GeoPosition(41.621699, 12.461698);
        $waypoints[] = new GeoPosition(41.669318, 12.502332);
        $waypoints[] = new GeoPosition(41.629914, 12.474227);

        $result = $this->service->findBestRoute($origin, $destination, $waypoints);

        $this->assertTrue(is_array($result));
        $this->assertCount(3, $result);
    }

    public function testMultipleDirections()
    {
        $origins[] = new GeoPosition(41.895592, 12.514301);
        $origins[] = new GeoPosition(41.621699, 12.461698);
        $destinations[] = new GeoPosition(41.669318, 12.502332);
        $destinations[] = new GeoPosition(41.629914, 12.474227);

        $result = $this->service->getMultipleDirections($origins, $destinations);

        $this->assertTrue(is_object($result));
        $this->assertCount(2, $result->rows);
        $this->assertEquals("OK", $result->status);
    }

    public function testOutputFormat()
    {
        $oldFormat = $this->service->getFormat();

        $xmlFormat = $this->service->setFormat(GoogleDirection::FORMAT_XML)->getFormat();
        $this->assertEquals(GoogleDirection::FORMAT_XML, $xmlFormat);
        $xmlFormat = $this->service->setFormat(GoogleDirection::FORMAT_JSON)->getFormat();
        $this->assertEquals(GoogleDirection::FORMAT_JSON, $xmlFormat);

        $this->service->setFormat($oldFormat);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidOutputFormat()
    {
        $this->service->setFormat("NOT-EXIST");
    }

    public function testMode()
    {
        $oldMode = $this->service->getMode();

        $aMode = $this->service->setMode(GoogleDirection::MODE_DRIVING)->getMode();
        $this->assertEquals(GoogleDirection::MODE_DRIVING, $aMode);
        $aMode = $this->service->setMode(GoogleDirection::MODE_BICYCLING)->getMode();
        $this->assertEquals(GoogleDirection::MODE_BICYCLING, $aMode);
        $aMode = $this->service->setMode(GoogleDirection::MODE_WALKING)->getMode();
        $this->assertEquals(GoogleDirection::MODE_WALKING, $aMode);

        $this->service->setMode($oldMode);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMode()
    {
        $this->service->setMode("NOT-EXIST");
    }

    public function testGeoPosition()
    {
        $aPos = new GeoPosition();

        $aValue = $aPos->setLatitude(0.0)->getLatitude();
        $this->assertEquals(0.0, $aValue);

        $aValue = $aPos->setLongitude(0.0)->getLongitude();
        $this->assertEquals(0.0, $aValue);
    }

}