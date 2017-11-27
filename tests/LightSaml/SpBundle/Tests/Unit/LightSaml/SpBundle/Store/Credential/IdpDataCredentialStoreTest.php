<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Store\Credential;

use InvalidArgumentException;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\X509Credential;
use LightSaml\SpBundle\Infrastructure\DomainResolver\Api as DomainResolverApi;
use LightSaml\SpBundle\Infrastructure\IdpData\Api as IdpDataApi;
use LightSaml\SpBundle\Infrastructure\RestApiFailure;
use LightSaml\SpBundle\Store\Credential\IdpDataCredentialStore;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class IdpDataCredentialStoreTest extends \PHPUnit_Framework_TestCase
{
    private $cert = "-----BEGIN CERTIFICATE-----
MIID9TCCAt2gAwIBAgIJAIlX2F9iF0DuMA0GCSqGSIb3DQEBCwUAMIGPMQswCQYD
VQQGEwJQTDETMBEGA1UECAwKRG9sbnlzbGFzazEQMA4GA1UEBwwHV3JvY2xhdzER
MA8GA1UECgwIUGl3aWtQUk8xETAPBgNVBAsMCFBpd2lrUFJPMREwDwYDVQQDDAhQ
aXdpa1BSTzEgMB4GCSqGSIb3DQEJARYRY29udGFjdEBwaXdpay5wcm8wIBcNMTcw
NzEyMTYzNzI2WhgPMjExNzA3MDkxNjM3MjZaMIGPMQswCQYDVQQGEwJQTDETMBEG
A1UECAwKRG9sbnlzbGFzazEQMA4GA1UEBwwHV3JvY2xhdzERMA8GA1UECgwIUGl3
aWtQUk8xETAPBgNVBAsMCFBpd2lrUFJPMREwDwYDVQQDDAhQaXdpa1BSTzEgMB4G
CSqGSIb3DQEJARYRY29udGFjdEBwaXdpay5wcm8wggEiMA0GCSqGSIb3DQEBAQUA
A4IBDwAwggEKAoIBAQDH0bczI2hBDR+yFDFd1tsFtCssyu2gwr1/k4MzdCzb9fby
oPKeTy1EqrMmtLSErFPI/c4Jpq40jk0j7Z3US8K7iMe03pN8t8444ahuD7fUKVH1
8swNY/torHJd3i2FQzscmyMxnz35u/puDrSLznuPUvOcBq8CF5De6z22zlsMlGds
U6x/exKMy33jl9qTtv2diY/rAB+gmaLfYWPf0CMChL5m4RxF3M0Y2/R9Gr4urd8H
V6KFre8N3Npw+/HiK0GHgbRB1zS/pCHEScZq5alnAiCtRyasxb9Vj/ER19VmlJ57
Jhj13rSxJ4rQ/qnX6FaI9lzoPyx/vNif0sa19sP3AgMBAAGjUDBOMB0GA1UdDgQW
BBQptq1kt+G3iFwpt77ukbmHOOFGvTAfBgNVHSMEGDAWgBQptq1kt+G3iFwpt77u
kbmHOOFGvTAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQCqYsSWLuh0
wWhzalLDON4kooqyhcUJYz0DIUOVAqWlu4msGtAiqa4F7LatcjbXgkYSmPKQIBku
t5rx6+l4Ieofvye6beL/ahec8ud4dH1vmw6qiMb4PsOwhtM0j8pgcQGaiqWW8TU2
B1c/WIKyCXd5thzlhPNIWnGhxYk8G8J8mrFu3XpddTvE5Ns2uDU5uNZnNnGap6qg
GUbHmScFKPhW5gPqE1Y7cWegjhD2LcBlbTEaYtxin/P789Ur/p5gp0es2K+y3S3T
RmUm1tIsQFoMOb0mGvseHYTayC8tnKkxKoJZyzjqNkGJUEdWyAX0+6IsdxVKU9cN
WN6WIDbzX3Vo
-----END CERTIFICATE-----";

    private $key = '/../../../../../../../../../vendor/lightsaml/lightsaml/resources/sample/Certificate/lightsaml-idp.key';

    /** @var DomainResolverApi */
    private $domainResolverApi;

    /** @var IdpDataApi */
    private $idpDataApi;

    /** @var LoggerInterface */
    private $logger;

    /** @var IdpDataCredentialStore */
    private $idpDataCredentialStore;

    /** @var CredentialInterface */
    private $credentials;

    /** @test */
    public function itRetrievesCredentialsByEntityId()
    {
        $this->iHaveIdpDataCredentialStore();
        $this->whenIAskStoreForCredentialsByEntityId('some entityId');
        $this->thenDomainResolverShouldBeCalledForOrganisation();
        $this->thenIdpDataShouldBeCalledForCertificateAndPrivateKey();
        $this->thenCredentialsShouldBeReturned('some entityId');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid PEM encoded certificate
     */
    public function itThrowsExceptionAndLogsErrorWhenDomainResolverApiFails()
    {
        $this->iHaveIdpDataCredentialStore();

        $exception = new RestApiFailure('some message');
        $this->domainResolverApi->getOrganisation()->willThrow($exception);

        $this->whenIAskStoreForCredentialsByEntityId('some entityId');

        $this->thenErrorShouldBeLogged($exception);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid PEM encoded certificate
     */
    public function itThrowsExceptionAndLogsErrorWhenRetrievingCertificateFromIdpDataApiFails()
    {
        $this->iHaveIdpDataCredentialStore();

        $exception = new RestApiFailure('some message');
        $this->idpDataApi->getCertificate(Argument::exact('default'))->willThrow($exception);

        $this->whenIAskStoreForCredentialsByEntityId('some entityId');
        $this->thenErrorShouldBeLogged($exception);
    }

    /** @test */
    public function itLogsErrorWhenRetrievingPrivateKeyFromIdpDataApiFailsButRetrievesCredentialsByEntityId()
    {
        $this->iHaveIdpDataCredentialStore();

        $exception = new RestApiFailure('some message');
        $this->idpDataApi->getPrivateKey(Argument::exact('default'))->willThrow($exception);

        $this->whenIAskStoreForCredentialsByEntityId('some entityId');
        $this->thenErrorShouldBeLogged($exception);
        $this->thenCredentialsShouldBeReturned('some entityId');
    }

    private function iHaveIdpDataCredentialStore()
    {
        $this->domainResolverApi = $this->prophesize(DomainResolverApi::class);
        $this->domainResolverApi->getOrganisation()->willReturn('default');

        $this->idpDataApi = $this->prophesize(IdpDataApi::class);
        $this->idpDataApi->getCertificate(Argument::exact('default'))->willReturn($this->cert);
        $this->idpDataApi->getPrivateKey(Argument::exact('default'))->willReturn(
            file_get_contents(__DIR__ . $this->key)
        );

        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->idpDataCredentialStore = new IdpDataCredentialStore(
            $this->idpDataApi->reveal(),
            $this->domainResolverApi->reveal(),
            $this->logger->reveal()
        );
    }

    private function whenIAskStoreForCredentialsByEntityId($entityId)
    {
        $this->credentials = $this->idpDataCredentialStore->getByEntityId($entityId);
    }

    private function thenDomainResolverShouldBeCalledForOrganisation()
    {
        $this->domainResolverApi->getOrganisation()->shouldHaveBeenCalled();
    }

    private function thenIdpDataShouldBeCalledForCertificateAndPrivateKey()
    {
        $this->idpDataApi->getCertificate(Argument::exact('default'))->shouldHaveBeenCalled();
        $this->idpDataApi->getPrivateKey(Argument::exact('default'))->shouldHaveBeenCalled();
    }

    private function thenCredentialsShouldBeReturned()
    {
        $this->assertInstanceOf(X509Credential::class, current($this->credentials));
    }

    private function thenErrorShouldBeLogged($exception)
    {
        $this->logger->error('some message', ['exception' => $exception])->shouldHaveBeenCalled();
    }
}
