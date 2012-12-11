<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Form\RegistrationType;

class DefaultController extends Controller {
	
    public function indexAction() {
		$vars = array();
		
		if (!$this->getUser()) {
			$form = $this->createForm(new RegistrationType(), new User());
			$vars = array('form' => $form->createView());
		}
		
        return $this->render('SmartkillWebBundle:Default:index.html.twig', $vars);
    }
    
    public function staticAction($template) {
        return $this->render('SmartkillWebBundle:Default:'.$template.'.html.twig');
    }
}
