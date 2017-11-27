<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Store\Credential;

use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Credential;
use LightSaml\SpBundle\Credential\X509CertificateIdpData;
use LightSaml\SpBundle\Infrastructure\DomainResolver\Api as DomainResolverApi;
use LightSaml\SpBundle\Infrastructure\IdpData\Api as IdpDataApi;
use LightSaml\SpBundle\Infrastructure\RestApiFailure;
use LightSaml\Store\Credential\CredentialStoreInterface;
use Psr\Log\LoggerInterface;

class IdpDataCredentialStore implements CredentialStoreInterface
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

    /**
     * @param string $entityId
     *
     * @return \LightSaml\Credential\CredentialInterface[]
     * @throws \InvalidArgumentException
     */
    public function getByEntityId($entityId)
    {
        $certificate = '';
        $privateKey = '';
        try {
            $organisation = $this->domainResolverApi->getOrganisation();
            $certificate = $this->idpDataApi->getCertificate($organisation);
            $privateKey = $this->idpDataApi->getPrivateKey($organisation);
        } catch (RestApiFailure $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        $credential = new X509Credential(
            X509CertificateIdpData::fromString($certificate),
            KeyHelper::createPrivateKey($privateKey, '')
        );
        $credential->setEntityId($entityId);

        return [$credential];
    }
}
