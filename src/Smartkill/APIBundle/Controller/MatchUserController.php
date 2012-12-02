<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MatchUserController extends Controller {
	
    public function positionAction() {
		$this->checkSession();
		
		$request = $this -> getRequest();
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:MatchUser');
		$query = $repository->createQueryBuilder('mu')
			->where('mu.user = :user')
			->andWhere('mu.match = :match')
			->setParameters(array(
		    	'user'  => $request->request->get('user_id'),
		    	'match' => $request->request->get('match_id'),
			))
			->getQuery();
		$position = $query->getSingleResult();
		
		if (!$position) {
			return $this -> jsonResponse(array('msg'=>'Position undefinied'),'error');
		}
		
		$serializer = $this->get('serializer');
		return $this -> jsonResponse($serializer->serialize(array('status' => 'success', 'position'=>$position), 'json'));
    }
	
	private function checkSession() {
		$request = $this -> getRequest();
		$repo = $this->getDoctrine()->getRepository('SmartkillAPIBundle:Session');
		
		$session = $repo -> findOneById($request->request->get('id'));
		
		if (!$session) {
			return $this -> jsonResponse(array('msg'=>'Session not found'),'error');
		}
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
