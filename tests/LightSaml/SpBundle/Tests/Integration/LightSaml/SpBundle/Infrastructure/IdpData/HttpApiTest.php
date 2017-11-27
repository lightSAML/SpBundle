<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Integration\LightSaml\SpBundle\Infrasctrucure\IdpData;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use LightSaml\SpBundle\Infrastructure\IdpData\HttpApi;
use LightSaml\SpBundle\Tests\Integration\ContainerAwareTestCase;

class HttpApiTest extends ContainerAwareTestCase
{
    use HttpMockTrait;

    /** @var HttpApi */
    private $httpApi;

    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass(9092, "localhost");
    }

    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpHttpMock();

        $this->httpApi = $this->getContainer()->get('lightsaml_sp.idp_data_api.http');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->tearDownHttpMock();
    }

    /** @test */
    public function itGetsMetadata()
    {
        $responseBody = 'some xml file';
        $this->apiResponds(200, $responseBody);

        $response = $this->httpApi->getMetadata('default');

        $this->thenRequestUrlEquals(
            'http://localhost:9092/organisation/default/metadata'
        );
        $this->thenRequestMethodEquals('GET');
        $this->thenResponseBodyEquals($response, $responseBody);
    }

    /** @test */
    public function itGetsCertificate()
    {
        $responseBody = 'some certificate :)';
        $this->apiResponds(200, $responseBody);

        $response = $this->httpApi->getCertificate('default');

        $this->thenRequestUrlEquals(
            'http://localhost:9092/organisation/default/certificate'
        );
        $this->thenRequestMethodEquals('GET');
        $this->thenResponseBodyEquals($response, $responseBody);
    }

    /** @test */
    public function itGetsPrivateKey()
    {
        $responseBody = 'some private key :)';
        $this->apiResponds(200, $responseBody);

        $response = $this->httpApi->getPrivateKey('default');

        $this->thenRequestUrlEquals(
            'http://localhost:9092/organisation/default/key'
        );
        $this->thenRequestMethodEquals('GET');
        $this->thenResponseBodyEquals($response, $responseBody);
    }

    /** @param string $expectedUrl */
    private function thenRequestUrlEquals($expectedUrl)
    {
        $this->assertEquals($expectedUrl, $this->http->requests->latest()->getUrl());
    }

    private function thenRequestMethodEquals($expectedMethod)
    {
        $this->assertEquals($expectedMethod, $this->http->requests->latest()->getMethod());
    }

    private function thenResponseBodyEquals($expectedResponse, $actualResponse)
    {
        $this->assertEquals($expectedResponse, $actualResponse);
    }

    private function apiResponds($statusCode, $message)
    {
        $this->http->mock
            ->when()
            ->then()
            ->body($message)
            ->statusCode($statusCode)
            ->end();

        $this->http->setUp();
    }
}
