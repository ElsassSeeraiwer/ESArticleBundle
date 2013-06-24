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

    public function getESArticleScript($env, $authorized_role = 'ROLE_SUPER_ADMIN', $jquery = true, $tinymce = true)
    {
        $config = $this->container->getParameter('elsass_seeraiwer_es_article.config');
        $domain = $this->container->getParameter('elsass_seeraiwer_es_article.domain');
        $content_css = $this->container->getParameter('elsass_seeraiwer_es_article.content_css');

        $content = $env->render('ElsassSeeraiwerESArticleBundle:ArticleDB:articleScript.html.twig', array(
            'authorized_role'   => $authorized_role,
            'selectedConfig'    => $config,
            'selectedDomain'    => $domain,
            'content_css'       => $content_css,
            'jQuery'            => $jquery,
            'tinyMCE'           => $tinymce
        ));

        return $content;
    }

    public function getArticle($env, $string, $classes = '')
    {
        $article = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findOneBySlug($string);

        $content = $this->getArticleContent($string, $article);

        return $env->render('ElsassSeeraiwerESArticleBundle:ArticleDB:article.html.twig', array(
            'article'           => $article, 
            'content'           => $content,
            'classes'           => $classes
        ));
    }

    public function getArticleContent($string, $article = null)
    {
        if($article == null)$article = $this->em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findOneBySlug($string);

        if(!$article)return 'no article called "'.$string.'"';

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