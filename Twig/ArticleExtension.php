<?php

namespace ElsassSeeraiwer\ESArticleBundle\Twig;

use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use \Twig_Filter_Method;
use \Twig_Function_Method;

class ArticleExtension extends \Twig_Extension
{
    private $request;
    private $container;
	private $em;

    public function onKernelRequest(GetResponseEvent $event) {
        if ($event->getRequestType() === HttpKernel::MASTER_REQUEST) {
            $this->request = $event->getRequest();
        }
    }

    public function getFilters()
    {
        return array(
        	'article_content' => new Twig_Filter_Method($this, 'getArticleContent', array('is_safe' => array('html'))),
            'article' => new Twig_Filter_Method($this, 'getArticle', array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }


    public function getFunctions()
    {
        return array(
            'article_script' => new Twig_Function_Method($this, 'getESArticleScript', array('needs_environment' => true, 'is_safe' => array('all'))),
        );
    }

    public function getGlobals()
    {
        return array(
            'tinymce' => array(
                //'selector' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.selector'),
                'content_css' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.content_css'),
                'plugin' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.plugin'),
                'toolbar1' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.toolbar1'),
                'toolbar2' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.toolbar2'),
                'contextmenu' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.contextmenu'),
                'tools' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.tools'),
                'nonbreaking_force_tab' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.nonbreaking_force_tab'),
                'save_enablewhendirty' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.save_enablewhendirty'),
                'style_formats' => $this->container->getParameter('elsass_seeraiwer_es_article.tinymce.style_formats'),
            ),
        );
    }

    public function getESArticleScript($env, $authorized_role = '', $jquery = true, $tinymce = true, $tags = true)
    {
        //$content_css = $this->container->getParameter('elsass_seeraiwer_es_article.content_css');
        if($authorized_role == '')$authorized_role = $this->container->getParameter('elsass_seeraiwer_es_article.default_authorized_role');

        $content = $env->render('ElsassSeeraiwerESArticleBundle:ArticleDB:articleScript.html.twig', array(
            'authorized_role'   => $authorized_role,
            //'content_css'       => $content_css,
            'jQuery'            => $jquery,
            'tinyMCE'           => $tinymce,
            'tags'              => $tags
        ));

        return $content;
    }

    public function getArticle($env, $string, $classes = '', $tags = true)
    {
        $article = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findOneBySlug($string);
        if (!$article) {
            $article = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findOneByFixedSlug($string);
            if (!$article) {
                throw $this->createNotFoundException('Article not found');
            }
        }

        $content = $this->getArticleContent($string, $article);

        return $env->render('ElsassSeeraiwerESArticleBundle:ArticleDB:article.html.twig', array(
            'article'           => $article, 
            'content'           => $content,
            'classes'           => $classes,
            'tags'              => $tags,
        ));
    }

    public function getArticleContent($string, $article = null)
    {
        if($article == null)$article = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findOneBySlug($string);

        if (!$article) {
            $article = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findOneByFixedSlug($string);
            if (!$article) {
                throw $this->createNotFoundException('Article not found');
            }
        }

        $trans = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:ArticleTranslation')
            ->findOneBy(array(
                'article'   => $article->getId(),
                'locale'    => $this->request->getLocale()
            ));

    	return $trans->getContent();
    }

    public function getName()
    {
        return 'article_twig_extension';
    }
    
    public function __construct($container, $entityManager)
    {
        $this->container = $container;
        $this->em = $entityManager;
    }
}