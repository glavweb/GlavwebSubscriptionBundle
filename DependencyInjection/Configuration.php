<?php

namespace Glavweb\SubscriptionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('glavweb_subscription');

        $rootNode
            ->children()
                ->arrayNode('contexts')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
//                            ->scalarNode('success_url')->defaultNull()->end()
//                            ->scalarNode('failure_url')->defaultNull()->end()
                            ->scalarNode('role')->defaultValue('ROLE_ADMIN')->end()
                            ->scalarNode('subject')->defaultValue('Send subscription')->cannotBeEmpty()->end()
                            ->scalarNode('from_email')->cannotBeEmpty()->end()
                            ->scalarNode('from_name')->defaultNull()->end()
                            ->scalarNode('entity_class_name')->cannotBeEmpty()->end()
                            ->scalarNode('entity_title')->cannotBeEmpty()->end()

                            ->arrayNode('templates')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('success')->defaultValue('GlavwebSubscriptionBundle:Subscription:success.html.twig')->cannotBeEmpty()->end()
                                    ->scalarNode('failure')->defaultValue('GlavwebSubscriptionBundle:Subscription:failure.html.twig')->cannotBeEmpty()->end()
                                    ->scalarNode('email_new_subscription')->defaultValue('GlavwebSubscriptionBundle:Subscription:email_new_subscription.html.twig')->cannotBeEmpty()->end()
                                    ->scalarNode('email_event')->defaultValue('GlavwebSubscriptionBundle:Subscription:email_event.html.twig')->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;


        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
