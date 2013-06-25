<?php

namespace ElsassSeeraiwer\ESArticleBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ElsassSeeraiwerESArticleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('elsass_seeraiwer_es_article.config', $config['config']);
        $container->setParameter('elsass_seeraiwer_es_article.domain', $config['domain']);
        $container->setParameter('elsass_seeraiwer_es_article.default_authorized_role', $config['default_authorized_role']);
        $container->setParameter('elsass_seeraiwer_es_article.locales', $config['locales']);

        //$container->setParameter('elsass_seeraiwer_es_article.tinymce.selector', $config['tinymce']['selector']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.content_css', $config['tinymce']['content_css']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.plugin', $config['tinymce']['plugin']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.toolbar1', $config['tinymce']['toolbar1']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.toolbar2', $config['tinymce']['toolbar2']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.contextmenu', $config['tinymce']['contextmenu']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.tools', $config['tinymce']['tools']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.nonbreaking_force_tab', $config['tinymce']['nonbreaking_force_tab']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.save_enablewhendirty', $config['tinymce']['save_enablewhendirty']);
        $container->setParameter('elsass_seeraiwer_es_article.tinymce.style_formats', trim($config['tinymce']['style_formats']));
    }
}
