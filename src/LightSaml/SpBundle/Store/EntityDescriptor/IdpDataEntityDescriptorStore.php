<?php
/*
 * This file is part of the LightSAML SP-Bundle package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace LightSaml\SpBundle\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\SpBundle\Metadata\IdpDataMetadataProvider;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;

class IdpDataEntityDescriptorStore implements EntityDescriptorStoreInterface
{
    /** @var IdpDataMetadataProvider */
    private $idpDataMetadataProvider;

    /** @var EntityDescriptor|EntitiesDescriptor */
    private $object;

    public function __construct(IdpDataMetadataProvider $IdpDataMetadataProvider)
    {
        $this->idpDataMetadataProvider = $IdpDataMetadataProvider;
    }

    /**
     * @param string $entityId
     *
     * @return EntityDescriptor|null
     */
    public function get($entityId)
    {
        if (null == $this->object) {
            $this->load();
        }

        if ($this->object instanceof EntityDescriptor) {
            if ($this->object->getEntityID() == $entityId) {
                return $this->object;
            } else {
                return null;
            }
        } else {
            return $this->object->getByEntityId($entityId);
        }
    }

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId)
    {
        return $this->get($entityId) != null;
    }

    /**
     * @return array|EntityDescriptor[]
     */
    public function all()
    {
        if (null == $this->object) {
            $this->load();
        }

        if ($this->object instanceof EntityDescriptor) {
            return [$this->object];
        } else {
            return $this->object->getAllEntityDescriptors();
        }
    }

    private function load()
    {
        $this->object = EntityDescriptor::loadXml($this->idpDataMetadataProvider->getMetadata());
    }
}
