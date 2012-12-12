<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Entity\MatchUser;

class MatchUserController extends Controller {
	
	private function prepare($id, $user) {
		$em = $this->getDoctrine()->getManager();
		$mu = $em->getRepository('SmartkillWebBundle:MatchUser');
		
		$entity = $mu->find(array('user'=>$user, 'match'=>$id));
		
		if (!$entity) {
			throw $this->createNotFoundException('Nie znaleziono takiego gracza.');
		}
		
		$c = new MatchController();
		$c -> setContainer($this->container);
		
		if (!$c -> canManage($entity->getMatch(), $this->getUser())) {
			throw new AccessDeniedException();
		}
		
		return $entity;
	}
	
    public function switchAction($id, $user) {
		$entity = $this -> prepare($id, $user);
		
		if ($entity->getType() == MatchUser::TYPE_HUNTER) {
			$entity->setType(MatchUser::TYPE_PREY);
		} else {
			$entity->setType(MatchUser::TYPE_HUNTER);
		}
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($entity);
		$em->flush();
		
		return new JsonResponse(array('status' => 'success'));
    }
    
    public function deleteAction($id, $user) {
		$entity = $this -> prepare($id, $user);
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($entity);
		$em->flush();
		
		return new JsonResponse(array('status' => 'success'));
    }
}
