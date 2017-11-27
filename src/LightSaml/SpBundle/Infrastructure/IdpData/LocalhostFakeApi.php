<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Infrastructure\IdpData;

class LocalhostFakeApi implements Api
{
    const LIGHTSAML_PATH = __DIR__ . '/../../../../../vendor/lightsaml/lightsaml';

    public function getMetadata($organisation)
    {
        return file_get_contents(
            self::LIGHTSAML_PATH . '/web/sp/localhost-lightsaml-lightsaml-idp.xml'
        );
    }

    public function getCertificate($organisation)
    {
        return file_get_contents(
            self::LIGHTSAML_PATH . '/resources/sample/Certificate/lightsaml-idp.crt'
        );
    }

    public function getPrivateKey($organisation)
    {
        return file_get_contents(
            self::LIGHTSAML_PATH . '/resources/sample/Certificate/lightsaml-idp.key'
        );
    }
}
