<?php

namespace LightSaml\SpBundle\Bridge\Container;

use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Build\Container\ServiceContainerInterface;
use LightSaml\Error\LightSamlBuildException;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\Resolver\Session\SessionProcessorInterface;
use LightSaml\Resolver\Signature\SignatureResolverInterface;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;
use LightSaml\Validator\Model\Assertion\AssertionValidatorInterface;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use LightSaml\Validator\Model\Signature\SignatureValidatorInterface;

class ServiceContainer extends AbstractContainer implements ServiceContainerInterface
{
    /**
     * @return AssertionValidatorInterface
     */
    public function getAssertionValidator()
    {
        // TODO: Implement getAssertionValidator() method.
    }

    /**
     * @return AssertionTimeValidator
     */
    public function getAssertionTimeValidator()
    {
        // TODO: Implement getAssertionTimeValidator() method.
    }

    /**
     * @return SignatureResolverInterface
     */
    public function getSignatureResolver()
    {
        // TODO: Implement getSignatureResolver() method.
    }

    /**
     * @return EndpointResolverInterface
     */
    public function getEndpointResolver()
    {
        // TODO: Implement getEndpointResolver() method.
    }

    /**
     * @return NameIdValidatorInterface
     */
    public function getNameIdValidator()
    {
        // TODO: Implement getNameIdValidator() method.
    }

    /**
     * @return BindingFactoryInterface
     */
    public function getBindingFactory()
    {
        // TODO: Implement getBindingFactory() method.
    }

    /**
     * @return SignatureValidatorInterface
     */
    public function getSignatureValidator()
    {
        // TODO: Implement getSignatureValidator() method.
    }

    /**
     * @return CredentialResolverInterface
     */
    public function getCredentialResolver()
    {
        // TODO: Implement getCredentialResolver() method.
    }

    /**
     * @return \LightSaml\Resolver\Logout\LogoutSessionResolverInterface
     */
    public function getLogoutSessionResolver()
    {
        throw new LightSamlBuildException('Not implemented in SP');
    }

    /**
     * @return SessionProcessorInterface
     */
    public function getSessionProcessor()
    {
        // TODO: Implement getSessionProcessor() method.
    }
}
