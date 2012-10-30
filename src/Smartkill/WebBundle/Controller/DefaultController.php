<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
	
    public function indexAction() {
        return $this->render('SmartkillWebBundle:Default:index.html.twig');
    }
    
    public function contactAction() {
        return $this->render('SmartkillWebBundle:Default:contact.html.twig');
    }
}
