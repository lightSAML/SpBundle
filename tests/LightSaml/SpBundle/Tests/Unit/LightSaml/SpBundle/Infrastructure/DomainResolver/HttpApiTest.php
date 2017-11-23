<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Infrastructure\DomainResolver;

use Helmich\Psr7Assert\Psr7Assertions;
use Http\Client\Exception\HttpException;
use Http\Mock\Client;
use LightSaml\SpBundle\Infrastructure\DomainResolver\HttpApi;
use LightSaml\SpBundle\Infrastructure\RestApiFailure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class HttpApiTest extends \PHPUnit_Framework_TestCase
{
    use Psr7Assertions;

    /** @var HttpApi */
    private $httpApi;

    /** @var Client */
    private $client;

    /** @test */
    public function itGetsOrganisation()
    {
        $this->httpApi->getOrganisation();

        $this->assertRequestHasUri(
            $this->lastRequest(),
            'http://domain-resolver/domain/piwikpro.dev'
        );
        $this->assertRequestIsGet($this->lastRequest());
    }

    /** @test */
    public function itFailsWhenErrorHappensWhileGettingOrganisation()
    {
        $this->client->addException($this->prophesize(HttpException::class)->reveal());

        $this->expectException(RestApiFailure::class);

        $this->httpApi->getOrganisation();
    }

    protected function setUp()
    {
        parent::setUp();

        $request = $this->prophesize(Request::class);
        $request->getHost()->willReturn('piwikpro.dev');

        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn($request->reveal());

        $this->client = new Client();
        $this->httpApi = new HttpApi($this->client, $requestStack->reveal(), 'http://domain-resolver');
    }

    private function lastRequest()
    {
        return $this->client->getRequests()[0];
    }
}
