<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Entity\MatchUser;

class MatchUserController extends Controller {
	
    public function switchAction($id, $user) {
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		
		$em = $this->getDoctrine()->getManager();
		$mu = $em->getRepository('SmartkillWebBundle:MatchUser');
		
		$entity = $mu->find(array('user'=>$user, 'match'=>$id));
		
		if (!$entity) {
			throw $this->createNotFoundException('Nie znaleziono takiego gracza.');
		}
		
		if ($entity->getType() == MatchUser::TYPE_HUNTER) {
			$entity->setType(MatchUser::TYPE_PREY);
		} else {
			$entity->setType(MatchUser::TYPE_HUNTER);
		}
		
		$em->persist($entity);
		$em->flush();
		
		$response -> setContent(json_encode(array('status' => 'success')));
		return $response;
    }
    
    public function deleteAction($id, $user) {
		$response = new Response();
		$response->headers->set('Content-Type', 'application/json');
		
		$em = $this->getDoctrine()->getManager();
		$mu = $em->getRepository('SmartkillWebBundle:MatchUser');
		
		$entity = $mu->find(array('user'=>$user, 'match'=>$id));
		
		if (!$entity) {
			throw $this->createNotFoundException('Nie znaleziono takiego gracza.');
		}
		
		$em->remove($entity);
		$em->flush();
		
		$response -> setContent(json_encode(array('status' => 'success')));
		return $response;
    }
}
