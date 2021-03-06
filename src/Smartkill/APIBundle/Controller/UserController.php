<?php

namespace Smartkill\APIBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Smartkill\APIBundle\Entity\Session;

class UserController extends Controller {
	
    public function loginAction() {
		$request = $this -> getRequest();
		
		$user = $this->getRepository('SmartkillWebBundle:User')
			->findOneByUsername($request->get('username'));
		
		if (!$user) {
			return $this -> errorResponse('User not found');
		}
		
		$encoder  = $this->get('security.encoder_factory')->getEncoder($user);
		$password = $encoder->encodePassword($request->get('password'), $user->getSalt());
		
		if ($user->getPassword()!=$password) {
			return $this -> errorResponse('User not found');
		}
		
		$session = new Session();
		$session -> setUser($user);
		
		$em = $this->getManager();
		$em->persist($session);
		$em->flush();
		
		return $this -> jsonResponse(array('id'=>$session->getId(),'user'=>$user));
    }
    
    public function logoutAction() {
        $session = $this->checkSession();
		
		if (!$session) {
			return $this -> sessionNotFound();
		}
		
		$em = $this->getManager();
		$em->remove($session);
		$em->flush();
		
		return $this -> jsonResponse();
    }
    
    public function profileAction() {
		$session = $this->checkSession();
		
		if (!$session) {
			return $this -> sessionNotFound();
		}
		
		$user = $this->getRepository('SmartkillWebBundle:User')
			->find($this->getRequest()->get('user'));
		
		if (!$user) {
			return $this -> errorResponse('User not found');
		}
		
		return $this -> jsonResponse($user);
	}
}
