<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SpBundle\Store\EntityDescriptor\CompositeEntityDescriptorStore;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;

class CompositeEntityDescriptorStoreTest extends \PHPUnit_Framework_TestCase
{
    /** @var CompositeEntityDescriptorStore */
    private $compositeEntityDescriptorStore;

    /** @test */
    public function itAllowsToClearAllStores()
    {
        $this->iHaveStoreWithOneEntityDescriptor();
        $this->whenIRemoveAllEntityDescriptors();
        $this->thenThereWillBeNoEntityDescriptorInStore();
    }

    private function iHaveStoreWithOneEntityDescriptor()
    {
        $entityDescriptor = $this->prophesize(EntityDescriptor::class);
        $store = $this->prophesize(EntityDescriptorStoreInterface::class);
        $store->all()->willReturn([$entityDescriptor->reveal()]);

        $this->compositeEntityDescriptorStore = new CompositeEntityDescriptorStore([$store->reveal()]);

        $this->assertGreaterThan(0, $this->compositeEntityDescriptorStore->all());
    }

    private function whenIRemoveAllEntityDescriptors()
    {
        $this->compositeEntityDescriptorStore->removeAll();
    }

    private function thenThereWillBeNoEntityDescriptorInStore()
    {
        $this->assertCount(0, $this->compositeEntityDescriptorStore->all());
    }
}
