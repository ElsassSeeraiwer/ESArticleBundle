<?php

namespace ElsassSeeraiwer\ESArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ElsassSeeraiwer\ESArticleBundle\Entity\Article;

/**
 * @Route("/article")
 */
class MainController extends Controller
{
    /**
     * @Route("/{slug}/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     */
    public function viewAction(Article $article)
    {
    	$Container = $this->get('service_container');

		$config = $Container->getParameter('elsass_seeraiwer_es_article.config');
		$domain = $Container->getParameter('elsass_seeraiwer_es_article.domain');
        $content_css = $Container->getParameter('elsass_seeraiwer_es_article.content_css');

    	return array(
    		'article'			=> $article,
    		'selectedConfig'	=> $config,
    		'selectedDomain'	=> $domain,
            'content_css'       => $content_css
		);
    }

    /**
     * @Route("/{key}/embedded/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article", options={"key" = "key"})
     * @Template("ElsassSeeraiwerESArticleBundle:Main:embedded.html.twig")
     */
    public function embeddedByKeyAction(Article $article)
    {
        return $this->embeddedProcess($article);
    }

    /**
     * @Route("/{slug}/embedded/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template("ElsassSeeraiwerESArticleBundle:Main:embedded.html.twig")
     */
    public function embeddedBySlugAction(Article $article)
    {
        return $this->embeddedProcess($article);
    }

    private function embeddedProcess(Article $article)
    {
        $request = $this->getRequest();
        $jQuery = ($request->attributes->get('jquery') == null)? true : $request->attributes->get('jquery');
        $tinyMCE = ($request->attributes->get('tinymce') == null)? true : $request->attributes->get('tinymce');
        $classes = $request->attributes->get('class');

        $Container = $this->get('service_container');

        $config = $Container->getParameter('elsass_seeraiwer_es_article.config');
        $domain = $Container->getParameter('elsass_seeraiwer_es_article.domain');
        $content_css = $Container->getParameter('elsass_seeraiwer_es_article.content_css');

        return array(
            'article'           => $article,
            'selectedConfig'    => $config,
            'selectedDomain'    => $domain,
            'jQuery'            => $jQuery,
            'tinyMCE'           => $tinyMCE,
            'content_css'       => $content_css,
            'classes'           => $classes
        );
    }
}
