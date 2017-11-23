<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Metadata;

use LightSaml\SpBundle\Infrastructure\DomainResolver\Api as DomainResolverApi;
use LightSaml\SpBundle\Infrastructure\IdpData\Api as IdpDataApi;
use LightSaml\SpBundle\Infrastructure\RestApiFailure;
use Psr\Log\LoggerInterface;

class IdpDataMetadataProvider implements MetadataProvider
{
    /** @var IdpDataApi */
    private $idpDataApi;

    /** @var DomainResolverApi */
    private $domainResolverApi;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(IdpDataApi $idpDataApi, DomainResolverApi $domainResolverApi, LoggerInterface $logger)
    {
        $this->idpDataApi = $idpDataApi;
        $this->domainResolverApi = $domainResolverApi;
        $this->logger = $logger;
    }

    public function getMetadata()
    {
        try {
            $organisation = $this->domainResolverApi->getOrganisation();

            return $this->idpDataApi->getMetadata($organisation);
        } catch (RestApiFailure $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}
