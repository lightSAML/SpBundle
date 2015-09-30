<?php

namespace LightSaml\SpBundle\Bridge\Container;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractContainer
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
