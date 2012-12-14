<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository {
	
	public function getPosition(User $user) {
		return $this->createQueryBuilder('u')
			->select('COUNT(u.id)+1')
			->where('u.pointsPrey + u.pointsHunter > ?1')
			->setParameter(1, $user->getPointsSum())
			->getQuery()
			->getSingleScalarResult();
	}
	
}
