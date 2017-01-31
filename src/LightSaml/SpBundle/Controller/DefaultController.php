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

    public function discoveryAction()
    {
        $parties = $this->get('lightsaml.container.build')->getPartyContainer()->getIdpEntityDescriptorStore()->all();

        if (count($parties) == 1) {
            return $this->redirect($this->generateUrl('lightsaml_sp.login', ['idp' => $parties[0]->getEntityID()]));
        }

        return $this->render('LightSamlSpBundle::discovery.html.twig', [
            'parties' => $parties,
        ]);
    }

    public function loginAction(Request $request)
    {
        $idpEntityId = $request->get('idp');
        if (null === $idpEntityId) {
            return $this->redirect($this->generateUrl($this->container->getParameter('lightsaml_sp.route.discovery')));
        }

        $profile = $this->get('ligthsaml.profile.login_factory')->get($idpEntityId);
        $context = $profile->buildContext();
        $action = $profile->buildAction();

        $action->execute($context);

        return $context->getHttpResponseContext()->getResponse();
    }

    public function sessionsAction()
    {
        $ssoState = $this->get('lightsaml.container.build')->getStoreContainer()->getSsoStateStore()->get();

        return $this->render('LightSamlSpBundle::sessions.html.twig', [
            'sessions' => $ssoState->getSsoSessions(),
        ]);
    }
}
