<?php

/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\SpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function metadataAction()
    {
        $profile = $this->get('ligthsaml.profile.metadata');
        $context = $profile->buildContext();
        $action = $profile->buildAction();

        $action->execute($context);

        return $context->getHttpResponseContext()->getResponse();
    }

    public function discoveryAction(Request $request)
    {
        $params = array();
        $relayState = $request->get('RelayState');
        if (null !== $relayState) {
            $params['RelayState'] = $relayState;
        }

        $parties = $this->get('lightsaml.container.build')->getPartyContainer()->getIdpEntityDescriptorStore()->all();

        if (1 == count($parties)) {
            return $this->redirect($this->generateUrl('lightsaml_sp.login', array_merge($params, ['idp' => $parties[0]->getEntityID()])));
        }

        return $this->render('@LightSamlSp/discovery.html.twig', [
            'parties' => $parties,
            'params' => $params
        ]);
    }

    public function loginAction(Request $request)
    {
        $relayState = $request->get('RelayState');
        $idpEntityId = $request->get('idp');
        if (null === $idpEntityId) {
            $params = array();
            if (null !== $relayState) {
                $params['RelayState'] = $relayState;
            }
            return $this->redirect($this->generateUrl($this->container->getParameter('lightsaml_sp.route.discovery'), $params));
        }

        $profile = $this->get('ligthsaml.profile.login_factory')->get($idpEntityId);
        $context = $profile->buildContext();

        if (null !== $relayState) {
            $context->setRelayState($relayState);
        }

        $action = $profile->buildAction();

        $action->execute($context);

        return $context->getHttpResponseContext()->getResponse();
    }

    public function sessionsAction()
    {
        $ssoState = $this->get('lightsaml.container.build')->getStoreContainer()->getSsoStateStore()->get();

        return $this->render('@LightSamlSp/sessions.html.twig', [
            'sessions' => $ssoState->getSsoSessions(),
        ]);
    }
}
