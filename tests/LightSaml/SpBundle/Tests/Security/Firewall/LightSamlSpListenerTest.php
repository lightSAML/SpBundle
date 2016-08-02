<?php

namespace LightSaml\SpBundle\Tests\Security\Firewall;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use LightSaml\SpBundle\Security\Firewall\LightSamlSpListener;
use Symfony\Component\Security\Core\User\User;

class LightSamlSpListenerTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs()
    {
        new LightSamlSpListener(
            $this->getTokenStorageMock(),
            $this->getAuthenticationManagerMock(),
            $this->getSessionAuthenticationStrategyMock(),
            $this->getHttpUtilsMock(),
            'main',
            $this->getAuthenticationSuccessHandlerMock(),
            $this->getAuthenticationFailureHandlerMock(),
            []
        );
    }

    public function test_calls_profile_to_receive_response_and_authentication_manager_to_authenticate_token()
    {
        $listener = new LightSamlSpListener(
            $this->getTokenStorageMock(),
            $authenticationManagerMock = $this->getAuthenticationManagerMock(),
            $this->getSessionAuthenticationStrategyMock(),
            $httpUtilsMock = $this->getHttpUtilsMock(),
            'main',
            $authenticationSuccessHandlerMock = $this->getAuthenticationSuccessHandlerMock(),
            $this->getAuthenticationFailureHandlerMock(),
            []
        );

        $profileBuilderMock = $this->getProfileBuilderMock();
        $actionMock = $this->getActionMock();
        $contextMock = $this->getContextMock();

        $profileBuilderMock->expects($this->any())
            ->method('buildContext')
            ->willReturn($contextMock);
        $profileBuilderMock->expects($this->any())
            ->method('buildAction')
            ->willReturn($actionMock);

        $samlResponse = new Response();

        $contextMock->expects($this->any())
            ->method('getInboundMessage')
            ->willReturn($samlResponse);

        $actionMock->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function (ProfileContext $context) use ($samlResponse, $contextMock) {
                $this->assertSame($contextMock, $context);
            });

        $authenticationManagerMock->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf(\LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken::class))
            ->willReturn($authenticatedToken = new SamlSpToken([], 'main', [], new User('username', '')));

        $listener->setProfile($profileBuilderMock);

        $requestMock = $this->getRequestMock();
        $requestMock->expects($this->any())
            ->method('hasSession')
            ->willReturn(true);
        $requestMock->expects($this->any())
            ->method('hasPreviousSession')
            ->willReturn(true);
        $requestMock->expects($this->any())
            ->method('getSession')
            ->willReturn($sessionMock = $this->getSessionMock());

        $authenticationSuccessHandlerMock->expects($this->any())
            ->method('onAuthenticationSuccess')
            ->willReturn($responseMock = $this->getResponseMock());

        $eventMock = $this->getGetResponseEventMock();
        $eventMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $httpUtilsMock->expects($this->any())
            ->method('checkRequestPath')
            ->willReturn(true);

        $listener->handle($eventMock);
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
        return $this->getMockBuilder(\LightSaml\Action\ActionInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Builder\Profile\ProfileBuilderInterface
     */
    private function getProfileBuilderMock()
    {
        return $this->getMockBuilder(\LightSaml\Builder\Profile\ProfileBuilderInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Request
     */
    private function getRequestMock()
    {
        return $this->getMockBuilder(\Symfony\Component\HttpFoundation\Request::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Response
     */
    private function getResponseMock()
    {
        return $this->getMockBuilder(\Symfony\Component\HttpFoundation\Response::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private function getSessionMock()
    {
        return $this->getMockBuilder(\Symfony\Component\HttpFoundation\Session\SessionInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpKernel\Event\GetResponseEvent
     */
    private function getGetResponseEventMock()
    {
        return $this->getMockBuilder(\Symfony\Component\HttpKernel\Event\GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface|\Symfony\Component\Security\Core\SecurityContextInterface
     */
    private function getTokenStorageMock()
    {
        if (class_exists('\Symfony\Bundle\SecurityBundle\Command\UserPasswordEncoderCommand')) {
            return $this->getMockBuilder(\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface::class)->getMock();
        } else { // for symfony/security-bundle <= 2.6
            return $this->getMockBuilder(\Symfony\Component\Security\Core\SecurityContextInterface::class)->getMock();
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    private function getAuthenticationManagerMock()
    {
        return $this->getMockBuilder(\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface
     */
    private function getSessionAuthenticationStrategyMock()
    {
        return $this->getMockBuilder(\Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Http\HttpUtils
     */
    private function getHttpUtilsMock()
    {
        return $this->getMockBuilder(\Symfony\Component\Security\Http\HttpUtils::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface
     */
    private function getAuthenticationSuccessHandlerMock()
    {
        return $this->getMockBuilder(\Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface
     */
    private function getAuthenticationFailureHandlerMock()
    {
        return $this->getMockBuilder(\Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface::class)->getMock();
    }
}
