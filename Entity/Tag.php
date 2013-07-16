<?php

namespace ElsassSeeraiwer\ESArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @ORM\Table(name="es_article_tag")
 * @ORM\Entity(repositoryClass="ElsassSeeraiwer\ESArticleBundle\Entity\TagRepository")
 */
class Tag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Article", inversedBy="tags")
     * @ORM\JoinTable(name="es_article_tag_join")
     */
    private $articles;

    public function __construct() {
        $this->articles = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $this->wd_remove_accents($name);
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add articles
     *
     * @param \ElsassSeeraiwer\ESArticleBundle\Entity\Article $articles
     * @return Tag
     */
    public function addArticle(\ElsassSeeraiwer\ESArticleBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \ElsassSeeraiwer\ESArticleBundle\Entity\Article $articles
     */
    public function removeArticle(\ElsassSeeraiwer\ESArticleBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    private function wd_remove_accents($str, $charset='utf-8')
    {
        $str = ereg_replace(' ','-',$str);
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        
        $str= strtolower($str);

        return $str;
    }
}