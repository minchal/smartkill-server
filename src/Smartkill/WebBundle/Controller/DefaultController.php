<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Form\RegistrationType;
use Smartkill\WebBundle\Form\ContactType;

class DefaultController extends Controller {
	
    public function indexAction() {
		$vars = array();
		
		if ($this->getUser()) {
			$em   = $this->getDoctrine()->getManager();
			$user = $this -> getUser();
			
			$vars = array(
				'entity'   => $user,
				'position' => $em->getRepository('SmartkillWebBundle:User')->getPosition($user)
			);
		} else {
			$vars = array(
				'form' => $this->createForm(new RegistrationType(), new User())->createView()
			);
		}
		
        return $this->render('SmartkillWebBundle:Default:index.html.twig', $vars);
    }
    
    public function staticAction($template) {
        return $this->render('SmartkillWebBundle:Default:'.$template.'.html.twig');
    }
    
    public function contactAction() {
    	$request = $this->getRequest();
    	$contact = array('name'=>'','email'=>'','subject'=>'','msg'=>'');
    	$form = $this->createForm(new ContactType(), $contact);
    	
    	if ($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    	
    		if ($form->isValid()) {
    			$contact = $form->getData();
    			
    			$message = \Swift_Message::newInstance()
		            ->setSubject('Smartkill - Kontakt')
		            ->setFrom('web@smartkill.pl')
		            ->setTo('michal@pawlowski.be')
		            ->setBody($this->renderView('SmartkillWebBundle:Default:contactEmail.txt.twig', array('contact' => $contact)));
		        
		        $this->get('mailer')->send($message);
		        
		        $this->get('session')->setFlash('success', 'Wiadomość została wysłana. Dziękujemy!');
		        
		        return $this->redirect($this->generateUrl('contact'));
    		}
    	}
    	
    	return $this->render('SmartkillWebBundle:Default:contact.html.twig', array('form' => $form->createView()));
    }
}
