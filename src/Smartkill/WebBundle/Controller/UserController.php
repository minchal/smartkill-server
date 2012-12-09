<?php

namespace Smartkill\WebBundle\Controller;

use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Form\RegistrationType;
use Smartkill\WebBundle\Form\ProfileType;
use Smartkill\WebBundle\Form\UserType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormError;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

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
	
	public function profileAction() {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
		
		$user = $this -> getUser();
		$old  = clone $user;
		
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
	
	public function avatarAction() {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
		
		$user = $this -> getUser();
		
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
				
				foreach ($this->container->getParameter('liip_imagine.filter_sets') as $f => $v) {
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
        	'entity' => $user,
			'deleteForm'  => $this->createDeleteForm($user->getId())->createView(),
        ));
	}
	
	public function rankingAction($page) {
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:User');
		
		$query = $repository->createQueryBuilder('u')
			->orderBy('u.pointsPrey + u.pointsHunter','DESC')
			->getQuery();
		
		try {
			$pager = new Pagerfanta(new DoctrineORMAdapter($query));
			
			$pager
				->setMaxPerPage(20)
				->setCurrentPage($page);
		} catch(NotValidCurrentPageException $e) {
			throw $this->createNotFoundException();
		}
		
		return $this->render('SmartkillWebBundle:User:ranking.html.twig', array(
			'pager' => $pager
		));
	}
	
    public function editAction($id) {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
        $entity  = $em->getRepository('SmartkillWebBundle:User')->find($id);
        $old     = clone $entity;
		
		$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($entity);
		
		$form    = $this->createForm(new UserType(), $entity);
		
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			
			if ($form->isvalid()) {
				if ($entity->getPassword()) {
					$password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
					$entity -> setPassword($password);
				} else {
					$entity->setPassword($old->getPassword());
				}
				
				$em->persist($entity);
				$em->flush();
				
				$this->get('session')->setFlash(
					'success',
					'Zmiany w profilu zostały zapisane!'
				);
				
				return $this->redirect($this->generateUrl('user',array('username'=>$entity->getUsername())));
			}
		}
		
        return $this->render('SmartkillWebBundle:User:form.html.twig', array(
            'entity'     => $entity,
        	'form'       => $form->createView()
        ));
	}
	
	public function deleteAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SmartkillWebBundle:User')->find($id);
		
		if (!$entity) {
			throw $this->createNotFoundException();
		}
		
		$form = $this->createDeleteForm($id)->bind($request);
		
		if ($form->isValid()) {
			if (!$request->get('yes')) {
				return $this->redirect($this->generateUrl('user', array('username'=>$entity->getUsername())));
			}
			
			$em->remove($entity);
			$em->flush();
			
			$this->get('session')->setFlash(
				'success',
				'Użytkownik został usunięty!'
			);
			
			return $this->redirect($this->generateUrl('ranking'));
		}
		
        return $this->render('SmartkillWebBundle:Form:question.html.twig', array(
        	'form'       => $form->createView(),
        	'title'      => 'Usuń użytkownika',
        	'question'   => 'Czy jesteś pewny, że chcesz usunąć użytkownika "'.$entity->getUsername().'"?'
        ));
	}
	
	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm()
		;
	}
}
