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
                ->scalarNode('tagpath')->defaultValue('')->end()
                ->scalarNode('tagpathparams')->defaultValue("{'tags': '#tags#'}")->end()
                ->arrayNode('locales')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('en', 'fr'))
                ->end()
                ->arrayNode('tinymce')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('content_css')->prototype('scalar')->end()->end()
                        ->scalarNode('plugin')->defaultValue('hr link table save print anchor searchreplace fullscreen charmap visualblocks image media nonbreaking autolink advlist contextmenu')->cannotBeEmpty()->end()
                        ->scalarNode('toolbar1')->defaultValue('save | undo redo | removeformat | styleselect | fullscreen print | cut copy paste | searchreplace | hr anchor link table charmap visualblocks nonbreaking')->cannotBeEmpty()->end()
                        ->scalarNode('toolbar2')->defaultValue('bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | subscript superscript')->cannotBeEmpty()->end()
                        ->scalarNode('contextmenu')->defaultValue('bold italic underline strikethrough | link image inserttable | cell row column deletetable')->cannotBeEmpty()->end()
                        ->scalarNode('tools')->defaultValue('inserttable')->cannotBeEmpty()->end()
                        ->booleanNode('nonbreaking_force_tab')->defaultTrue()->end()
                        ->booleanNode('save_enablewhendirty')->defaultTrue()->end()
                        ->scalarNode('style_formats')->defaultValue("[
                            {title: 'Titre 1', block: 'h2'},
                            {title: 'Titre 2', block: 'h3'},
                            {title: 'Titre 3', block: 'h4'},
                            {title: 'Important', inline: 'span', styles: {color: '#ff0000'}},
                            {title: 'Paragraphe important', block: 'p', classes: 'important'},
                            {title: 'Paragraphe normal', block: 'p'}
                        ]")->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
