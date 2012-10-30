<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Form\Type\RegistrationType;

class DefaultController extends Controller {
	
    public function indexAction() {
		$vars = array();
		
		if (!$this->getUser()) {
			$form = $this->createForm(new RegistrationType(), new User());
			$vars = array('form' => $form->createView());
		}
		
        return $this->render('SmartkillWebBundle:Default:index.html.twig', $vars);
    }
    
    public function contactAction() {
        return $this->render('SmartkillWebBundle:Default:contact.html.twig');
    }
}
