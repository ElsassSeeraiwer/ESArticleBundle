<?php

namespace ElsassSeeraiwer\ESArticleBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ElsassSeeraiwer\ESArticleBundle\Entity\Article;
use ElsassSeeraiwer\ESArticleBundle\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Util\FileUtils;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/article")
 */
class ArticleController extends ContainerAware
{
    /** @DI\Inject("jms_translation.config_factory") */
    private $configFactory;

    /** @DI\Inject */
    private $request;

    /** @DI\Inject("jms_translation.updater") */
    private $updater;

    /**
     * @Route("/list/", defaults={"field" = "status", "way" = "ASC"})
     * @Route("/list/orderby/{field}/{way}/")
     * @Template()
     */
    public function listAction($field, $way)
    {
        $em = $this->container->get('doctrine')->getEntityManager('default');

        $articles = $em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findAllOrderBy($field, $way);

        return array(
            'articles'  => $articles,
            'field'     => $field,
            'way'       => $way,
        );
    }

    /**
     * @Route("/new/")
     * @Template()
     */
    public function newAction(Request $request)
    {
    	$article = new Article();
    	$article->setStatus('draft');

    	$form = $this->container->get('form.factory')->create(new ArticleType(), $article, array(
            'action' => $this->generateUrl('elsassseeraiwer_esarticle_article_new'),
            'method' => 'POST'
        ));

    	$form->handleRequest($request);

    	if ($form->isValid()) {
            $em = $this->container->get('doctrine')->getEntityManager('default');
		    $em->persist($article);
		    $em->flush();

            $this->createTranslationKey($article->getKey());

	        return new RedirectResponse(
                $this->container->get('router')
                ->generate(
                    'elsassseeraiwer_esarticle_article_list', 
                    array(),
                    UrlGeneratorInterface::ABSOLUTE_PATH
                ), 
                302
            );
	    }

        return array(
        	'form' => $form->createView()
    	);
    }

    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * @Route("/modify/{slug}/title/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function modifyTitleAction(Request $request, Article $article)
    {
        $newTitle = $this->request->request->get('title');

        $article->setTitle($newTitle);
        
        $em = $this->container->get('doctrine')->getEntityManager('default');
        $em->persist($article);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{slug}/status/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function modifyStatusAction(Request $request, Article $article)
    {
        $newStatus = $this->request->request->get('status');

        $article->setStatus($newStatus);
        
        $em = $this->container->get('doctrine')->getEntityManager('default');
        $em->persist($article);
        $em->flush();

        return new Response("OK");
    }

    private function createTranslationKey($key)
    {
		$config = $this->container->getParameter('elsass_seeraiwer_es_article.config');
		$domain = $this->container->getParameter('elsass_seeraiwer_es_article.domain');
		$locales = $this->container->getParameter('elsass_seeraiwer_es_article.locales');
		$locale = $locales[0];
		
		$config = $this->configFactory->getConfig($config, $locale);

        $files = FileUtils::findTranslationFiles($config->getTranslationsDir());
        if (!isset($files[$domain][$locale])) {
            throw new RuntimeException(sprintf('There is no translation file for domain "%s" and locale "%s".', $domain, $locale));
        }

        $locales = array_keys($files[$domain]);

        foreach ($locales as $elocale) {
            list($format, $file) = $files[$domain][$elocale];
            
            $this->updater->addTranslation($file, $format, $domain, $elocale, $key, $key);
        }
    }
}