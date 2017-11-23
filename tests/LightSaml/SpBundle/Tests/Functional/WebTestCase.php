<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use LightSaml\SpBundle\Tests\Integration\ContainerAwareTestCase;

class WebTestCase extends ContainerAwareTestCase
{
    /** @var Client */
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->getContainer()->get('test.client');
    }

    protected function sendJsonRequest($method, $url, array $requestData = [])
    {
        $this->client->request($method, $url, $requestData, [], [
            'CONTENT_TYPE' => 'application/json'
        ]);
    }

    protected function lastResponse()
    {
        return $this->client->getResponse();
    }

    protected function assertStatusCodeEquals($statusCode)
    {
        $this->assertEquals($statusCode, $this->lastResponse()->getStatusCode());
    }
}
