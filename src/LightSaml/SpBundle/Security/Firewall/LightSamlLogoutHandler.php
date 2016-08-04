<?php

namespace LightSaml\SpBundle\Security\Firewall;

use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\SamlConstants;
use LightSaml\SpBundle\Model\Protocol\LogoutMessageContextFactory;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\Store\Sso\SsoStateStoreInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LightSamlLogoutHandler.
 */
class LightSamlLogoutHandler implements LogoutSuccessHandlerInterface
{
    /** @var BindingFactoryInterface */
    private $bindingFactory;
    /** @var SessionInterface */
    private $session;
    /** @var SsoStateStoreInterface */
    private $ssoStateStore;
    /** @var RouterInterface */
    private $router;
    /** @var LogoutMessageContextFactory */
    private $logoutMessageContextFactory;

    /**
     * LightSamlLogoutHandler constructor.
     *
     * @param BindingFactoryInterface     $bindingFactory
     * @param SsoStateStoreInterface      $ssoStateStore
     * @param RouterInterface             $router
     * @param LogoutMessageContextFactory $logoutMessageContextFactory
     */
    public function __construct(
        BindingFactoryInterface $bindingFactory,
        SsoStateStoreInterface $ssoStateStore,
        RouterInterface $router,
        LogoutMessageContextFactory $logoutMessageContextFactory
    ) {
        $this->bindingFactory = $bindingFactory;
        $this->ssoStateStore = $ssoStateStore;
        $this->router = $router;
        $this->logoutMessageContextFactory = $logoutMessageContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $this->session = $request->getSession();
        $samlMessage = $this->getIncomingMessage($request);

        if (null === $samlMessage) {
            return $this->sendLogoutRequestToIP();
        }

        if ($samlMessage instanceof LogoutResponse) {
            return $this->handleLogoutResponseFromIP($samlMessage);
        } elseif ($samlMessage instanceof LogoutRequest) {
            return $this->handleLogoutRequestFromIP($samlMessage);
        }

        throw new InvalidSamlMessageForLogoutException($samlMessage);
    }

    /**
     * @return Response
     */
    private function sendLogoutRequestToIP()
    {
        $sessions = $this->ssoStateStore->get()->getSsoSessions();

        if (count($sessions) === 0) {
            return $this->createRedirectToHomepage();
        }

        /* @var $session SsoSessionState */
        $session = $sessions[count($sessions) - 1];
        $context = $this->logoutMessageContextFactory->request($session);

        return $this->bindingFactory
            ->create($context->getBindingType())
            ->send($context);
    }

    /**
     * @param LogoutRequest $samlRequest
     *
     * @return Response
     */
    private function sendLogoutResponseToIP(LogoutRequest $samlRequest)
    {
        $context = $this->logoutMessageContextFactory->response($samlRequest);

        return $this->bindingFactory
            ->create($context->getBindingType())
            ->send($context);
    }

    /**
     * @param LogoutResponse $logoutResponse
     *
     * @return RedirectResponse
     */
    private function handleLogoutResponseFromIP(LogoutResponse $logoutResponse)
    {
        $status = $logoutResponse->getStatus();
        $code = $status->getStatusCode() ? $status->getStatusCode()->getValue() : null;

        if (!in_array($code, [SamlConstants::STATUS_PARTIAL_LOGOUT, SamlConstants::STATUS_SUCCESS])) {
            throw new LightSamlLogoutException($logoutResponse);
        }

        $this->invalidateSession();

        return $this->createRedirectToHomepage();
    }

    /**
     * @param LogoutRequest $logoutRequest
     *
     * @return Response
     */
    private function handleLogoutRequestFromIP(LogoutRequest $logoutRequest)
    {
        $response = $this->sendLogoutResponseToIP($logoutRequest);
        $this->invalidateSession();

        return $response;
    }

    /**
     * @param Request $request
     * @param string  $bindingType - see SamlConstants::Binding_*
     *
     * @return \LightSaml\Model\Protocol\SamlMessage|null
     */
    private function getSamlMessage(Request $request, $bindingType)
    {
        $messageContext = new MessageContext();
        $binding = $this->bindingFactory->create($bindingType);
        $binding->receive($request, $messageContext);

        return $messageContext->getMessage();
    }

    private function invalidateSession()
    {
        $this->session->invalidate();
        $this->session = null;
    }

    /**
     * @return RedirectResponse
     */
    private function createRedirectToHomepage()
    {
        return new RedirectResponse($this->router->generate('homepage'));
    }

    /**
     * @param $request
     *
     * @return \LightSaml\Model\Protocol\SamlMessage|null
     */
    private function getIncomingMessage($request)
    {
        $bindingType = $this->bindingFactory->detectBindingType($request);

        if (null === $bindingType) {
            return;
        }

        return $this->getSamlMessage($request, $bindingType);
    }
}
