<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class MatchController extends Controller {
	
    public function listingAction() {
		$session = $this->checkSession();
    	
		if (!$session) {
			return $this->sessionNotFound();
		}
		
		$matches = $this->getRepository('SmartkillWebBundle:Match')
			->createQueryBuilder('m')
			->where('m.dueDate > :date')
			->setParameter('date', new \DateTime())
			->orderBy('m.dueDate', 'DESC')
			->getQuery()
			->getResult();
		
		if (!$matches) {
			return $this -> errorResponse('Matches not found');
		}
		
		return $this -> jsonResponse(array('status' => 'success', 'matches'=>$matches));
    }
}
