<?php

namespace LightSaml\SpBundle\Tests\Security\Authentication\Token;

use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpTokenFactory;
use Symfony\Component\Security\Core\User\User;

class SamlSpTokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_wout_arguments()
    {
        new SamlSpTokenFactory();
    }

    public function test_creates_token()
    {
        $factory = new SamlSpTokenFactory();

        $token = $factory->create(
            $providerKey = 'main',
            $attributes = ['a'=>1],
            $user = new User('joe', '', ['ROLE_USER']),
            $responseToken = new SamlSpResponseToken(new Response(), $providerKey)
        );

        $this->assertInstanceOf(SamlSpToken::class, $token);
        $roles = $token->getRoles();
        $this->assertCount(1, $roles);
        $this->assertEquals('ROLE_USER', $roles[0]->getRole());
        $this->assertEquals($attributes, $token->getAttributes());
        $this->assertSame($user, $token->getUser());
    }
}
