<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MatchRepository
 */
class MatchRepository extends EntityRepository {
	
	public function getNextUserMatch($user) {
		$query = $this->createQueryBuilder('m')
			->innerJoin('m.players','p','WITH','p.user = :user')
			->andWhere('m.status = :status')
			->andWhere('m.dueDate > :date')
			->orderBy('m.dueDate','ASC')
			->setParameter('user', $user)
			->setParameter('status', Match::PLANED)
			->setParameter('date', new \DateTime())
			->setMaxResults(1)
			->getQuery()
		;
		
		try {
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
	
}
