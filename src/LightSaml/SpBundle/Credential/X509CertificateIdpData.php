<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Credential;

use LightSaml\Credential\X509Certificate;

class X509CertificateIdpData extends X509Certificate
{
    /**
     * @param string $data
     * @return X509Certificate
     * @throws \InvalidArgumentException
     */
    public static function fromString($data)
    {
        $result = new self();
        $result->loadFromString($data);

        return $result;
    }

    /**
     * @param string $data
     * @return X509Certificate
     * @throws \InvalidArgumentException
     */
    private function loadFromString($data)
    {
        $this->loadPem($data);

        return $this;
    }
}
