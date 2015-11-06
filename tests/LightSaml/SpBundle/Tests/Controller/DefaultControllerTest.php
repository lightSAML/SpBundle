<?php

namespace LightSaml\SpBundle\Tests\Controller;

use LightSaml\SpBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
    public function test_metadata_action_returns_response_from_profile()
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainerMock()
    {
        return $this->getMock(\Symfony\Component\DependencyInjection\ContainerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Builder\Profile\ProfileBuilderInterface
     */
    private function getProfileBuilderMock()
    {
        return $this->getMock(\LightSaml\Builder\Profile\ProfileBuilderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\Profile\ProfileContext
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(\LightSaml\Context\Profile\ProfileContext::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Action\ActionInterface
     */
    private function getActionMock()
    {
        return $this->getMock(\LightSaml\Action\ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\Profile\HttpResponseContext
     */
    private function getHttpResponseContextMock()
    {
        return $this->getMock(\LightSaml\Context\Profile\HttpResponseContext::class);
    }
}
