<?php

namespace ElsassSeeraiwer\ESArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
	public function findAllOrderBy($orderField, $orderWay)
	{
		$em = $this->getEntityManager();

		$query = $em->createQuery(
			"SELECT a 
			FROM ElsassSeeraiwerESArticleBundle:Article a 
			LEFT JOIN a.tags t
			ORDER BY a.".$orderField." ".$orderWay);

		$result = $query->getResult();

		return $result;
	}
}
