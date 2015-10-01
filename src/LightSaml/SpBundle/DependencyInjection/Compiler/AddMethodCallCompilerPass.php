<?php

namespace LightSaml\SpBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddMethodCallCompilerPass implements CompilerPassInterface
{
    /** @var string */
    private $serviceId;

    /** @var string */
    private $tagName;

    /** @var string */
    private $methodName;

    /**
     * @param $serviceId
     * @param $tagName
     * @param $methodName
     */
    public function __construct($serviceId, $tagName, $methodName)
    {
        $this->serviceId = $serviceId;
        $this->tagName = $tagName;
        $this->methodName = $methodName;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->has($this->serviceId)) {
            return;
        }

        $definition = $container->findDefinition($this->serviceId);

        $taggedServices = $container->findTaggedServiceIds($this->tagName);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall($this->methodName, [new Reference($id)]);
        }
    }
}
