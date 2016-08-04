<?php

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
