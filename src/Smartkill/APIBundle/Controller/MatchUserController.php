<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Component\EventDispatcher\Event;

use Doctrine\ORM\NoResultException;

use Smartkill\WebBundle\Entity\MatchUser;
use Smartkill\WebBundle\Entity\EventCatch;

class MatchUserController extends Controller {
	
    public function positionAction() {
    	$session = $this->checkSession();
    	
		if (!$session) {
			return $this->sessionNotFound();
		}
		
		$request = $this -> getRequest();
		
		$match = $request->get('match');
		$lat   = $request->get('lat');
		$lng   = $request->get('lng');
		
		// aktualizacja pozycji użytkownika
		if (!$this->updatePositionAction($session->getUser(), $match, $lat, $lng)) {
			return $this -> errorResponse('User invalid');
		}
		
		$positions = $this->getRepository('SmartkillWebBundle:MatchUser')
			->createQueryBuilder('mu')
			->where('mu.match = :match AND NOT mu.user = :user')
			->setParameter('match', $match)
			->setParameter('user', $session->getUser())
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
	
	public function killUserAction() {
		$session = $this->checkSession();
		
		if (!$session) {
			return $this->sessionNotFound();
		}
	
		$request = $this->getRequest();
		
		$match = $request->get('match');
		$lat   = $request->get('lat');
		$lng   = $request->get('lng');
		$prey  = $session->getUser();
		
		$mu = $this->getRepository('SmartkillWebBundle:MatchUser')
			->findOneBy(array('match' => $match, 'user' => $prey, 'type' => MatchUser::TYPE_PREY));
		
		$mu2 = $this->getRepository('SmartkillWebBundle:MatchUser')
			->findOneBy(array('match' => $match, 'user' => $request->get('hunter'), 'type' => MatchUser::TYPE_HUNTER));
		
		if (!$mu || !$mu2) {
			return $this -> errorResponse('User not in match');
		}
		
		$hunter = $mu2->getUser();
		$match  = $mu2->getMatch();
		
		$event = new EventCatch();
		$event->setMatch($match);
		$event->setHunter($hunter);
		$event->setPrey($prey);
		$event->setLat($lat);
		$event->setLng($lng);
		$event->setDate(new \DateTime());
		
		$em = $this->getManager();
		$em->persist($event);
		$em->flush();
		
		return $this -> jsonResponse();
	}
}
