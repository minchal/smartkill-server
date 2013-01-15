<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Smartkill\WebBundle\Entity\Match;

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
		
		return $this -> jsonResponse(array('matches'=>$matches));
    }
    
    public function startAction() {
    	$session = $this->checkSession();
    	
    	if (!$session) {
    		return $this->sessionNotFound();
    	}
    
    	$match = $this->getRepository('SmartkillWebBundle:Match')->find($this->getRequest()->get('match'));
    	
    	if (!$match) {
    		return $this -> errorResponse('Match not found');
    	}
    	
    	if($session->getUser() != $match->getCreatedBy()) {
    		return $this -> errorResponse('Access denied');
    	}
    	
    	if ($match->getStatus() != Match::PLANED) {
			return $this -> errorResponse('Match already started!');
		}
    	
    	$match -> start($this->getManager());
    	
    	return $this -> jsonResponse();
    }
    
    public function finishAction() {
    	$session = $this->checkSession();
    	
    	if (!$session) {
    		return $this->sessionNotFound();
    	}
    
    	$match = $this->getRepository('SmartkillWebBundle:Match')->find($this->getRequest()->get('match'));
    	
    	if (!$match) {
    		return $this -> errorResponse('Match not found');
    	}
    	
    	if($session->getUser() != $match->getCreatedBy()) {
    		return $this -> errorResponse('Access denied');
    	}
    	
    	if ($match->getStatus() != Match::GOINGON) {
			return $this -> errorResponse('Match not started yet!');
		}
    	
    	$match -> finish($this->getManager());
    	
    	return $this -> jsonResponse();
    }
}
