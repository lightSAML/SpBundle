<?php

namespace LightSaml\SpBundle\Tests\Security\Authentication\Token;

use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Component\Security\Core\User\User;

class SamlSpTokenTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_roles_array_provider_key_string_attributes_array_and_user()
    {
        new SamlSpToken(
            ['ROLE_USER'],
            'main',
            ['a', 'b'],
            new User('username', '')
        );
    }

    public function test_returns_empty_credentials()
    {
        $token = new SamlSpToken([], 'main', [], null);
        $this->assertEquals('', $token->getCredentials());
    }

    public function test_returns_provider_key_given_in_constructor()
    {
        $token = new SamlSpToken([], $expectedProviderKey = 'main', [], null);
        $this->assertEquals($expectedProviderKey, $token->getProviderKey());
    }
}
