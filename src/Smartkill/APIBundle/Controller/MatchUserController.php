<?php

namespace Smartkill\APIBundle\Controller;

use Doctrine\ORM\NoResultException;

class MatchUserController extends Controller {
	
    public function positionAction() {
    	$session = $this->checkSession();
    	
		if (!$session) {
			return $this->sessionNotFound();
		}
		
		$request = $this -> getRequest();
		
		$user  = $request->get('user');
		$match = $request->get('match');
		$lat   = $request->get('lat');
		$lng   = $request->get('lng');
		
		// aktualizacja pozycji użytkownika
		if (!$this->updatePositionAction($user, $match, $lat, $lng)) {
			return $this -> errorResponse('User invalid');
		}
		
		$positions = $this->getRepository('SmartkillWebBundle:MatchUser')
			->createQueryBuilder('mu')
			->where('mu.match = :match')
			->setParameter('match', $match)
			->orderBy('mu.user', 'ASC')
			->getQuery()
			->getResult();
		
		if (!$positions) {
			return $this -> errorResponse('Match invalid');
		}
		
		return $this -> jsonResponse(array('positions'=>$positions));
    }
    
    public function updatePositionAction($user, $match, $lat, $lng) {
		$session = $this->checkSession();
    	
		if (!$session) {
			return $this->sessionNotFound();
		}
		
    	$em = $this->getManager();
    	
    	try {
	    	$query = $em->getRepository('SmartkillWebBundle:MatchUser')->createQueryBuilder('mu')
		    	->where('mu.match = :match')
		    	->andWhere('mu.user = :user')
		    	->setParameters(array(
		    		'match' => $match,
		    		'user'  => $user
		    	  ))
		    	->getQuery();
	    	$user = $query->getSingleResult();
    	} catch (NoResultException $e) {
    		return false;
    	}
    	
    	$user->setLat($lat);
    	$user->setLng($lng);
    	$user->setUpdatedAt(new \DateTime());
    	
    	$em->persist($user);
    	$em->flush();
    	
    	return true;
    }
	
	public function isJoinedAction() {
		$session = $this->checkSession();
    	
		if (!$session) {
			return $this->sessionNotFound();
		}
		
		$request = $this->getRequest();
		
		$user  = $session->getUser();
		$match = $request->get('match');
		
		$result = $this->getRepository('SmartkillWebBundle:MatchUser')
			->findOneBy(array('match' => $match, 'user' => $user->getId()));
		
		return $this -> jsonResponse(array('is_joined'=> (boolean) $result));
	}
	
	public function userGamesAction() {
		$session = $this->checkSession();
    	
		if (!$session) {
			return $this->sessionNotFound();
		}
		
		$request = $this->getRequest();
	
		$user = $session->getUser();
		$query = $this->getDoctrine()->getManager()
			->createQuery(
				'SELECT m FROM SmartkillWebBundle:MatchUser mu
			 		JOIN SmartkillWebBundle:Match m WITH mu.match = m.id
			 		WHERE mu.user = :id'
			  )
			->setParameter('id', $user->getId());
	    
	    try {
	    	$games = $query->getResult();
	    	return $this -> jsonResponse(array('status' => 'success', 'games'=>$games));
	    } catch (NoResultException $e) {
	    	return $this -> errorResponse('List is empty');
	    }
	}
}
