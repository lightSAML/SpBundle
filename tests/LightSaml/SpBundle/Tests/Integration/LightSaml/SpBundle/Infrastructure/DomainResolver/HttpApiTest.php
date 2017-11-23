<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Integration\LightSaml\SpBundle\Infrasctrucure\DomainResolver;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use LightSaml\SpBundle\Infrastructure\DomainResolver\HttpApi;
use LightSaml\SpBundle\Tests\Integration\ContainerAwareTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
        $this->setupHost();

        $this->httpApi = $this->getContainer()->get('lightsaml_sp.domain_resolver_api.http');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->tearDownHttpMock();
    }

    /** @test */
    public function itGetsOrganisation()
    {
        $responseBody = ['organisation' => 'default'];
        $this->apiResponds(200, $responseBody);

        $response = $this->httpApi->getOrganisation();

        $this->thenRequestUrlEquals('http://localhost:9092/domain/piwikpro.dev');
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

    private function setupHost()
    {
        $request = $this->prophesize(Request::class);
        $request->getHost()->willReturn('piwikpro.dev');

        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn($request->reveal());

        $this->getContainer()->set('request_stack', $requestStack->reveal());
    }

    private function apiResponds($statusCode, array $message)
    {
        $this->http->mock
            ->when()
            ->then()
            ->body(json_encode($message))
            ->statusCode($statusCode)
            ->end();

        $this->http->setUp();
    }
}
