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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException();
		}
		
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
	
	public function detailsAction($username, $page) {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('SmartkillWebBundle:User');
		
		$user = $repository->findOneByUsername($username);
		
		if (!$user) {
			throw $this->createNotFoundException('Nie znaleziono takiego użytkownika!');
		}
		
		$query = $em->getRepository('SmartkillWebBundle:Match')
			->createQueryBuilder('m')
			->orderBy('m.dueDate','DESC')
			->innerJoin('m.players','p','WITH','p.user = :user')
			->setParameter('user', $user);
		
		try {
			$pager = new Pagerfanta(new DoctrineORMAdapter($query->getQuery()));
			$pager
				->setMaxPerPage(20)
				->setCurrentPage($page);
		} catch(NotValidCurrentPageException $e) {
			throw $this->createNotFoundException();
		}
		
		$pos = $repository->createQueryBuilder('u')
			->select('COUNT(u.id)+1')
			->where('u.pointsPrey + u.pointsHunter > ?1')
			->setParameter(1, $user->getPointsSum())
			->getQuery();
		
        return $this->render('SmartkillWebBundle:User:details.html.twig', array(
        	'position'    => $pos->getSingleScalarResult(),
        	'entity'      => $user,
        	'pager'       => $pager,
			'deleteForm'  => $this->createDeleteForm($user->getId())->createView(),
        ));
	}
	
	public function rankingAction($page) {
		$repository = $this->getDoctrine()->getRepository('SmartkillWebBundle:User');
		
		$qb2 = $repository->createQueryBuilder('uu')
			->select('COUNT(uu.id)+1')
			->where('uu.pointsPrey+uu.pointsHunter > u.pointsPrey+u.pointsHunter');
		
		$qb = $repository->createQueryBuilder('u')
			->addSelect('('.$qb2->getDQL().') AS position')
			->orderBy('u.pointsPrey + u.pointsHunter','DESC');
		
		try {
			$pager = new Pagerfanta(new DoctrineORMAdapter($qb->getQuery()));
			
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
		if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException();
		}
		
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
        $entity  = $em->getRepository('SmartkillWebBundle:User')->find($id);
        
		if (!$entity) {
			throw $this->createNotFoundException('Nie znaleziono takiego użytkownika!');
		}
		
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
		if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException();
		}
		
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SmartkillWebBundle:User')->find($id);
		
		if (!$entity) {
			throw $this->createNotFoundException('Nie znaleziono takiego użytkownika!');
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
