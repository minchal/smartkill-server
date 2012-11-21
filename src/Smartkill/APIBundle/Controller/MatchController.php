<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MatchController extends Controller {
	
    public function listingAction() {
		$request = $this -> getRequest();
		$repo = $this->getDoctrine()->getRepository('SmartkillAPIBundle:Session');
		
		$session = $repo -> findOneById($request->request->get('id'));
		
		if (!$session) {
			return $this -> jsonResponse(array('msg'=>'Session not found'),'error');
		}
		
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:Match');
		$query = $repository->createQueryBuilder('m')
			->where('m.dueDate > :date')
			->setParameter('date', new \DateTime())
			->orderBy('m.dueDate', 'DESC')
			->getQuery();
		$matches = $query->getResult();
		
		if (!$matches) {
			return $this -> jsonResponse(array('msg'=>'Matches not found'),'error');
		}
		
		$serializer = $this->get('serializer');
		return $this -> jsonResponse($serializer->serialize(array('status' => 'success', 'matches'=>$matches), 'json'));
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
