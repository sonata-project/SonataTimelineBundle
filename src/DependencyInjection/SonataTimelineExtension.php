<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\DependencyInjection;

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector as DeprecatedDoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SonataTimelineExtension.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataTimelineExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $config = $this->addDefaults($config);

        if ('orm' !== $config['manager_type']) {
            throw new \RuntimeException('Only ORM manager is implemented');
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('timeline.xml');
        $loader->load(sprintf('timeline_%s.xml', $config['manager_type']));

        $this->configureClass($config, $container);

        if (isset($bundles['SonataDoctrineBundle'])) {
            $this->registerSonataDoctrineMapping($config);
        } else {
            // NEXT MAJOR: Remove next line and throw error when not registering SonataDoctrineBundle
            $this->registerDoctrineMapping($config);
        }
    }

    /**
     * @return array
     */
    public function addDefaults(array $config)
    {
        if ('orm' === $config['manager_type']) {
            $modelType = 'Entity';
        } elseif ('mongodb' === $config['manager_type']) {
            $modelType = 'Document';
        }

        $defaultConfig['class']['timeline'] = sprintf('Application\\Sonata\\TimelineBundle\\%s\\Timeline', $modelType);
        $defaultConfig['class']['action'] = sprintf('Application\\Sonata\\TimelineBundle\\%s\\Action', $modelType);
        $defaultConfig['class']['action_component'] = sprintf('Application\\Sonata\\TimelineBundle\\%s\\ActionComponent', $modelType);
        $defaultConfig['class']['component'] = sprintf('Application\\Sonata\\TimelineBundle\\%s\\Component', $modelType);

        return array_replace_recursive($defaultConfig, $config);
    }

    /**
     * @param array $config
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        if ('orm' === $config['manager_type']) {
            $modelType = 'entity';
        } elseif ('mongodb' === $config['manager_type']) {
            $modelType = 'document';
        }

        $container->setParameter(sprintf('sonata.timeline.admin.timeline.%s', $modelType), $config['class']['timeline']);
        $container->setParameter(sprintf('sonata.timeline.admin.action.%s', $modelType), $config['class']['action']);
        $container->setParameter(sprintf('sonata.timeline.admin.action_component.%s', $modelType), $config['class']['action_component']);
        $container->setParameter(sprintf('sonata.timeline.admin.component.%s', $modelType), $config['class']['component']);
        $container->setParameter(sprintf('sonata.timeline.admin.user.%s', $modelType), $config['class']['user']);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     */
    public function registerDoctrineMapping(array $config)
    {
        @trigger_error(
            'Using SonataEasyExtendsBundle is deprecated since sonata-project/timeline-bundle 3.7. Please register SonataDoctrineBundle as a bundle instead.',
            \E_USER_DEPRECATED
        );

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DeprecatedDoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['timeline'], 'mapManyToOne', [
            'fieldName' => 'action',
            'targetEntity' => $config['class']['action'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => 'timelines',
            'joinColumns' => [
                [
                    'name' => 'action_id',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['timeline'], 'mapManyToOne', [
            'fieldName' => 'subject',
            'targetEntity' => $config['class']['component'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'subject_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['action'], 'mapOneToMany', [
             'fieldName' => 'actionComponents',
             'targetEntity' => $config['class']['action_component'],
             'cascade' => [
                 1 => 'persist',
             ],
             'mappedBy' => 'action',
        ]);

        $collector->addAssociation($config['class']['action'], 'mapOneToMany', [
             'fieldName' => 'timelines',
             'targetEntity' => $config['class']['timeline'],
             'cascade' => [],
             'mappedBy' => 'action',
        ]);

        $collector->addAssociation($config['class']['action_component'], 'mapManyToOne', [
            'fieldName' => 'action',
            'targetEntity' => $config['class']['action'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => 'actionComponents',
            'joinColumns' => [
                [
                    'name' => 'action_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['action_component'], 'mapManyToOne', [
            'fieldName' => 'component',
            'targetEntity' => $config['class']['component'],
            'cascade' => [],
            'mappedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'component_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);
    }

    private function registerSonataDoctrineMapping(array $config): void
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $componentOptions = OptionsBuilder::createManyToOne('component', $config['class']['component'])
            ->addJoin([
                'name' => 'component_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ]);

        $collector->addAssociation(
            $config['class']['timeline'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('action', $config['class']['action'])
                ->inversedBy('timelines')
                ->addJoin([
                    'name' => 'action_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['timeline'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('subject', $config['class']['component'])
                ->addJoin([
                    'name' => 'subject_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );

        $collector->addAssociation(
            $config['class']['action'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('actionComponents', $config['class']['action_component'])
                ->cascade(['persist'])
                ->mappedBy('action')
        );

        $collector->addAssociation(
            $config['class']['action'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('timelines', $config['class']['timeline'])
                ->mappedBy('action')
        );

        $collector->addAssociation(
            $config['class']['action_component'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('action', $config['class']['action'])
                ->inversedBy('actionComponents')
                ->addJoin([
                    'name' => 'action_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ])
        );

        $collector->addAssociation($config['class']['action_component'], 'mapManyToOne', $componentOptions);
    }
}
