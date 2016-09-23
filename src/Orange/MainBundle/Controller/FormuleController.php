<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Formule;
use Orange\MainBundle\Form\FormuleType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Entity\Reference;
use Symfony\Component\Routing\RequestContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Bu controller.
 *
 */
class FormuleController extends BaseController
{


	/**
	 * @QMLogger(message="Liste des formules")
	 * @Route("/les_formules", name="les_formules")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
    	return array();
    }
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_formules", name="liste_des_formules")
     * @Method("GET")
     * @Template()
     */
    public function listeAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Formule')->listAllBu($this->getUser()->getStructure()->getBuPrincipal());
    	return $this->paginate($request, $queryBuilder);
    }
    
    /**
     * Displays a form to edit an existing Structure entity.
     * @QMLogger(message="Visualisation d'une formule")
     * @Route("/{id}/details_formule", name="details_formule", requirements={ "id"=  "\d+"})
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$refs = $em->getRepository('OrangeMainBundle:Reference')->listAll();
    	$entity = $em->getRepository('OrangeMainBundle:Formule')->find($id);
    	return array('entity' => $entity, 'references' => $refs);
    }
    
    /**
     * Displays a form to create a new formule entity.
     * @QMLogger(message="Nouvelle Formule")
     * @Route("/nouvelle_formule", name="nouvelle_formule")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$refs = $em->getRepository('OrangeMainBundle:Reference')->listAll();
        $entity = new Formule();
        $form   = $this->createCreateForm($entity,'Formule');
        return array(
            'entity' => $entity,
        	'references' => $refs,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new formule entity.
     * @QMLogger(message="Création formule")
     * @Route("/creer_formule", name="creer_formule")
     * @Method("POST")
     * @Template("OrangeMainBundle:Formule:new.html.twig")
     */
    public function creerAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = new Formule();
    	$refs = $em->getRepository('OrangeMainBundle:Reference')->listAll();
    	$form = $this->createCreateForm($entity,'Formule');
    	$form->handleRequest($request);
    	if ($request->getMethod() == 'POST' ) {
    		if ($form->isValid()) {
    			$num = implode("+", $entity->getNum());
    			$den = implode("+", $entity->getDenom());
    			$entity->setNumerateur($num);
    			$entity->setDenominateur($den);
    			$entity->setBu($this->getUser()->getStructure()->getBuPrincipal());
    			$em->persist($entity);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
							'title' => 'Notification',
							'body' => 'Formule a étè créée avec succes'
				));
    			return $this->redirect($this->generateUrl('les_formules'));
    		}
    	}
    	return array(
    			'entity' => $entity,
    			'references' => $refs,
    			'form'   => $form->createView(),
    	);
    }
    
    
    /**
     * Displays a form to edit an existing Structure entity.
     * @QMLogger(message="Edition Formule")
     * @Route("/{id}/edition_formule", name="edition_formule", requirements={ "id"=  "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$refs = $em->getRepository('OrangeMainBundle:Reference')->listAll();
    	$entity = $em->getRepository('OrangeMainBundle:Formule')->find($id);
    	$num = explode("+", $entity->getNumerateur());
    	$den = explode("+", $entity->getDenominateur());
    	$entity->setNum($num);
    	$entity->setDenom($den);
    	$form = $this->createCreateForm($entity,'Formule');
    	$request = $this->get('request');
    	if ($request->getMethod() == 'POST') {
    		$form->bind($request);
    		if ($form->isValid()) {
    			$num = implode("+", $entity->getNum());
    			$den = implode("+", $entity->getDenom());
    			$entity->setNumerateur($num);
    			$entity->setDenominateur($den);
    			$em->persist($entity);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Formule modifiée avec succés.'));
    			return $this->redirect($this->generateUrl('les_formules'));
    		}
    	}
    	return array('entity' => $entity,
    				'references' => $refs,
    				'edit_form' => $form->createView());
    }
    
    
    /**
     *  Deletes a Formule entity.
     *  @QMLogger(message="Suppression formule")
     *  @Route("/{id}/supprimer_formule", name="supprimer_formule", requirements={ "id"=  "\d+"})
     *  @Security("has_role('ROLE_ADMIN')")
     */
    
    public function deleteAction($id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Formule')->find($id);
    		$em->remove($entity);
    		$em->flush();
    		$this->container->get('session')->getFlashBag()->add('success', array (
    				'title' =>'Notification',
    				'body' => 'La formule a été supprimée avec succes !'
    		));
    	return $this->redirect($this->generateUrl('les_formules'));
    }
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Formule $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			'<span align="center" style="margin-left: 15px; width:20px; height:20px; background:'.$entity->getCouleur().'">&nbsp;&nbsp;&nbsp;&nbsp;</span>',
    			$entity->getLibelle(),
    			$entity->getVisibilite() ? 'OUI' : 'NON',
    			$this->get('orange_main.actions')->generateActionsForFormule($entity)
    	);
    }
    
}
