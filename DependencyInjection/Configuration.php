<?php

namespace ElsassSeeraiwer\ESArticleBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elsass_seeraiwer_es_article');

        $rootNode
            ->children()
                ->scalarNode('config')->defaultValue('app')->end()
                ->scalarNode('domain')->defaultValue('articles')->end()
                ->scalarNode('default_authorized_role')->defaultValue('ROLE_SUPER_ADMIN')->end()
                ->arrayNode('locales')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('en', 'fr'))
                ->end()
                ->arrayNode('content_css')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('tinymce')
                    ->addDefaultsIfNotSet()
                    ->children()
                        //->scalarNode('selector')->defaultValue('article.editable')->cannotBeEmpty()->end()
                        ->scalarNode('content_css')->defaultValue('')->end()
                        ->scalarNode('plugin')->defaultValue('hr link table save print anchor searchreplace fullscreen charmap visualblocks image media nonbreaking autolink advlist contextmenu')->cannotBeEmpty()->end()
                        ->scalarNode('toolbar1')->defaultValue('')->cannotBeEmpty()->end()
                        ->scalarNode('toolbar2')->defaultValue('')->cannotBeEmpty()->end()
                        ->scalarNode('contextmenu')->defaultValue('')->cannotBeEmpty()->end()
                        ->scalarNode('tools')->defaultValue('')->cannotBeEmpty()->end()
                        ->booleanNode('nonbreaking_force_tab')->defaultTrue()->end()
                        ->booleanNode('save_enablewhendirty')->defaultTrue()->end()
                        ->scalarNode('style_formats')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
