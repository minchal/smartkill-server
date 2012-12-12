<?php

namespace Smartkill\WebBundle\Controller;

use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Entity\MatchUser;
use Smartkill\WebBundle\Entity\User;
use Smartkill\WebBundle\Entity\Package;
use Smartkill\WebBundle\Form\MatchType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

class MatchController extends Controller {
	
	/**
	 * Mecz może być zarządzany, gdy ma status zaplanowany i użytkownik jest właścicielem albo adminem.
	 */
	public function canManage(Match $match, $user = null) {
		return 
			$match->isStatus(Match::PLANED) && 
			(
				$this->get('security.context')->isGranted('ROLE_ADMIN') || 
				$user && $user == $match->getCreatedBy()
			)
		;
	}
	
    public function indexAction($page)  {
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('SmartkillWebBundle:Match');
		
		$query = $repository->createQueryBuilder('m')
			->orderBy('m.dueDate','ASC')
			->getQuery();
		
		try {
			$pager = new Pagerfanta(new DoctrineORMAdapter($query));
			
			$pager
				->setMaxPerPage(20)
				->setCurrentPage($page);
		} catch(NotValidCurrentPageException $e) {
			throw $this->createNotFoundException();
		}
		
		return $this->render('SmartkillWebBundle:Match:index.html.twig', array(
			'pager' => $pager
		));
	}

    public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$mu = $em->getRepository('SmartkillWebBundle:MatchUser');
		
		$match = $em->getRepository('SmartkillWebBundle:Match')->find($id);
		
		if (!$match) {
			throw $this->createNotFoundException('Nie znaleziono takiego meczu.');
		}
		
		if (isset($_GET['start'])) {
			$match -> start($em);
		}
		
		$joined = false;
		
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			$joined = $mu->find(array('user'=>$this->getUser()->getId(), 'match'=>$match->getId()));
		}
		
		return $this->render('SmartkillWebBundle:Match:show.html.twig', array(
			'entity'      => $match,
			'joined'	  => $joined,
			'canManage'	  => $this->canManage($match, $this->getUser()),
			'preys'       => $mu -> findBy(array('match'=>$match,'type'=>MatchUser::TYPE_PREY)),
			'hunters'     => $mu -> findBy(array('match'=>$match,'type'=>MatchUser::TYPE_HUNTER)),
			'joinForm'    => $this->createJoinForm($match->getId())->createView(),
			'deleteForm'  => $this->createDeleteForm($match->getId())->createView(),
			'random' => Package::createRandom($match->getLat(), $match->getLng(), $match->getSize(), Package::getTypes())
		));
    }
	
    public function addAction() {
		if (!$this->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException();
		}
		
		$request = $this->getRequest();
		$user    = $this->getUser();
        $entity  = new Match();
        $entity -> setLat(51.11);
		$entity -> setLng(17.06);
		$form    = $this->createForm(new MatchType(), $entity);
		
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			
			if ($form->isvalid()) {
				$entity -> setCreatedAt(new \DateTime());
				$entity -> setCreatedBy($user);
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($entity);
				$em->flush();
				
				$this->get('session')->setFlash(
					'success',
					'Mecz został pomyślnie utworzony!'
				);
				
				return $this->redirect($this->generateUrl('match'));
			}
		}
		
        return $this->render('SmartkillWebBundle:Match:form.html.twig', array(
            'entity'     => $entity,
        	'form'       => $form->createView(),
        	'title'      => 'Zaplanuj mecz',
        	'isEditForm' => false
        ));
	}
	
    public function editAction($id) {
		$request = $this->getRequest();
		$em      = $this->getDoctrine()->getManager();
        $entity  = $em->getRepository('SmartkillWebBundle:Match')->find($id);
        $old     = clone $entity;
        
        if (!$this->canManage($entity, $this->getUser())) {
			throw new AccessDeniedException();
		}
        
		$form    = $this->createForm(new MatchType(), $entity);
		
		if ($request->getMethod() == 'POST') {
			$form->bind($request);
			
			if ($form->isvalid()) {
				if (!$entity->getPassword()) {
					$entity->setPassword($old->getPassword());
				}
				
				$em->persist($entity);
				$em->flush();
				
				$this->get('session')->setFlash(
					'success',
					'Zmiany w ustawieniach meczu zostały zapisane!'
				);
				
				return $this->redirect($this->generateUrl('match_show',array('id'=>$entity->getId())));
			}
		}
		
        return $this->render('SmartkillWebBundle:Match:form.html.twig', array(
            'entity'     => $entity,
        	'form'       => $form->createView(),
        	'title'      => 'Edytuj mecz',
        	'isEditForm' => true
        ));
	}
	
	public function deleteAction(Request $request, $id) {
		if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException();
		}
		
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('SmartkillWebBundle:Match')->find($id);
		
		if (!$entity) {
			throw $this->createNotFoundException();
		}
		
		$form = $this->createDeleteForm($id)->bind($request);
		
		if ($form->isValid()) {
			if (!$request->get('yes')) {
				return $this->redirect($this->generateUrl('match_show', array('id'=>$entity->getId())));
			}
			
			$em->remove($entity);
			$em->flush();
			
			$this->get('session')->setFlash(
				'success',
				'Mecz został usunięty!'
			);
			
			return $this->redirect($this->generateUrl('match'));
		}
		
        return $this->render('SmartkillWebBundle:Form:question.html.twig', array(
        	'form'       => $form->createView(),
        	'title'      => 'Usuń mecz',
        	'question'   => 'Czy jesteś pewny, że chcesz usunąć mecz "'.$entity->getName().'"?'
        ));
	}
	
	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->getForm()
		;
	}
    
    public function joinAction(Request $request, $id) {
		if (!$this->get('security.context')->isGranted('ROLE_USER')) {
			throw new AccessDeniedException();
		}
		
		$user  = $this->getUser();
		$em    = $this->getDoctrine()->getManager();
		$mu    = $em->getRepository('SmartkillWebBundle:MatchUser');
		$match = $em->getRepository('SmartkillWebBundle:Match')->find($id);
		
		if (!$match) {
			throw $this->createNotFoundException('Unable to find Match entity.');
		}
		
		$joined = $mu->find(array('user'=>$user->getId(), 'match'=>$match->getId()));
		
		if ($joined) {
			return new JsonResponse(array('status' => 'error', 'msg'=> 'Już dołączyłeś do tego meczu!'));
		}
		
		$form = $this->createJoinForm($match->getId())
			->bind($request);
		
		if (!$form->isValid()) {
			throw $this->createNotFoundException('Form invalid.');
		}
		
		if ($match->isFull()) {
			return new JsonResponse(array('status' => 'error', 'msg'=> 'Osiągnięto już limit graczy dla tego meczu!'));
		}
		
		if ($match->getPassword()) {
			$data = $form->getData();
			
			if ($match->getPassword() != $data['password']) {
				return new JsonResponse(array('status' => 'error', 'msg'=> 'Podane hasło jest nieprawidłowe!'));
			}
		}
		
		$entity = new MatchUser();
		$entity -> setType($entity::TYPE_PREY);
		$entity -> setUser($user);
		$entity -> setMatch($match);
		
		$em->persist($entity);
		$em->flush();
		
		$this->get('session')->setFlash(
			'success',
			'Zostałeś zapisany do meczu!'
		);
		
		return new JsonResponse(array('status' => 'success'));
    }
    
	private function createJoinForm($id) {
		return $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->add('password', 'password', array('label'=>'Hasło:','required'=>false))
			->getForm()
		;
	}
}
