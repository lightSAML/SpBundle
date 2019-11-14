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

use LightSaml\Binding\AbstractBinding;
use LightSaml\Binding\BindingFactory;
use LightSaml\Builder\Profile\ProfileBuilderInterface;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Response;
use LightSaml\SamlConstants;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class LightSamlSpListener extends AbstractAuthenticationListener
{
    /** @var ProfileBuilderInterface */
    private $profile;

    /** @var BindingFactory */
    private $bindingFactory;

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
     * @param BindingFactory $bindingFactory
     *
     * @return LightSamlSpListener
     */
    public function setBindingFactory(BindingFactory $bindingFactory)
    {
        $this->bindingFactory = $bindingFactory;

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
        $bindingType = $this->bindingFactory->detectBindingType($request);

        if (null === $bindingType) {
            throw new \LogicException('No SAML response.');
        }

        $binding = $this->bindingFactory->create($bindingType);
        $messageContext = new MessageContext();
        /* @var $binding AbstractBinding */
        $binding->receive($request, $messageContext);
        $samlRequest = $messageContext->getMessage();

        if ($samlRequest instanceof LogoutResponse) {
            $status = $samlRequest->getStatus();
            $code = $status->getStatusCode() ? $status->getStatusCode()->getValue() : null;

            if (SamlConstants::STATUS_PARTIAL_LOGOUT === $code || SamlConstants::STATUS_SUCCESS === $code) {
                $request->getSession()->invalidate();
            }

            throw new AuthenticationException('This is a logout response');
        }

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
