<?php

namespace LightSaml\SpBundle\Tests\Security\Authentication\Provider;

use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\Authentication\Provider\LightsSamlSpAuthenticationProvider;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use LightSaml\SpBundle\Security\User\UsernameMapperInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;

class LightsSamlSpAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_provider_key()
    {
        new LightsSamlSpAuthenticationProvider('main');
    }

    public function test_constructs_with_all_arguments()
    {
        new LightsSamlSpAuthenticationProvider(
            'main',
            $this->getUserProviderMock(),
            false,
            $this->getUserCheckerMock(),
            $this->getUsernameMapperMock(),
            $this->getUserCreatorMock(),
            $this->getAttributeMapperMock()
        );
    }

    public function test_supports_saml_sp_response_token()
    {
        $provider = new LightsSamlSpAuthenticationProvider($providerKey = 'main');
        $this->assertTrue($provider->supports(new SamlSpResponseToken(new Response(), $providerKey)));
    }

    public function test_does_not_support_non_saml_sp_response_token()
    {
        $provider = new LightsSamlSpAuthenticationProvider($providerKey = 'main');
        $this->assertFalse($provider->supports($this->getMock(TokenInterface::class)));
    }

    public function test_creates_authenticated_token_with_user_and_his_roles()
    {
        $provider = new LightsSamlSpAuthenticationProvider(
            $providerKey = 'main',
            $userProviderMock = $this->getUserProviderMock(),
            false,
            null,
            $usernameMapperMock = $this->getUsernameMapperMock()
        );

        $user = $this->getUserMock();
        $user->expects($this->any())
            ->method('getRoles')
            ->willReturn($expectedRoles = ['foo', 'bar']);

        $usernameMapperMock->expects($this->any())
            ->method('getUsername')
            ->willReturn($expectedUsername = 'some.username');

        $userProviderMock->expects($this->any())
            ->method('loadUserByUsername')
            ->with($expectedUsername)
            ->willReturn($user);

        $token = $provider->authenticate(new SamlSpResponseToken(new Response(), $providerKey));

        $this->assertInstanceOf(SamlSpToken::class, $token);
        $this->assertTrue($token->isAuthenticated());
        $this->assertEquals($expectedRoles, array_map(function (Role $role) {
            return $role->getRole();
        }, $token->getRoles()));
        $this->assertSame($user, $token->getUser());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\User\UserCheckerInterface
     */
    private function getUserCheckerMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\User\UserInterface
     */
    private function getUserMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private function getUserProviderMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\SpBundle\Security\User\UsernameMapperInterface
     */
    private function getUsernameMapperMock()
    {
        return $this->getMock(UsernameMapperInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\SpBundle\Security\User\UserCreatorInterface
     */
    private function getUserCreatorMock()
    {
        return $this->getMock(UserCreatorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\SpBundle\Security\User\AttributeMapperInterface
     */
    private function getAttributeMapperMock()
    {
        return $this->getMock(AttributeMapperInterface::class);
    }
}
