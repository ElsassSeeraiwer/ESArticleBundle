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
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template("ElsassSeeraiwerESArticleBundle:Main:embedded.html.twig")
     */
    public function embeddedByKeyAction(Article $article, $jQuery = true, $tinyMCE = true)
    {
        return $this->embeddedProcess($article, $jQuery, $tinyMCE);
    }

    /**
     * @Route("/{slug}/embedded/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template("ElsassSeeraiwerESArticleBundle:Main:embedded.html.twig")
     */
    public function embeddedBySlugAction(Article $article, $jQuery = true, $tinyMCE = true)
    {
        return $this->embeddedProcess($article, $jQuery, $tinyMCE);
    }

    private function embeddedProcess(Article $article, $jQuery = true, $tinyMCE = true)
    {
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
            'content_css'       => $content_css
        );
    }
}
