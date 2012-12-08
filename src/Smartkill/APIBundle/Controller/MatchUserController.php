<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\NoResultException;

class MatchUserController extends Controller {
	
    public function positionAction() {
    	$session = $this->checkSession();
		if ($session instanceof Response) {
			return $session;
		}
		
		$request = $this -> getRequest();
		
		$user  = $request->request->get('user');
		$match = $request->request->get('match');
		$lat   = $request->request->get('lat');
		$lng   = $request->request->get('lng');
		
		// aktualizacja pozycji użytkownika
		if (!$this->updatePositionAction($user, $match, $lat, $lng)) {
			return $this -> jsonResponse(array('msg'=>'User invalid'),'error');
		}
		
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:MatchUser');
		$query = $repository->createQueryBuilder('mu')
			->where('mu.match = :match')
			->setParameter('match', $match)
			->orderBy('mu.user', 'ASC')
			->getQuery();
		$positions = $query->getResult();
		
		if (!$positions) {
			return $this -> jsonResponse(array('msg'=>'Match invalid'),'error');
		}
		
		$serializer = $this->get('serializer');
		return $this -> jsonResponse($serializer->serialize(array('status' => 'success', 'positions'=>$positions), 'json'));
    }
    
    public function updatePositionAction($user, $match, $lat, $lng) {
    	$em = $this->getDoctrine()->getManager();
    	
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
		if ($session instanceof Response) {
			return $session;
		}
		
		$request = $this->getRequest();
	
		$user  = $session->getUser();
		$match = $request->request->get('match');
	
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:MatchUser');
		$result = $repository->findOneBy(array('match' => $match, 'user' => $user->getId()));
		
		return $this -> jsonResponse(array('is_joined'=>($result ? true : false)));
	}
	
	public function userGamesAction() {
		$session = $this->checkSession();
		if ($session instanceof Response) {
			return $session;
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
	    	$serializer = $this->get('serializer');
	    	return $this -> jsonResponse($serializer->serialize(array('status' => 'success', 'games'=>$games), 'json'));
	    } catch (NoResultException $e) {
	    	return $this -> jsonResponse(array('msg'=>'List is empty'),'error');
	    }
	}
	
	private function checkSession() {
		$request = $this -> getRequest();
		$repo = $this->getDoctrine()->getRepository('SmartkillAPIBundle:Session');
		
		$session = $repo -> findOneById($request->request->get('id'));
		
		if (!$session) {
			return $this -> jsonResponse(array('msg'=>'Session not found'),'error');
		}
		
		return $session;
	}
    
    private function jsonResponse($args = array(), $status = 'success') {
    	if (is_array($args)) {
    		$args = json_encode(array('status' => $status) + $args);
    	}
    	
		$response = new Response($args);
		$response->headers->set('Content-Type', 'application/json');
		
		return $response;
	}
}
