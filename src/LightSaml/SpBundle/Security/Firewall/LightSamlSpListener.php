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

use LightSaml\Builder\Profile\ProfileBuilderInterface;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class LightSamlSpListener extends AbstractAuthenticationListener
{
    /** @var ProfileBuilderInterface */
    private $profile;

    /**
     * @param ProfileBuilderInterface $profile
     *
     * @return LightSamlSpListener
     */
    public function setProfile(ProfileBuilderInterface $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Performs authentication.
     *
     * @param Request $request A Request instance
     *
     * @return TokenInterface|Response|null The authenticated token, null if full authentication is not possible, or a Response
     *
     * @throws AuthenticationException if the authentication fails
     */
    protected function attemptAuthentication(Request $request)
    {
        $samlResponse = $this->receiveSamlResponse();

        $token = new SamlSpResponseToken($samlResponse, $this->providerKey);

        $token = $this->authenticationManager->authenticate($token);

        return $token;
    }

    /**
     * @return \LightSaml\Model\Protocol\Response
     */
    private function receiveSamlResponse()
    {
        $context = $this->profile->buildContext();
        $action = $this->profile->buildAction();

        $action->execute($context);

        return $context->getInboundMessage();
    }
}
