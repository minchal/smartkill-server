<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SfController;
use Symfony\Component\HttpFoundation\Response;

use Smartkill\APIBundle\Entity\Session;
use Smartkill\WebBundle\Controller\JsonResponse;

class Controller extends SfController {
	
	protected function getManager() {
		return $this->getDoctrine()->getManager();
	}
	
	protected function getRepositiory($repo) {
		return $this->getDoctrine()->getRepository($repo);
	}
	
	protected function checkSession() {
		$request = $this -> getRequest();
		$repo = $this->getDoctrine()->getRepository('SmartkillAPIBundle:Session');
		
		return $repo -> findOneById($request->request->get('id'));
	}
	
	protected function sessionNotFound() {
		return $this -> errorResponse('Session not found');
	}
	
	protected function errorResponse($msg) {
		return $this -> jsonResponse(array('msg' => $msg), 'error');
	}
	
    protected function jsonResponse($args = array(), $status = 'success') {
		if (is_array($args)) {
			$args = array('status' => $status) + $args;
		}
		
		if (is_array($args) || is_object($args)) {
    		$args = $this -> get('serializer') -> serialize($args, 'json');
    	}
    	
		return new JsonResponse($args);
	}
}
