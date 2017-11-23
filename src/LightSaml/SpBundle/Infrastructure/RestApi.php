<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Infrastructure;

use GuzzleHttp\Psr7\Request;
use Http\Client\Exception as HttpClientException;
use Http\Client\HttpClient;

abstract class RestApi
{
    /** @var HttpClient */
    private $httpClient;

    /** @var string */
    private $restApiUrl;

    public function __construct(HttpClient $httpClient, $restApiUrl)
    {
        $this->httpClient = $httpClient;
        $this->restApiUrl = $restApiUrl;
    }

    protected function sendRequest($method, $resourcePath)
    {
        try {
            return $this->httpClient->sendRequest(new Request(
                $method,
                sprintf('%s/%s', $this->restApiUrl, $resourcePath),
                ['Content-Type' => 'application/json']
            ));
        } catch (HttpClientException $e) {
            throw RestApiFailure::fromPrevious($e);
        }
    }
}
