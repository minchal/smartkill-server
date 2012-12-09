<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\NotBlank;

use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Form\RegistrationType;
use Smartkill\WebBundle\Form\ProfileType;

class UserController extends Controller {
	
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('SmartkillWebBundle:User:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
    
    public function logoutAction() {
        
    }
    
    public function registerAction() {
		$request = $this->getRequest();
		$form = $this->createForm(new RegistrationType(), new User());
		
		if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			
			if ($form->isvalid()) {
				$user = $form->getData();
				
				$factory = $this->get('security.encoder_factory');
				$encoder = $factory->getEncoder($user);
				$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
				
				$user -> setPassword($password);
				$user -> setRegisteredAt(new \DateTime());
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();
				
				return $this->render('SmartkillWebBundle:User:register_ok.html.twig');
			}
		}
		
        return $this->render('SmartkillWebBundle:User:register.html.twig', array('form' => $form->createView()));
	}
	
	public function profileAction($id) {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
		
		if (!$id) {
			$id = $this -> getUser() -> getId();
		}
		
		$user = $em->getRepository('SmartkillWebBundle:User')->find($id);
		$old  = clone $user;
		
		if (!$user) {
			throw $this->createNotFoundException('Nie znaleziono takiego użytkownika.');
		}
		
		$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		
		$form = $this->createForm(new ProfileType(), $user);
		
		if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			
			if ($user->oldPassword) {
				$p = $encoder->encodePassword($user->oldPassword, $user->getSalt());
				
				if ($p != $old->getPassword()) {
					$form->get('oldPassword')->addError(new FormError('Podane hasło jest nieprawidłowe!'));
				}
			}
			
			if ($form->isvalid()) {
				if ($user->oldPassword && $user->getPassword()) {
					$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
					$user -> setPassword($password);
				} else {
					$user -> setPassword($old->getPassword());
				}
				
				$em->persist($user);
				$em->flush();
				
				$this->get('session')->setFlash(
					'success',
					'Zmiany w profilu zostały zapisane!'
				);
				
				return $this->redirect($request->getRequestUri());
			}
		}
		
        return $this->render('SmartkillWebBundle:User:profile.html.twig', array(
        	'form'   => $form->createView(),
        	'entity' => $user
        ));
	}
	
	public function avatarAction($id) {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
		
		if (!$id) {
			$id = $this -> getUser() -> getId();
		}
		
		$user = $em->getRepository('SmartkillWebBundle:User')->find($id);
		
		if (!$user) {
			throw $this->createNotFoundException('Nie znaleziono takiego użytkownika.');
		}
		
		$form = $this->createFormBuilder($user)
        	->add('avatarFile', 'file', array('label'=>'Wybierz plik:', 'required'=>false, 'constraints'=> new NotBlank()))
        	->getForm();
        
		if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			
			if ($form->isvalid()) {
				$user->uploadAvatar();
				
				$em->persist($user);
				$em->flush();
				
				$cm = $this->get('liip_imagine.cache.manager');
				
				foreach (array('avatar_large','avatar_medium','avatar_small') as $f) {
					$cm->remove($user->getAvatarUrl(), $f);
				}
				
				$this->get('session')->setFlash(
					'success',
					'Avatar został zapisany!'
				);
				
				return $this->redirect($request->getRequestUri());
			}
		}
		
        return $this->render('SmartkillWebBundle:User:avatar.html.twig', array(
        	'form'   => $form->createView(),
        	'entity' => $user
        ));
	}
	
	public function publicAction($username) {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository('SmartkillWebBundle:User')->findOneByUsername($username);
		
        return $this->render('SmartkillWebBundle:User:public.html.twig', array(
        	'entity' => $user
        ));
	}
	
	public function rankingAction($page) {
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:User');
		
		// pagination
		$total = $repository->createQueryBuilder('u')->getQuery()->getResult();
		$total_entities    = count($total);
		$entities_per_page = $this->container->getParameter('max_on_listing');
		$last_page         = ceil($total_entities / $entities_per_page);
		$previous_page     = $page > 1 ? $page - 1 : 1;
		$next_page         = $page < $last_page ? $page + 1 : $last_page;
		
		$entities = $repository->createQueryBuilder('u')
			->setFirstResult(($page * $entities_per_page) - $entities_per_page)
			->setMaxResults($this->container->getParameter('max_on_listing'))
			->orderBy('u.pointsHunter + u.pointsPrey', 'DESC')
			->getQuery()
			->getResult();
		
		return $this->render('SmartkillWebBundle:User:ranking.html.twig', array(
		            'entities'		=> $entities,
		            'previous_page'	=> $previous_page,
		            'current_page'	=> $page,
		            'next_page'		=> $next_page,
		            'last_page'		=> $last_page,
		));
	}
}
