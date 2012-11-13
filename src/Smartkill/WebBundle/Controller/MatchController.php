<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Form\Type\MatchCreateType;

class MatchController extends Controller {
    
    public function createAction() {
		$request = $this->getRequest();
		$form = $this->createForm(new MatchCreateType(), new Match());
		
		if ($request->getMethod()=='POST') {
			$form->bindRequest($request);
			
			if ($form->isvalid()) {
				$match = $form->getData();
				
// 				$factory = $this->get('security.encoder_factory');
// 				$encoder = $factory->getEncoder($match);
// 				$password = $encoder->encodePassword($match->getPassword(), $match->getSalt());
				
// 				$match -> setPassword($password);
				$match -> setCreatedAt(new \DateTime());
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($match);
				$em->flush();
				
				return $this->render('SmartkillWebBundle:Match:create_ok.html.twig');
			}
		}
		
        return $this->render('SmartkillWebBundle:Match:create.html.twig', array('form' => $form->createView()));
	}
	
	public function listingAction() {
		$em = $this->getDoctrine()->getManager();
		$result = $em -> createQuery('SELECT m FROM SmartkillWebBundle:Match m') -> getResult();
		
		return $this->render('SmartkillWebBundle:Match:listing.html.twig', array('matches' => $result));
	}
}
