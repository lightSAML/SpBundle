<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Infrastructure\IdpData;

use Helmich\Psr7Assert\Psr7Assertions;
use Http\Client\Exception\HttpException;
use Http\Mock\Client;
use LightSaml\SpBundle\Infrastructure\IdpData\HttpApi;
use LightSaml\SpBundle\Infrastructure\RestApiFailure;

class HttpApiTest extends \PHPUnit_Framework_TestCase
{
    use Psr7Assertions;

    /** @var HttpApi */
    private $httpApi;

    /** @var Client */
    private $client;

    /** @test */
    public function itGetsMetadata()
    {
        $this->httpApi->getMetadata('default');

        $this->assertRequestHasUri(
            $this->lastRequest(),
            'http://idp-data/organisation/default/metadata'
        );
        $this->assertRequestIsGet($this->lastRequest());
    }

    /** @test */
    public function itFailsWhenErrorHappensWhileGettingMetadata()
    {
        $this->client->addException($this->prophesize(HttpException::class)->reveal());

        $this->expectException(RestApiFailure::class);

        $this->httpApi->getMetadata('default');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->client = new Client();
        $this->httpApi = new HttpApi($this->client, 'http://idp-data');
    }

    private function lastRequest()
    {
        return $this->client->getRequests()[0];
    }
}
