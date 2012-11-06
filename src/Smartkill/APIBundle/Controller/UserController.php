<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Smartkill\APIBundle\Entity\Session;

class UserController extends Controller {
	
    public function loginAction() {
		$request = $this -> getRequest();
		$repo = $this->getDoctrine()->getRepository('SmartkillWebBundle:User');
		
		$user = $repo -> findOneByUsername($request->request->get('username'));
		
		if (!$user) {
			return $this -> jsonResponse(array('msg'=>'User not found'), 'error');
		}
		
		$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		$password = $encoder->encodePassword($request->request->get('password'), $user->getSalt());
		
		if ($user->getPassword()!=$password) {
			return $this -> jsonResponse(array('msg'=>'User not found'), 'error');
		}
		
		$session = new Session();
		$session -> setUser($user);
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($session);
		$em->flush();
		
		return $this -> jsonResponse(array('id'=>$session->getId()));
    }
    
    public function logoutAction() {
        $request = $this -> getRequest();
		$repo = $this->getDoctrine()->getRepository('SmartkillAPIBundle:Session');
		
		$session = $repo -> findOneById($request->request->get('id'));
		
		if (!$session) {
			return $this -> jsonResponse(array('msg'=>'Session not found'),'error');
		}
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($session);
		$em->flush();
		
		return $this -> jsonResponse();
    }
    
    private function jsonResponse(array $args = array(), $status = 'success') {
		$response = new Response(json_encode(array('status' => $status) + $args));
		$response->headers->set('Content-Type', 'application/json');
		
		return $response;
	}
}
