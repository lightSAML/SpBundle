<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Model\Protocol;

use LightSaml\Context\Profile\MessageContext;
use LightSaml\Credential\X509CredentialInterface;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Helper as LightSamlHelper;
use LightSaml\Model\Protocol\SamlMessage;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\Model\XmlDSig\Signature;
use LightSaml\SamlConstants;
use LightSaml\State\Sso\SsoSessionState;
use LightSaml\Store\Credential\CredentialStoreInterface;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Model\XmlDSig\SignatureWriter;

/**
 * Class LogoutMessageFactory.
 */
class LogoutMessageContextFactory
{
    /** @var string */
    private $spEntityId;
    /** @var EntityDescriptorStoreInterface */
    private $entityDescriptorStore;
    /** @var CredentialStoreInterface */
    private $ownCredentialStore;

    /**
     * LogoutMessageFactory constructor.
     *
     * @param string $spEntityId
     * @param EntityDescriptorStoreInterface $entityDescriptorStore
     * @param CredentialStoreInterface $ownCredentialStore
     */
    public function __construct(
        $spEntityId,
        EntityDescriptorStoreInterface $entityDescriptorStore,
        CredentialStoreInterface $ownCredentialStore
    ) {
        $this->spEntityId = $spEntityId;
        $this->entityDescriptorStore = $entityDescriptorStore;
        $this->ownCredentialStore = $ownCredentialStore;
    }

    /**
     * @param SsoSessionState $sessionState
     *
     * @return MessageContext
     */
    public function request(SsoSessionState $sessionState)
    {
        $logoutRequest = new LogoutRequest();
        $this->initMessage($logoutRequest);

        $logoutRequest->setSessionIndex($sessionState->getSessionIndex());
        $logoutRequest->setNameID(new NameID(
            $sessionState->getNameId(),
            $sessionState->getNameIdFormat()
        ));
        $logoutRequest->setDestination($this->getSingleLogoutServiceLocation());

        return $this->surroundWithContext($logoutRequest);
    }

    /**
     * @param LogoutRequest $ipRequest
     *
     * @return MessageContext
     */
    public function response(LogoutRequest $ipRequest)
    {
        $logoutResponse = new LogoutResponse();
        $this->initMessage($logoutResponse);

        $logoutResponse->setRelayState($ipRequest->getRelayState());
        $logoutResponse->setInResponseTo($ipRequest->getID());
        $logoutResponse->setStatus(new Status(
            new StatusCode(SamlConstants::STATUS_SUCCESS)
        ));
        $logoutResponse->setDestination($this->getSingleLogoutServiceLocation());

        return $this->surroundWithContext($logoutResponse);
    }

    /**
     * @param SamlMessage $samlMessage
     */
    private function initMessage(SamlMessage $samlMessage)
    {
        $samlMessage
            ->setID(LightSamlHelper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new Issuer($this->spEntityId))
            ->setSignature($this->getSignature());
    }

    /**
     * @return string
     */
    private function getSingleLogoutServiceLocation()
    {
        return $this->getIdpSsoDescriptor()->getFirstSingleLogoutService()->getLocation();
    }

    /**
     * @return string
     */
    private function getSingleLogoutServiceBindingType()
    {
        return $this->getIdpSsoDescriptor()->getFirstSingleLogoutService()->getBinding();
    }

    /**
     * @return \LightSaml\Model\Metadata\IdpSsoDescriptor|null
     */
    private function getIdpSsoDescriptor()
    {
        return $this->entityDescriptorStore->get(0)->getFirstIdpSsoDescriptor();
    }

    /**
     * @param SamlMessage $samlMessage
     *
     * @return MessageContext
     */
    private function surroundWithContext(SamlMessage $samlMessage)
    {
        $context = new MessageContext();
        $context->setMessage($samlMessage);
        $context->setBindingType($this->getSingleLogoutServiceBindingType());

        return $context;
    }

    /**
     * @return Signature
     */
    private function getSignature()
    {
        /** @var X509CredentialInterface[] $ownCredential */
        $ownCredential = $this->ownCredentialStore->getByEntityId($this->spEntityId);
        return new SignatureWriter($ownCredential[0]->getCertificate(), $ownCredential[0]->getPrivateKey());
    }
}
