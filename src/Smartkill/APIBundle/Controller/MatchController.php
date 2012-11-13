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
		
		$repo = $this->getDoctrine()->getRepository('SmartkillWebBundle:Match');
		$matches = $repo -> findAll();
		
		if (!$matches) {
			return $this -> jsonResponse(array('msg'=>'Matches not found'),'error');
		}
		
		return $this -> jsonResponse(array('matches'=>$matches));
    }
    
    private function jsonResponse(array $args = array(), $status = 'success') {
		$response = new Response(json_encode(array('status' => $status) + $args));
		$response->headers->set('Content-Type', 'application/json');
		
		return $response;
	}
}
