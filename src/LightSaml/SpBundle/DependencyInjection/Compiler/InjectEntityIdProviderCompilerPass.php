<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\DependencyInjection\Compiler;

use LightSaml\SpBundle\DependencyInjection\Compiler\Exception\DefinitionNotFoundException;
use LightSaml\SpBundle\DependencyInjection\Compiler\Exception\InvalidEntityIdProviderException;
use LightSaml\SpBundle\DependencyInjection\Configuration;
use LightSaml\SpBundle\Security\Authentication\EntityId\EntityIdProviderInterface as Provider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ExpressionLanguage\Expression;

class InjectEntityIdProviderCompilerPass implements CompilerPassInterface
{
    public function getServiceArgumentMap()
    {
        return [
            'lightsaml.own.entity_descriptor_provider' => 0,
            'lightsaml.own.credential_store.0' => 0,
            'lightsaml.credential.credential_store' => 2,
            'lightsaml_sp.slo.message_factory' => 0
        ];
    }

    public function process(ContainerBuilder $container)
    {
        $parameterName = sprintf('%s.%s', Configuration::CONFIGURATION_NAME, Provider::PROVIDER_NAME);
        if (!$container->hasParameter($parameterName)) {
            return;
        }

        $providerName = $container->getParameter($parameterName);
        if (!$this->validateProvider($container, $providerName)) {
            throw new InvalidEntityIdProviderException($providerName);
        }

        $providerExpression = new Expression(sprintf(Provider::PROVIDER_PATTERN, $providerName));
        foreach ($this->getServiceArgumentMap() as $serviceName => $argumentIndex) {
            if (!$container->hasDefinition($serviceName)) {
                throw new DefinitionNotFoundException($serviceName);
            }

            $serviceDefinition = $container->getDefinition($serviceName);
            $serviceDefinition
                ->replaceArgument($argumentIndex, $providerExpression);
        }
    }

    private function validateProvider(ContainerBuilder $container, $providerName)
    {
        $providerDefinition = $container->getDefinition($providerName);
        $providerReflection = new \ReflectionClass($providerDefinition->getClass());

        return $providerReflection->implementsInterface(Provider::class);
    }
}
