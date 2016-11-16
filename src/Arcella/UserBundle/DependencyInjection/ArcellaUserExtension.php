<?php

/*
 * This file is part of the Arcella package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arcella\UserBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds the configuration parameters for the user bundle into the container.
 */
class ArcellaUserExtension extends Extension
{
    /**
     * Load the configuration of the user bundle and add it to the container.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $container->setParameter('arcella.user.salt.length', $processedConfig['salt']['length']);
        $container->setParameter('arcella.user.salt.keyspace', $processedConfig['salt']['keyspace']);
    }
}
