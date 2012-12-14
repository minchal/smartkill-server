<?php

namespace Smartkill\WebBundle\Controller;

use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Form\RegistrationType;
use Smartkill\WebBundle\Form\ProfileType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormError;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

class ProfileController extends Controller {
	
	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		
		if (!$this->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException();
		}
	}
	
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();
		$repoUser  = $em->getRepository('SmartkillWebBundle:User');
		$repoMatch = $em->getRepository('SmartkillWebBundle:Match');
		
		$user = $this->getUser();
		
		$query = $repoMatch->createQueryBuilder('m')
			->innerJoin('m.players','p','WITH','p.user = :user')
			->andWhere('m.status = :status')
			->andWhere('m.dueDate > :date')
			->orderBy('m.dueDate','ASC')
			->setParameter('user', $this->getUser())
			->setParameter('status', Match::PLANED)
			->setParameter('date', new \DateTime())
			->setMaxResults(1)
			->getQuery()
		;
		
        return $this->render('SmartkillWebBundle:Profile:index.html.twig', array(
        	'entity' => $user,
        	'position' => $repoUser->getPosition($user),
        	'match' => $query->getSingleResult(),
        ));
	}
	
	public function editAction() {
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
		
        return $this->render('SmartkillWebBundle:Profile:edit.html.twig', array(
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
		
        return $this->render('SmartkillWebBundle:Profile:avatar.html.twig', array(
        	'form'   => $form->createView(),
        	'entity' => $user
        ));
	}
	
	public function matchesAction($page, $type) {
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('SmartkillWebBundle:Match');
		
		$query = $repository->createQueryBuilder('m')
			->orderBy('m.dueDate','DESC');
		
		if ($type == 'created') {
			$query->where('m.createdBy = :user');
		} elseif ($type == 'joined') {
			$query->innerJoin('m.players','p','WITH','p.user = :user');
		} else {
			throw $this->createNotFoundException();
		}
		
		$query->setParameter('user', $this->getUser());
		
		try {
			$pager = new Pagerfanta(new DoctrineORMAdapter($query->getQuery()));
			$pager
				->setMaxPerPage(20)
				->setCurrentPage($page);
		} catch(NotValidCurrentPageException $e) {
			throw $this->createNotFoundException();
		}
		
        return $this->render('SmartkillWebBundle:Profile:'.$type.'.html.twig', array(
        	'pager' => $pager
        ));
	}
}
