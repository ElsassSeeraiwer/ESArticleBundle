<?php

namespace ElsassSeeraiwer\ESArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
	public function getAllByCount()
	{
		$em = $this->getEntityManager();

		$query = $em->createQuery(
			"SELECT t, SIZE(t.articles) as a
			FROM ElsassSeeraiwerESArticleBundle:Tag t
			LEFT JOIN t.articles art
			WHERE SIZE(t.articles) > 0 AND art.status = 'published'
			ORDER BY a DESC");

		$result = $query->getResult();

		return $result;
	}

	public function getAllUnpublishedByCount()
	{
		$em = $this->getEntityManager();

		$query = $em->createQuery(
			"SELECT t, SIZE(t.articles) as a
			FROM ElsassSeeraiwerESArticleBundle:Tag t
			LEFT JOIN t.articles art
			ORDER BY a DESC");

		$result = $query->getResult();

		return $result;
	}
}
