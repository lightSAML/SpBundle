<?php

namespace LightSaml\SpBundle\Tests\DependencyInjection;

use LightSaml\SpBundle\DependencyInjection\Configuration;
use LightSaml\SpBundle\DependencyInjection\LightSamlSpExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class LightSamlSpExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadsWithEmptyConfiguration()
    {
        $configs = array();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $extension->load($configs, $containerBuilder);
    }

    public function loadsServiceProvider()
    {
        return [
            ['security.authentication.listener.lightsaml_sp'],
            ['security.authentication.provider.lightsaml_sp'],
            ['lightsaml_sp.username_mapper.simple'],
            ['lightsaml_sp.token_factory'],
        ];
    }

    /** @dataProvider loadsServiceProvider
     * @param $serviceId
     */
    public function testLoadsService($serviceId)
    {
        $configs = array();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition($serviceId));
    }

    /** @dataProvider loadsIdpDataMetadataProviderConfig
     * @param $parameter
     * @param $expected
     */
    public function testLoadsIdpDataMetadataProviderConfig($parameter, $expected)
    {
        $configs = [
            'light_saml_sp' => [
                'idp_data_metadata_provider' => [
                    'idp_data_url' => 'idp-data',
                    'domain_resolver_url' => 'domain-resolver'
                ]
            ],
        ];
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension = new LightSamlSpExtension();
        $extension->load($configs, $containerBuilder);

        $idpDataMetadataProviderConfigParameter = sprintf(
            '%s.%s.%s',
            Configuration::CONFIGURATION_NAME,
            Configuration::IDP_DATA_METADATA_PROVIDER,
            $parameter
        );

        $this->assertEquals($expected, $containerBuilder->getParameter($idpDataMetadataProviderConfigParameter));
    }

    public function loadsIdpDataMetadataProviderConfig()
    {
        return [
            ['enabled', false],
            ['idp_data_url', 'idp-data'],
            ['domain_resolver_url', 'domain-resolver'],
        ];
    }
}
