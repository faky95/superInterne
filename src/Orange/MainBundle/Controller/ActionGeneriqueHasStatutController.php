<?php

namespace Orange\MainBundle\Controller;

use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\MainBundle\Entity\ActionGeneriqueHasStatut;
use Orange\MainBundle\OrangeMainEvents;
use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * ActionGeneriqueHasStatut controller
 */
class ActionGeneriqueHasStatutController extends BaseController
{
	/**
	 * @Route("/{id}/faite_actiongenerique", name="faite_actiongenerique")
	 * @Method({"GET","POST"})
	 * @Template("OrangeMainBundle:ActionGeneriqueHasStatut:faite.html.twig")
	 */
	public function faiteAction(Request $request, $id){
		$em   = $this->getDoctrine()->getManager();
		/** @var ActionGenerique $entity */
		$entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
		if(!$entity){
			$this->get('session')->getFlashBag()->add('error', array('title' => 'Notification', 'body' =>  'L\'action générique n\'existe pas.'));
			return new JsonResponse(array('url' => $this->generateUrl('les_actiongeneriques')));
		}
		$this->denyAccessUnlessGranted('faite', $entity, 'accés non autorisé!');
		if(!$entity->isFaisable()){
			$this->get('session')->getFlashBag()->add('error', array('title' => 'Notification', 'body' =>  'L\'action générique ne peut etre cloturé pour le moment'));
			return new JsonResponse(array('url' => $this->generateUrl('details_actiongenerique', array('id' => $entity->getId()))));
		}
		
		$dispatcher = $this->get('event_dispatcher');
		$actionHasStatut = new ActionGeneriqueHasStatut();
		$form = $this->createCreateForm($actionHasStatut,'ActionGeneriqueHasStatut');
		if($request->getMethod() === 'POST') {
			$form->handleRequest($request);
			if($form->isValid()) {
				$actionHasStatut->setActionGenerique($entity);
				$actionHasStatut->setUtilisateur($this->getUser());
				$event = $this->get('orange_main.actiongenerique_event')->createForActionGenerique($entity);
				$dispatcher->dispatch(OrangeMainEvents::ACTIONGENERIQUE_FAITE, $event);
				$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>$entity->getStatut()));
				$actionHasStatut->setStatut($statut);
				$em->persist($actionHasStatut);
				$em->flush();
				return new JsonResponse(array('url' => $this->generateUrl('details_actiongenerique', array('id' => $entity->getId()))));
			} else {
				return $this->render('OrangeMainBundle:ActionGeneriqueHasStatut:faite.html.twig', array('entity'=>$entity,'id' => $id, 'form'=>$form->createView()), new Response(null, 303));
			}
		}
		return array('entity'=>$entity,'id' => $id, 'form'=>$form->createView());
	}
	
	/**
	 * @Route("/{id}/abandon_actiongenerique", name="abandon_actiongenerique")
	 * @Method({"GET","POST"})
	 * @Template("OrangeMainBundle:ActionGeneriqueHasStatut:abandon.html.twig")
	 */
	public function abandonAction(Request $request, $id){
		$em     = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:ActionGenerique')->find($id);
		if(!$entity){
			$this->get('session')->getFlashBag()->add('error', array('title' => 'Notification', 'body' =>  'L\'action générique n\'existe pas.'));
			return new JsonResponse(array('url' => $this->generateUrl('les_actiongeneriques')));
		}
		$this->denyAccessUnlessGranted('abandonne', $entity, 'accés non autorisé!');
		$dispatcher = $this->get('event_dispatcher');
		$actionHasStatut = new ActionGeneriqueHasStatut();
		$form = $this->createCreateForm($actionHasStatut,'ActionGeneriqueHasStatut');
		if ($request->getMethod () === 'POST') {
			$form->handleRequest ( $request );
			if($form->isValid()) {
				$actionHasStatut->setActionGenerique($entity);
				$actionHasStatut->setUtilisateur($this->getUser());
				$event = $this->get('orange_main.actiongenerique_event')->createForActionGenerique($entity);
				$dispatcher->dispatch(OrangeMainEvents::ACTIONGENERIQUE_ABANDON, $event);
				$statut = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>$entity->getStatut()));
				$actionHasStatut->setStatut($statut);
				$em->persist($actionHasStatut);
				$em->flush();
				return new JsonResponse(array('url' => $this->generateUrl('details_actiongenerique', array('id' => $entity->getId()))));
			} else {
				return $this->render('OrangeMainBundle:ActionGeneriqueHasStatut:abandon.html.twig', array('entity'=>$entity,'id' => $id, 'form'=>$form->createView()), new Response(null, 303));
			}
		}
		return array('entity'=>$entity, 'form'=>$form->createView());
	}
	
}
