<?php

namespace LightSaml\Tests\Credential;

use LightSaml\SpBundle\Credential\X509CertificateIdpData;

class X509CertificateIdpDataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itLoadsX509CertificateFromString()
    {
        X509CertificateIdpData::fromString("-----BEGIN CERTIFICATE-----
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
-----END CERTIFICATE-----");
    }

    /** @test */
    public function itThrowsExceptionWhenLoadingMalformedCertificateFromString()
    {
        $this->expectException(\InvalidArgumentException::class);
        X509CertificateIdpData::fromString("malformed string");
    }
}
