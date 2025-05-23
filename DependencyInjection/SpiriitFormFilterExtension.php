<?php

/*
 * This file is part of the composer-write-changelogs project.
 *
 * (c) Dev Spiriit <dev@spiriit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiriit\Bundle\FormFilterBundle\DependencyInjection;

use Spiriit\Bundle\FormFilterBundle\Filter\FilterOperands;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class SpiriitFormFilterExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('form.xml');

        foreach ($config['listeners'] as $name => $enable) {
            if ($enable) {
                $loader->load(sprintf('%s.xml', $name));
            }
        }

        if (isset($config['force_case_insensitivity'])) {
            $filterPrepareDef = $container->getDefinition('spiriit_form_filter.filter_prepare');
            $filterPrepareDef->addMethodCall(
                'setForceCaseInsensitivity',
                [$config['force_case_insensitivity']]
            );
        }

        if (isset($config['encoding'])) {
            $filterPrepareDef = $container->getDefinition('spiriit_form_filter.filter_prepare');
            $filterPrepareDef->addMethodCall(
                'setEncoding',
                [$config['encoding']]
            );
        }

        $container->setParameter('spiriit_form_filter.where_method', $config['where_method']);
        $container->setParameter('spiriit_form_filter.text.condition_pattern', FilterOperands::getStringOperandByString($config['condition_pattern']));
    }
}
