<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Metadata;

use LightSaml\SpBundle\Infrastructure\DomainResolver\Api as DomainResolverApi;
use LightSaml\SpBundle\Infrastructure\IdpData\Api as IdpDataApi;
use LightSaml\SpBundle\Infrastructure\RestApiFailure;
use LightSaml\SpBundle\Metadata\IdpDataMetadataProvider;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class IdpDataMetadataProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itReturnsMetadata()
    {
        $domainResolverApi = $this->prophesize(DomainResolverApi::class);
        $domainResolverApi->getOrganisation()->willReturn('default');

        $idpDataApi = $this->prophesize(IdpDataApi::class);
        $idpDataApi->getMetadata(Argument::exact('default'))->willReturn('metadata.xml');

        $idpDataMetadataProvider = new IdpDataMetadataProvider(
            $idpDataApi->reveal(),
            $domainResolverApi->reveal(),
            $this->prophesize(LoggerInterface::class)->reveal()
        );

        $this->assertEquals('metadata.xml', $idpDataMetadataProvider->getMetadata());
    }

    /** @test */
    public function itLogsErrorWhenDomainResolverApiFails()
    {
        $exception = new RestApiFailure('some message');

        $domainResolverApi = $this->prophesize(DomainResolverApi::class);
        $domainResolverApi->getOrganisation()->willThrow($exception);

        $logger = $this->prophesize(LoggerInterface::class);

        $idpDataMetadataProvider = new IdpDataMetadataProvider(
            $this->prophesize(IdpDataApi::class)->reveal(),
            $domainResolverApi->reveal(),
            $logger->reveal()
        );

        $idpDataMetadataProvider->getMetadata();

        $logger->error('some message', ['exception' => $exception])->shouldHaveBeenCalled();
    }

    /** @test */
    public function itLogsErrorWhenIdpDataApiFails()
    {
        $exception = new RestApiFailure('some message');

        $domainResolverApi = $this->prophesize(DomainResolverApi::class);
        $domainResolverApi->getOrganisation()->willReturn('default');

        $idpDataApi = $this->prophesize(IdpDataApi::class);
        $idpDataApi->getMetadata(Argument::exact('default'))->willThrow($exception);

        $logger = $this->prophesize(LoggerInterface::class);

        $idpDataMetadataProvider = new IdpDataMetadataProvider(
            $idpDataApi->reveal(),
            $domainResolverApi->reveal(),
            $logger->reveal()
        );

        $idpDataMetadataProvider->getMetadata();

        $logger->error('some message', ['exception' => $exception])->shouldHaveBeenCalled();
    }
}
