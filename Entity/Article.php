<?php

namespace ElsassSeeraiwer\ESArticleBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Article
 *
 * @ORM\Table(name="es_article")
 * @ORM\Entity(repositoryClass="ElsassSeeraiwer\ESArticleBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
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
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modify_date", type="datetime")
     */
    private $modifyDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publication_date", type="datetime", nullable=true)
     */
    private $publicationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(length=255, unique=true)
     */
    private $fixedSlug;

    /**
     * @ORM\OneToMany(targetEntity="ArticleTranslation", mappedBy="article")
     **/
    private $trans;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", mappedBy="articles")
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="firstUsername", type="string", length=255, nullable=true)
     */
    private $firstUsername;

    /**
     * @var string
     *
     * @ORM\Column(name="lastUsername", type="string", length=255, nullable=true)
     */
    private $lastUsername;

    public function __construct() {
        $this->trans = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->createDate = new \DateTime();
        $this->modifyDate = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setModifiedValue()
    {
        $this->modifyDate = new \DateTime();
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
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Article
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    
        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set modifyDate
     *
     * @param \DateTime $modifyDate
     * @return Article
     */
    public function setModifyDate($modifyDate)
    {
        $this->modifyDate = $modifyDate;
    
        return $this;
    }

    /**
     * Get modifyDate
     *
     * @return \DateTime 
     */
    public function getModifyDate()
    {
        return $this->modifyDate;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Article
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Add trans
     *
     * @param \ElsassSeeraiwer\ESArticleBundle\Entity\ArticleTranslation $trans
     * @return Article
     */
    public function addTran(\ElsassSeeraiwer\ESArticleBundle\Entity\ArticleTranslation $trans)
    {
        $this->trans[] = $trans;
    
        return $this;
    }

    /**
     * Remove trans
     *
     * @param \ElsassSeeraiwer\ESArticleBundle\Entity\ArticleTranslation $trans
     */
    public function removeTran(\ElsassSeeraiwer\ESArticleBundle\Entity\ArticleTranslation $trans)
    {
        $this->trans->removeElement($trans);
    }

    /**
     * Get trans
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrans()
    {
        return $this->trans;
    }

    /**
     * Get transByLocale
     *
     * @param string $locale
     * @return \ElsassSeeraiwer\ESArticleBundle\Entity\ArticleTranslation
     */
    public function getTransByLocale($locale)
    {
        foreach ($this->trans as $articleTrans) {
            if($articleTrans->getLocale() == $locale)return $articleTrans;
        }
        return false;
    }

    public function getContentAuthorsByLocale($locale)
    {
        $trans = $this->getTransByLocale($locale);

        $authors = array();
        $authors['createdBy'] = $trans->getFirstUsername();
        $authors['modifiedBy'] = $trans->getLastUsername();
        $authors['modifiedDate'] = $trans->getModifyDate();

        return $authors;
    }

    /**
     * Set fixedSlug
     *
     * @param string $fixedSlug
     * @return Article
     */
    public function setFixedSlug($fixedSlug)
    {
        $this->fixedSlug = $fixedSlug;
    
        return $this;
    }

    /**
     * Get fixedSlug
     *
     * @return string 
     */
    public function getFixedSlug()
    {
        return $this->fixedSlug;
    }

    /**
     * Add tags
     *
     * @param \ElsassSeeraiwer\ESArticleBundle\Entity\Tag $tags
     * @return Article
     */
    public function addTag(\ElsassSeeraiwer\ESArticleBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
        $tags->addArticle($this); // synchronously updating inverse side
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \ElsassSeeraiwer\ESArticleBundle\Entity\Tag $tags
     */
    public function removeTag(\ElsassSeeraiwer\ESArticleBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
        $tags->removeArticle($this); // synchronously updating inverse side
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set firstUsername
     *
     * @param string $firstUsername
     * @return Article
     */
    public function setFirstUsername($firstUsername)
    {
        $this->firstUsername = $firstUsername;
    
        return $this;
    }

    /**
     * Get firstUsername
     *
     * @return string 
     */
    public function getFirstUsername()
    {
        return $this->firstUsername;
    }

    /**
     * Set lastUsername
     *
     * @param string $lastUsername
     * @return Article
     */
    public function setLastUsername($lastUsername)
    {
        $this->lastUsername = $lastUsername;
    
        return $this;
    }

    /**
     * Get lastUsername
     *
     * @return string 
     */
    public function getLastUsername()
    {
        return $this->lastUsername;
    }

    /**
     * Set publicationDate
     *
     * @param \DateTime $publicationDate
     * @return Article
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;
    
        return $this;
    }

    /**
     * Get publicationDate
     *
     * @return \DateTime 
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }
}