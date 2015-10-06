<?php

namespace LightSaml\SpBundle\Security\User;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;

interface AttributeMapperInterface
{
    /**
     * @param SamlSpResponseToken $token
     *
     * @return array
     */
    public function getAttributes(SamlSpResponseToken $token);
}
