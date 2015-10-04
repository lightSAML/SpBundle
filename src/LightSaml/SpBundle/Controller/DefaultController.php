<?php

namespace LightSaml\SpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function metadataAction()
    {
        $profile = $this->get('ligth_saml_sp.profile.metadata');
        $context = $profile->buildContext();
        $action = $profile->buildAction();

        $action->execute($context);

        return $context->getHttpResponseContext()->getResponse();
    }

    public function discoveryAction()
    {
        $parties = $this->get('light_saml_sp.container.build')->getPartyContainer()->getIdpEntityDescriptorStore()->all();

        if (count($parties) == 1) {
            return $this->redirectToRoute('light_saml_sp.login', ['idp'=>$parties[0]->getEntityID()]);
        }

        return $this->render('@LightSamlSp/discovery.html.twig', [
            'parties' => $parties,
        ]);
    }

    public function loginAction(Request $request)
    {
        $idpEntityId = $request->get('idp');
        if (null == $idpEntityId) {
            return $this->redirectToRoute($this->container->getParameter('light_saml_sp.route.discovery'));
        }

        $profile = $this->get('ligth_saml_sp.profile.login_factory')->get($idpEntityId);
        $context = $profile->buildContext();
        $action = $profile->buildAction();

        $action->execute($context);

        return $context->getHttpResponseContext()->getResponse();
    }

    public function sessionsAction()
    {
        $ssoState = $this->get('light_saml_sp.container.build')->getStoreContainer()->getSsoStateStore()->get();

        return $this->render('@LightSamlSp/sessions.html.twig', [
            'sessions' => $ssoState->getSsoSessions(),
        ]);
    }
}
