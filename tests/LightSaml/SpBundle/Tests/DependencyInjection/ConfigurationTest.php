<?php

namespace LightSaml\SpBundle\Tests\DependencyInjection;

use LightSaml\ClaimTypes;
use LightSaml\SpBundle\DependencyInjection\Configuration;
use LightSaml\SpBundle\Security\User\SimpleUsernameMapper;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_allow_empty_configuration()
    {
        $emptyConfig = array();
        $this->processConfiguration($emptyConfig);
    }

    public function test_allow_set_username_mapper_scalar_array()
    {
        $config = [
            'light_saml_sp' => [
                'username_mapper' => [
                    'a', 'b', 'c',
                ],
            ],
        ];
        $this->processConfiguration($config);
    }

    public function test_sets_default_username_mapper()
    {
        $config = ['light_saml_sp' => []];
        $processedConfig = $this->processConfiguration($config);
        $this->assertArrayHasKey('username_mapper', $processedConfig);
        $this->assertTrue(is_array($processedConfig['username_mapper']));
        $this->assertEquals(
            [
                ClaimTypes::EMAIL_ADDRESS,
                ClaimTypes::ADFS_1_EMAIL,
                ClaimTypes::COMMON_NAME,
                ClaimTypes::WINDOWS_ACCOUNT_NAME,
                'urn:oid:0.9.2342.19200300.100.1.3',
                'uid',
                'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
                SimpleUsernameMapper::NAME_ID,
            ],
            $processedConfig['username_mapper']
        );
    }

    /**
     * @param array $configs
     *
     * @return array
     */
    protected function processConfiguration(array $configs)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }
}
