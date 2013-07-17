<?php

namespace ElsassSeeraiwer\ESArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use ElsassSeeraiwer\ESArticleBundle\Entity\Article;
use ElsassSeeraiwer\ESArticleBundle\Entity\Tag;
use ElsassSeeraiwer\ESArticleBundle\Form\ArticleType;
use ElsassSeeraiwer\ESArticleBundle\Entity\ArticleTranslation;

/**
 * @Route("/article")
 */
class ArticleDBController extends Controller
{
    /**
     * @Route("/list/", defaults={"field" = "publicationDate", "way" = "DESC"})
     * @Route("/list/orderby/{field}/{way}/", defaults={"field" = "publicationDate", "way" = "DESC"}, name="elsassseeraiwer_esarticle_articledb_list_ordered")
     * @Template()
     */
    public function listAction($field, $way)
    {
        $em = $this->container->get('doctrine')->getEntityManager('default');

        $articles = $em->getRepository('ElsassSeeraiwerESArticleBundle:Article')->findAllOrderBy($field, $way);
        $tagEntities = $em->getRepository('ElsassSeeraiwerESArticleBundle:Tag')->findAll();
        $taglist = array();
        foreach ($tagEntities as $tagElem) {
            $taglist[] = $tagElem->getName();
        }

        return array(
            'articles'  => $articles,
            'field'     => $field,
            'way'       => $way,
            'taglist'   => $taglist,
        );
    }

    /**
     * @Route("/get/{slug}/")
     * @Template()
     */
    public function getAction($slug)
    {
        return array('slug' => $slug);
    }

    /**
     * @Route("/add/")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $user = $this->getUser();

        $article = new Article();
        $article->setStatus('draft');
        $article->setFirstUsername($user->getUsername());
        $article->setLastUsername($user->getUsername());
        $article->setPublicationDate(new \DateTime('now'));

        $form = $this->createForm(new ArticleType(), $article, array(
            'action' => $this->generateUrl('elsassseeraiwer_esarticle_articledb_add'),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
        
            $Container = $this->get('service_container');
            $locales = $Container->getParameter('elsass_seeraiwer_es_article.locales');

            $this->createLocaleTranslations($article, $locales);

            return $this->redirect($this->generateUrl('elsassseeraiwer_esarticle_articledb_list'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/delete/{slug}/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function removeAction(Request $request, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $trans = $article->getTrans();
        
        $em->remove($article);

        foreach ($trans as $translation) {
            $em->remove($translation);
        }

        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{slug}/content/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function modifyContentAction(Request $request, Article $article)
    {
        $content = $this->getRequest()->request->get('content');
        $locale = $this->getRequest()->request->get('locale');
        $user = $this->getUser();

        $articleTranslation = $article->getTransByLocale($locale);
        $articleTranslation->setContent($content);
        $articleTranslation->setLastUsername($user->getUsername());

        $article->setLastUsername($user->getUsername());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($articleTranslation);
        $em->persist($article);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{slug}/publication-date/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function modifyPublicationDateAction(Request $request, Article $article)
    {
        $publicationdate = $this->getRequest()->request->get('publicationdate');
        $user = $this->getUser();

        $article->setPublicationDate(new \DateTime($publicationdate));
        $article->setLastUsername($user->getUsername());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{slug}/title/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function modifyTitleAction(Request $request, Article $article)
    {
        $newTitle = $this->getRequest()->request->get('title');
        $user = $this->getUser();

        $article->setTitle($newTitle);
        $article->setLastUsername($user->getUsername());
        
        $em = $this->getDoctrine()->getManager();
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
        $newStatus = $this->getRequest()->request->get('status');
        $user = $this->getUser();

        $article->setStatus($newStatus);
        $article->setLastUsername($user->getUsername());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return new Response("OK");
    }

    /**
     * @Route("/modify/{slug}/tags/")
     * @ParamConverter("article", class="ElsassSeeraiwerESArticleBundle:Article")
     * @Template()
     * @Method("POST")
     */
    public function modifyTagsAction(Request $request, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $clientTagList = $this->getRequest()->request->get('tags');
        $user = $this->getUser();

        foreach ($clientTagList as $key => $value) 
        {
            $clientTagList[$key] = $this->wd_remove_accents($value);
        }

        $serverTags = $article->getTags();
        $serverTagList = array();

        foreach($serverTags as $serverTag)
        {
            $serverTagName = $serverTag->getName();
            if(!in_array($serverTagName, $clientTagList))
            {
                echo 'del: '.$serverTagName.'<br/>';
                $article->removeTag($serverTag);
                continue;
            }

            $key = array_search($serverTagName, $clientTagList);
            unset($clientTagList[$key]);
        }

        foreach($clientTagList as $clientTagName)
        {
            $newTag = $em->getRepository('ElsassSeeraiwerESArticleBundle:Tag')->findOneByName($clientTagName);

            if(!$newTag)
            {
                $newTag = new Tag();
                $newTag->setName($clientTagName);
                $em->persist($newTag);
            }

            $article->addTag($newTag);
        }

        $article->setLastUsername($user->getUsername());
        
        $em->persist($article);
        $em->flush();

        return new Response("OK");
    }

    protected function createLocaleTranslations(Article $article, Array $locales)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        foreach ($locales as $locale)
        {
            $trans = new ArticleTranslation();
            $trans->setLocale($locale);
            $trans->setContent('<h2>'.$article->getTitle().'</h2>');
            $trans->setArticle($article);
            $trans->setFirstUsername($user->getUsername());
            $trans->setLastUsername($user->getUsername());

            $em->persist($trans);
            $em->flush();
        }
        return true;
    }

    private function wd_remove_accents($str, $charset='utf-8')
    {
        $str = ereg_replace(' ','-',$str);
        $str = ereg_replace('/','-',$str);
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        
        $str= strtolower($str);
        
        return $str;
    }
}
