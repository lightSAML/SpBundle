<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Tests\LightSaml\SpBundle\Tests\Unit\LightSaml\SpBundle\Store\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\SpBundle\Store\Credential\CompositeCredentialStore;
use LightSaml\Store\Credential\CredentialStoreInterface;
use Prophecy\Argument;

class CompositeCredentialStoreTest extends \PHPUnit_Framework_TestCase
{
    /** @var CompositeCredentialStore */
    private $compositeCredentialStore;

    /** @test */
    public function itAllowsToClearAllStores()
    {
        $this->iHaveStoreWithOneCredentialStore();
        $this->whenIRemoveAllCredentialStores();
        $this->thenThereWillBeNoCredentialStoresInStore();
    }

    private function iHaveStoreWithOneCredentialStore()
    {
        $credential = $this->prophesize(CredentialInterface::class);
        $store = $this->prophesize(CredentialStoreInterface::class);
        $store->getByEntityId(Argument::exact('entity'))->willReturn([$credential->reveal()]);

        $this->compositeCredentialStore = new CompositeCredentialStore();
        $this->compositeCredentialStore->add($store->reveal());

        $this->assertCount(1, $this->compositeCredentialStore->getByEntityId('entity'));
    }

    private function whenIRemoveAllCredentialStores()
    {
        $this->compositeCredentialStore->removeAll();
    }

    private function thenThereWillBeNoCredentialStoresInStore()
    {
        $this->assertCount(0, $this->compositeCredentialStore->getByEntityId('entity'));
    }
}
