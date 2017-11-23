<?php

namespace LightSaml\SpBundle\Tests\LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SpBundle\Metadata\IdpDataMetadataProvider;
use LightSaml\SpBundle\Store\EntityDescriptor\IdpDataEntityDescriptorStore;

class IdpDataEntityDescriptorStoreTest extends \PHPUnit_Framework_TestCase
{
    /** @var IdpDataMetadataProvider */
    private $idpDataMetadataProvider;

    /** @var IdpDataEntityDescriptorStore */
    private $idpDataEntityDescriptorStore;

    /** @var EntityDescriptor */
    private $entityDescriptor;

    /** @test */
    public function itCallsIdpDataForMetadataWhenAskingStoreForAllEntityDescriptors()
    {
        $this->iHaveStoreWithIdpDataMetadataProvider();
        $this->whenIAskStoreForAllEntityDescriptors();
        $this->thenIdpDataShouldBeCalledForMetadata();
        $this->thenEntityIdShouldBeEquals('https://openidp.feide.no');
    }

    /** @test */
    public function itCallsIdpDataForMetadataWhenAskingStoreForGivenEntityId()
    {
        $this->iHaveStoreWithIdpDataMetadataProvider();
        $this->whenIAskStoreForForGivenEntityId('https://openidp.feide.no');
        $this->thenIdpDataShouldBeCalledForMetadata();
        $this->thenEntityIdShouldBeEquals('https://openidp.feide.no');
    }

    private function iHaveStoreWithIdpDataMetadataProvider()
    {
        $this->idpDataMetadataProvider = $this->prophesize(IdpDataMetadataProvider::class);
        $this->idpDataMetadataProvider->getMetadata()->willReturn(file_get_contents(
            __DIR__ . '/../../../../../../../../../vendor/lightsaml/lightsaml/web/sp/openidp.feide.no.xml'
        ));

        $this->idpDataEntityDescriptorStore = new IdpDataEntityDescriptorStore(
            $this->idpDataMetadataProvider->reveal()
        );
    }

    private function whenIAskStoreForAllEntityDescriptors()
    {
        $this->entityDescriptor = current($this->idpDataEntityDescriptorStore->all());
    }

    private function whenIAskStoreForForGivenEntityId($entityId)
    {
        $this->entityDescriptor = $this->idpDataEntityDescriptorStore->get($entityId);
    }

    private function thenIdpDataShouldBeCalledForMetadata()
    {
        $this->idpDataMetadataProvider->getMetadata()->shouldHaveBeenCalled();
    }

    private function thenEntityIdShouldBeEquals($entityId)
    {
        $this->assertEquals($entityId, $this->entityDescriptor->getEntityID());
    }
}
