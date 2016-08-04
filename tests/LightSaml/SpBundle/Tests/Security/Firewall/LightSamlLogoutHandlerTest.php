<?php
namespace LightSaml\SpBundle\Tests\Security\Firewall;
use LightSaml\Binding\AbstractBinding;
use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\Model\Protocol\StatusResponse;
use LightSaml\SamlConstants;
use LightSaml\SpBundle\Model\Protocol\LogoutMessageContextFactory;
use LightSaml\SpBundle\Security\Firewall\LightSamlLogoutHandler;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\State\Sso\SsoState;
use LightSaml\Store\Sso\SsoStateStoreInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class LightSamlLogoutHandlerTest
 * @package LightSaml\SpBundle\Tests\Security\Firewall
 */
class LightSamlLogoutHandlerTest extends \PHPUnit_Framework_TestCase
{
    const REQUEST_TYPE_LOGOUT = 1;
    const REQUEST_TYPE_LOGOUT_REQUEST = 2;
    const REQUEST_TYPE_LOGOUT_RESPONSE = 3;
    const REQUEST_TYPE_INVALID = 4;

    const REDIRECT_SAML_LOGOUT_REQUEST = '/saml_logout_request';
    const REDIRECT_SAML_LOGOUT_RESPONSE = '/saml_logout_response';
    const REDIRECT_HOMEPAGE = '/homepage';

    /** @var bool */
    private $returnSession = false;
    /** @var int */
    private $requestType;
    /** @var RedirectResponse */
    private $result;
    /** @var string */
    private $logoutResponseMessageStatusCode;
    /** @var bool */
    private $wasLoggedOut = false;

    public function test_sending_of_logout_request_to_ip_when_user_want_to_logout()
    {
        $this->iAmLoggedIn();

        $this->whenIWantToLogout();

        $this->thenLogoutMessageShouldBeSentToIP();
    }

    public function test_handling_of_logout_request_from_ip_when_user_is_not_logged_in()
    {
        $this->whenAnonymousWantToLogout();

        $this->thenSystemShouldRedirectIPToDefinedPage();
    }

    public function test_handling_of_logout_request_from_ip_when_user_is_logged_in()
    {
        $this->iAmLoggedIn();

        $this->whenSystemReceivesLogoutRequestFromIP();

        $this->thenIShouldBeSuccessfullyLoggedOut();
        $this->thenResponseAboutSuccessfullyLogoutShouldBeSentToIP();
    }

    public function test_handling_of_successful_logout_response_from_ip()
    {
        $this->iAmLoggedIn();

        $this->whenSystemReceivesSuccessfulLogoutResponseFromIP();

        $this->thenIShouldBeSuccessfullyLoggedOut();
        $this->thenSystemShouldRedirectIPToDefinedPage();
    }

    /**
     * @expectedException \LightSaml\SpBundle\Security\Firewall\LightSamlLogoutException
     */
    public function test_handling_of_unsuccessful_logout_response_from_ip()
    {
        $this->iAmLoggedIn();

        $this->whenSystemReceivesUnsuccessfulLogoutResponseFromIP();
    }

    /**
     * @expectedException \LightSaml\SpBundle\Security\Firewall\InvalidSamlMessageForLogoutException
     */
    public function test_handling_of_invalid_message_from_ip()
    {
        $this->iAmLoggedIn();

        $this->whenSystemReceivesInvalidMessageFromIP();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->returnSession = false;
        $this->requestType = null;
        $this->result = null;
        $this->logoutResponseMessageStatusCode = null;
        $this->wasLoggedOut = false;
    }

    private function iAmLoggedIn()
    {
        $this->returnSession = true;
    }

    private function whenIWantToLogout()
    {
        $this->requestType = self::REQUEST_TYPE_LOGOUT;

        $this->handleLogoutRequest();
    }

    private function whenAnonymousWantToLogout()
    {
        $this->whenIWantToLogout();
    }

    private function whenSystemReceivesLogoutRequestFromIP()
    {
        $this->requestType = self::REQUEST_TYPE_LOGOUT_REQUEST;

        $this->handleLogoutRequest();
    }

    private function whenSystemReceivesSuccessfulLogoutResponseFromIP()
    {
        $this->requestType = self::REQUEST_TYPE_LOGOUT_RESPONSE;
        $this->logoutResponseMessageStatusCode = SamlConstants::STATUS_SUCCESS;

        $this->handleLogoutRequest();
    }

    private function whenSystemReceivesUnsuccessfulLogoutResponseFromIP()
    {
        $this->requestType = self::REQUEST_TYPE_LOGOUT_RESPONSE;
        $this->logoutResponseMessageStatusCode = SamlConstants::STATUS_VERSION_MISMATCH;

        $this->handleLogoutRequest();
    }

    private function whenSystemReceivesInvalidMessageFromIP()
    {
        $this->requestType = self::REQUEST_TYPE_INVALID;

        $this->handleLogoutRequest();
    }

