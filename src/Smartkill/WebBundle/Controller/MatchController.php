<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Entity\MatchUser;
use Smartkill\WebBundle\Form\MatchType;

/**
 * 
 */
class MatchController extends Controller {
	
    public function indexAction()  {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('SmartkillWebBundle:Match')->findAll();
        
		return $this->render('SmartkillWebBundle:Match:index.html.twig', array(
			'entities' => $entities
		));
	}

    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SmartkillWebBundle:Match')->find($id);
		$user	= $this->getUser();
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Match entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        return array(
            'entity'      => $entity,
            'user'        => $user,
            'joined'	  => $em->getRepository('SmartkillWebBundle:MatchUser')->find(array('user'=>$user->getId(), 'match'=>$entity->getId())),
            'delete_form' => $deleteForm->createView(),
        );
    }

    public function addAction() {
		$request = $this->getRequest();
        $entity = new Match();
		$form = $this->createForm(new MatchType(), $entity);
		
		if ($request->getMethod()=='POST') {
			$form->bind($request);
			
			if ($form->isvalid()) {
				$entity -> setCreatedAt(new \DateTime());
				$entity -> setCreatedBy($user);
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($entity);
				$em->flush();
				
				return $this->render('SmartkillWebBundle:Match:add_ok.html.twig');
			}
		}
		
        return $this->render('SmartkillWebBundle:Match:form.html.twig', array(
            'entity' => $entity,
        	'form' => $form->createView(),
        	'title' => 'Zaplanuj mecz',
        	'action' => 'match_add'
        ));
	}
	
    /**
     * Displays a form to edit an existing Match entity.
     *
     * @Route("/{id}/edit", name="match_edit")
     * @Template()
     
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SmartkillWebBundle:Match')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Match entity.');
        }

        $editForm = $this->createForm(new MatchType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
*/
    /**
     * Edits an existing Match entity.
     
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SmartkillWebBundle:Match')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Match entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MatchType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('match_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
*/
    /**
     * Deletes a Match entity.
     *
    
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SmartkillWebBundle:Match')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Match entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('match'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
     */
    
    public function joinAction(Request $request, $id)
    {
    	$em 	= $this->getDoctrine()->getManager();
    	$user	= $this->getUser();
        $match	= $em->getRepository('SmartkillWebBundle:Match')->find($id);

        if (!$match) {
            throw $this->createNotFoundException('Unable to find Match entity.');
        }
        if ($match->getPassword()) {
        	var_dump($_POST);
        	if ($match->getPassword() != $request->get('pass')) {
        		throw $this->createNotFoundException('Password is invalid.');
        	}
        }
        
        $entity = new MatchUser();

		$entity -> setUser($user->getId());
		$entity -> setMatch($match->getId());
		$entity -> setType($entity::TYPE_PREY);
		$entity -> setLat($match->getLat());
		$entity -> setLng($match->getLng());
		$entity -> setUpdatedAt(new \DateTime());
			
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('match_show', array('id' => $match->getId())));
    }
}
