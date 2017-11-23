<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\Controller;

use LightSaml\Action\ActionInterface;
use LightSaml\Builder\Profile\ProfileBuilderInterface;
use LightSaml\Context\Profile\HttpResponseContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\SpBundle\Controller\DefaultController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testMetadataActionReturnsResponseFromProfile()
    {
        $controller = new DefaultController();
        $controller->setContainer($containerMock = $this->getContainerMock());

        $containerMock->expects($this->any())
            ->method('get')
            ->with('ligthsaml.profile.metadata')
            ->willReturn($profileBuilderMock = $this->getProfileBuilderMock());

        $actionMock = $this->getActionMock();
        $contextMock = $this->getContextMock();

        $profileBuilderMock->expects($this->any())
            ->method('buildContext')
            ->willReturn($contextMock);
        $profileBuilderMock->expects($this->any())
            ->method('buildAction')
            ->willReturn($actionMock);

        $contextMock->expects($this->once())
            ->method('getHttpResponseContext')
            ->willReturn($httpResponseContext = $this->getHttpResponseContextMock());

        $httpResponseContext->expects($this->once())
            ->method('getResponse')
            ->willReturn($expectedResponse = new Response(''));

        $actualResponse = $controller->metadataAction();

        $this->assertSame($expectedResponse, $actualResponse);
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    private function getContainerMock()
    {
        return $this->getMockBuilder(ContainerInterface::class)->getMock();
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|ProfileBuilderInterface */
    private function getProfileBuilderMock()
    {
        return $this->getMockBuilder(ProfileBuilderInterface::class)->getMock();
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|ProfileContext */
    private function getContextMock()
    {
        return $this->getMockBuilder(ProfileContext::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface */
    private function getActionMock()
    {
        return $this->getMockBuilder(ActionInterface::class)->getMock();
    }

    /** @return \PHPUnit_Framework_MockObject_MockObject|HttpResponseContext */
    private function getHttpResponseContextMock()
    {
        return $this->getMockBuilder(HttpResponseContext::class)->getMock();
    }
}
