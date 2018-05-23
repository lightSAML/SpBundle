<?php

namespace LightSaml\SpBundle\Tests;

use LightSaml\SpBundle\LightSamlSpBundle;

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
            ->with($this->isInstanceOf(\LightSaml\SpBundle\DependencyInjection\Security\Factory\LightSamlSpFactory::class));

        $bundle->build($containerBuilderMock);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function getContainerBuilderMock()
    {
        return $this->getMockBuilder(\Symfony\Component\DependencyInjection\ContainerBuilder::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension
     */
    private function getExtensionMock()
    {
        return $this->getMockBuilder(\Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension::class)->getMock();
    }
}
