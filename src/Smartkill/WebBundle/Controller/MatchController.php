<?php

namespace Smartkill\WebBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ivory\GoogleMapBundle\Model\MapTypeId;
use Smartkill\WebBundle\Entity\Match;
use Smartkill\WebBundle\Entity\MatchUser;
use Smartkill\WebBundle\Form\MatchType;

/**
 * Match controller.
 *
 * @Route("/match")
 */
class MatchController extends Controller
{
    /**
     * Lists all Match entities.
     *
     * @Route("/", name="match")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SmartkillWebBundle:Match')->findAll();

        return $this->render('SmartkillWebBundle:Match:index.html.twig', array('entities' => $entities));
    }

    /**
     * Finds and displays a Match entity.
     *
     * @Route("/{id}/show", name="match_show")
     * @Template()
     */
    public function showAction($id)
    {
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

    /**
     * Displays a form to create a new Match entity.
     *
     * @Route("/new", name="match_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Match();
        $form   = $this->createForm(new MatchType(), $entity);
        $map	= $this->get('ivory_google_map.map');
        
//         $geocoder = $this->get('ivory_google_map.geocoder');
//         $lat = $geocoder->getLatitude();
//         $lng = $geocoder->getLongtitude();
        
        $circle	= $this->get('ivory_google_map.circle');
        $circle->setPrefixJavascriptVariable('circle_');
        $circle->setCenter(51.11, 17.06, true);
        $circle->setRadius(1000);
        $circle->setOption('clickable', true);
        $circle->setOption('strokeWeight', 2);
        $map->addCircle($circle);
        
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);
        $map->setAutoZoom(false);
        $map->setCenter(51.11, 17.06, true);
        $map->setMapOption('zoom', 10);
        $map->setBound(-2.1, -3.9, 2.6, 1.4, true, true);
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setMapOption('mapTypeId', 'roadmap');
        
//         $map->setMapOption('disableDefaultUI', true);
//         $map->setMapOption('disableDoubleClickZoom', true);
        
        $map->setStylesheetOptions(array(
            'width'	 => '500px',
            'height' => '500px'
        ));
        
        $map->setLanguage('pl');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'map'	 => $map,
        );
    }

    /**
     * Creates a new Match entity.
     *
     * @Route("/create", name="match_create")
     * @Method("POST")
     * @Template("SmartkillWebBundle:Match:new.html.twig")
     */
    public function createAction(Request $request)
    {
    	$user 	= $this->getUser();
        $entity = new Match();
        $form = $this->createForm(new MatchType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
			$entity -> setCreatedAt(new \DateTime());
			$entity -> setCreatedBy($user);
			
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('match_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Match entity.
     *
     * @Route("/{id}/edit", name="match_edit")
     * @Template()
     */
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

    /**
     * Edits an existing Match entity.
     *
     * @Route("/{id}/update", name="match_update")
     * @Method("POST")
     * @Template("SmartkillWebBundle:Match:edit.html.twig")
     */
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

    /**
     * Deletes a Match entity.
     *
     * @Route("/{id}/delete", name="match_delete")
     * @Method("POST")
     */
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
    
    /**
    *
    * @Route("/{id}/join", name="match_join")
    * @Method("POST")
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