    private function handleLogoutRequest()
    {
        $logoutHandler = new LightSamlLogoutHandler(
            $this->getBindingFactory(),
            $this->getSsoStateStore(),
            $this->getRouter(),
            $this->getLogoutMessageContextFactory()
        );

        $this->result = $logoutHandler->onLogoutSuccess($this->getRequest());
    }

    /**
     * @return BindingFactoryInterface
     */
    private function getBindingFactory()
    {
        $mock = $this->getMockBuilder(BindingFactoryInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('detectBindingType')
            ->willReturnCallback(function () {
                return $this->requestType === self::REQUEST_TYPE_LOGOUT ? null : 'BindingType';
            });

        $mock
            ->expects($this->any())
            ->method('create')
            ->willReturnCallback(function () {
                return $this->getBinding();
            });

        return $mock;
    }

    /**
     * @return AbstractBinding
     */
    private function getBinding()
    {
        $mock = $this->getMockBuilder(AbstractBinding::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('receive')
            ->willReturnCallback(function (Request $request, MessageContext $context) {
                if ($this->requestType === self::REQUEST_TYPE_LOGOUT_REQUEST) {
                    $context->setMessage($this->getLogoutRequestMessage());
                } elseif ($this->requestType === self::REQUEST_TYPE_LOGOUT_RESPONSE) {
                    $context->setMessage($this->getLogoutResponseMessage());
                } else {
                    $context->setMessage($this->getMockBuilder(StatusResponse::class)->getMock());
                }
            });

        $mock
            ->expects($this->any())
            ->method('send')
            ->willReturnCallback(function () {
                $path = self::REDIRECT_SAML_LOGOUT_RESPONSE;

                if ($this->requestType === self::REQUEST_TYPE_LOGOUT) {
                    $path = self::REDIRECT_SAML_LOGOUT_REQUEST;
                }

                return new RedirectResponse($path);
            });

        return $mock;
    }

    /**
     * @return LogoutRequest
     */
    private function getLogoutRequestMessage()
    {
        return $this->getMockBuilder(LogoutRequest::class)->getMock();
    }

    /**
     * @return Status
     */
    private function getMessageStatus()
    {
        $status = new Status();
        $status->setStatusCode(new StatusCode($this->logoutResponseMessageStatusCode));

        return $status;
    }

    /**
     * @return LogoutResponse
     */
    private function getLogoutResponseMessage()
    {
        $mock = $this->getMockBuilder(LogoutResponse::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getStatus')
            ->willReturnCallback(function () {
                return $this->getMessageStatus();
            });

        return $mock;
    }

    /**
     * @return SsoStateStoreInterface
     */
    private function getSsoStateStore()
    {
        $mock = $this->getMockBuilder(SsoStateStoreInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->getSsoState());

        return $mock;
    }

    /**
     * @return SsoState
     */
    private function getSsoState()
    {
        $mock = $this->getMockBuilder(SsoState::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getSsoSessions')
            ->willReturnCallback(function () {
                if ($this->returnSession) {
                    return [$this->getMockBuilder(SsoSessionState::class)->getMock()];
                }

                return null;
            });

        return $mock;
    }

    /**
     * @return RouterInterface
     */
    private function getRouter()
    {
        $mock = $this->getMockBuilder(RouterInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('generate')
            ->willReturnCallback(function () {
                return self::REDIRECT_HOMEPAGE;
            });

        return $mock;
    }

    /**
     * @return LogoutMessageContextFactory
     */
    private function getLogoutMessageContextFactory()
    {
        $mock = $this->getMockBuilder(LogoutMessageContextFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->any())
            ->method('request')
            ->willReturn($this->getMockBuilder(MessageContext::class)->getMock());

        $mock
            ->expects($this->any())
            ->method('response')
            ->willReturn($this->getMockBuilder(MessageContext::class)->getMock());

        return $mock;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        $mock = $this->getMockBuilder(Request::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getSession')
            ->willReturn($this->getSession());

        return $mock;
    }

    /**
     * @return SessionInterface
     */
    private function getSession()
    {
        $mock = $this->getMockBuilder(SessionInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('invalidate')
            ->willReturnCallback(function () {
                $this->wasLoggedOut = true;
            });

        return $mock;
    }

    private function thenLogoutMessageShouldBeSentToIP()
    {
        $this->assertEquals(self::REDIRECT_SAML_LOGOUT_REQUEST, $this->result->getTargetUrl());
    }

    private function thenIShouldBeSuccessfullyLoggedOut()
    {
        $this->assertTrue($this->wasLoggedOut);
    }

    private function thenResponseAboutSuccessfullyLogoutShouldBeSentToIP()
    {
        $this->assertEquals(self::REDIRECT_SAML_LOGOUT_RESPONSE, $this->result->getTargetUrl());
    }

    private function thenSystemShouldRedirectIPToDefinedPage()
    {
        $this->assertEquals(self::REDIRECT_HOMEPAGE, $this->result->getTargetUrl());
    }
}
