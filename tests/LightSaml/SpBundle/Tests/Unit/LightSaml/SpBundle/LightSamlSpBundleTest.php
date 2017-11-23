<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle;

use LightSaml\SpBundle\DependencyInjection\Security\Factory\LightSamlSpFactory;
use LightSaml\SpBundle\LightSamlSpBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LightSamlSpBundleTest extends \PHPUnit_Framework_TestCase
{
    public function test_build_adds_security_extension()
    {
        $bundle = new LightSamlSpBundle();

        $containerBuilderMock = $this->getContainerBuilderMock();
        $containerBuilderMock->expects($this->once())
            ->method('getExtension')
            ->with('security')
            ->willReturn($extensionMock = $this->getExtensionMock());

        $extensionMock->expects($this->once())
            ->method('addSecurityListenerFactory')
            ->with($this->isInstanceOf(LightSamlSpFactory::class));

        $bundle->build($containerBuilderMock);
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder */
    private function getContainerBuilderMock()
    {
        return $this->getMockBuilder(ContainerBuilder::class)->getMock();
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|SecurityExtension */
    private function getExtensionMock()
    {
        return $this->getMockBuilder(
            SecurityExtension::class
        )->getMock();
    }
}
