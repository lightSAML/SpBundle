<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareTestCase extends WebTestCase
{
    /** @var Client */
    protected $client;

    /** @var ContainerInterface */
    protected $container;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = static::$kernel->getContainer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    protected function getContainer()
    {
        return $this->container;
    }
}
