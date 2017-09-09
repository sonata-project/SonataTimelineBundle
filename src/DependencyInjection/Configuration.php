<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * @author Alexander <iam.asm89@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sonata_timeline');

        $supportedManagerTypes = array('orm', 'mongodb');

        $rootNode
            ->children()
                ->scalarNode('manager_type')
                    ->defaultValue('orm')
                    ->validate()
                        ->ifNotInArray($supportedManagerTypes)
                        ->thenInvalid('The manager type %s is not supported. Please choose one of '.json_encode($supportedManagerTypes))
                    ->end()
                ->end()
                ->arrayNode('class')
                    ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return isset($v['actionComponent']);
                        })
                        ->then(function ($v) {
                            $v['action_component'] = $v['actionComponent'];
                            unset($v['actionComponent']);

                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('component')->defaultValue('%spy_timeline.class.component%')->cannotBeEmpty()->end()
                        ->scalarNode('actionComponent')->end()
                        ->scalarNode('action_component')->defaultValue('%spy_timeline.class.action_component%')->cannotBeEmpty()->end() // fix the actionComponent deprecated parameter ...
                        ->scalarNode('action')->defaultValue('%spy_timeline.class.action%')->cannotBeEmpty()->end()
                        ->scalarNode('timeline')->defaultValue('%spy_timeline.class.timeline%')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
