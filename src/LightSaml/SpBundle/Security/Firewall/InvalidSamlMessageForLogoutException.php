<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Security\Firewall;

use LightSaml\Error\LightSamlException;
use LightSaml\Model\Protocol\SamlMessage;

/**
 * Class InvalidSamlMessageForLogoutException.
 */
class InvalidSamlMessageForLogoutException extends LightSamlException
{
    const MSG_TEMPLATE = 'Received SAML message "%s" is not supported by logout action.';

    public function __construct(SamlMessage $samlMessage)
    {
        parent::__construct(sprintf(self::MSG_TEMPLATE, get_class($samlMessage)));
    }
}
