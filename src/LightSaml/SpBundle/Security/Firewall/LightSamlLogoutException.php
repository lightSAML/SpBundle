<?php

namespace LightSaml\SpBundle\Security\Firewall;

use LightSaml\Error\LightSamlException;
use LightSaml\Model\Protocol\LogoutResponse;

/**
 * Class LightSamlLogoutException.
 */
class LightSamlLogoutException extends LightSamlException
{
    const MSG_TEMPLATE = 'Invalid logout status returned by IP - "%s"';

    /**
     * LightSamlLogoutException constructor.
     *
     * @param LogoutResponse $logoutResponse
     */
    public function __construct(LogoutResponse $logoutResponse)
    {
        parent::__construct(sprintf(self::MSG_TEMPLATE, $logoutResponse->getStatus()->getStatusMessage()));
    }
}
