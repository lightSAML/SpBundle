<?php

namespace LightSaml\SpBundle;

use LightSaml\SpBundle\DependencyInjection\Compiler\AddMethodCallCompilerPass;
use LightSaml\SpBundle\DependencyInjection\Security\Factory\LightSamlSpFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LightSamlSpBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new LightSamlSpFactory());
    }
}
