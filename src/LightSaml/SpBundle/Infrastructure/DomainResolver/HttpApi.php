<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Infrastructure\DomainResolver;

use Http\Client\HttpClient;
use LightSaml\SpBundle\Infrastructure\RestApi;
use Symfony\Component\HttpFoundation\RequestStack;

class HttpApi extends RestApi implements Api
{
    /** @var string */
    private $domain;

    public function __construct(HttpClient $httpClient, RequestStack $requestStack, $domainResolverApiUrl)
    {
        $this->domain = $requestStack->getCurrentRequest()->getHost();
        parent::__construct($httpClient, $domainResolverApiUrl);
    }

    public function getOrganisation()
    {
        $response = $this->sendRequest('GET', sprintf('domain/%s', $this->domain));

        return json_decode($response->getBody()->getContents(), true);
    }
}
