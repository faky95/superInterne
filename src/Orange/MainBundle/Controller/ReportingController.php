<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Reporting;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;

/**
 * Reporting controller.
 */
class ReportingController extends BaseController
{

	/**
	 * @Route("/les_reportings", name="les_reportings")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
    	return array();
    }
    
    /**
     * Lists  entities.
     * @Route("/liste_des_reportings", name="liste_des_reportings")
     * @Method("GET")
     * @Template()
     */
    public function listeAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Reporting')->listAllReporting($this->getUser());
    	return $this->paginate($request, $queryBuilder);
    }
    
    /**
     * Displays a form to edit an existing Structure entity.
     * @Route("/{id}/edition_reporting", name="edition_reporting", requirements={ "id"=  "\d+"})
     * @Method({"POST","GET"})
     * @Template()
     */
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Reporting')->find($id);
    	$form = $this->createCreateForm($entity,'Reporting');
    	$request = $this->get('request');
    	if ($request->getMethod() == 'POST') {
    		$form->bind($request);
    		if ($form->isValid()) {
    			$em->persist($entity);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' => 'Notification', 'body' => 'Reporting modifié avec succés !'
    				));
    			return $this->redirect($this->generateUrl('les_reportings'));
    		}
    	}
    	return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    
    
    /**
     *  Deletes a Formule entity.
     *  @Route("/{id}/supprimer_reporting", name="supprimer_reporting", requirements={ "id"=  "\d+"})
     */
    public function deleteAction($id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Reporting')->find($id);
   		$em->remove($entity);
   		$em->flush();
   		$this->container->get('session')->getFlashBag()->add('success', array (
   				'title' =>'Notification', 'body' => 'Le reporting a été supprimé avec succes !'
   			));
    	return $this->redirect($this->generateUrl('les_reportings'));
    }
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Reporting $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelle(),
    			$this->get('orange_main.actions')->generateActionsForReporting($entity)
    		);
    }
	
	 /**
     *
     * @Route("/update_reportings", name="update_reportings")
     * @Template()
     */
    public function updateQueryAction(){
    	$em = $this->getDoctrine()->getManager();
    	$reportings = $em->getRepository('OrangeMainBundle:Reporting')->findBy(array('query' => ''));
    	foreach ($reportings as $key => $entity){
			if($entity->getRequete()!=null){
		    	$debutJoins = strpos($entity->getRequete(), 'FROM');
		    	$finJoins = strpos($entity->getRequete(), 'GROUP BY');
		    	
		    	$p1 = strpos($entity->getRequete(), 'COUNT');
		    	$p2 = strpos($entity->getRequete(), 'total');
		        
		        $tmp=explode('(', substr($entity->getRequete(), $p1, $p2 - $p1));
				list($val1,$val2)=$tmp;
		    	list($alias)=explode('.',$val2);
		    	$select = "SELECT ".$alias.".id";
		    	$joins = substr($entity->getRequete(), $debutJoins, $finJoins - $debutJoins);
		    	
		    	$where = " AND ". $alias.".etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'
					    			AND ".$alias.".etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'
					    			AND ".$alias.".etatCourant NOT LIKE 'ACTION_NOUVELLE'
    			                    AND ".$alias.".etatCourant NOT LIKE 'EVENEMENT_INVALIDER'";
		    	$groupBy= " GROUP BY ".$alias.".id";
		    	$query= $select." ".$joins." ".$where." ".$groupBy;
		    	$entity->setQuery($query);
		    	$em->persist($entity);
			}
    	}
    	$em->flush();
    	$this->get('session')->getFlashBag()->add('success', array (
    			'title' => 'Notification',
    			'body' => 'Reporting modifié avec succés !'
    	));
    	return $this->redirect($this->generateUrl('les_reportings'));
    }
    
}
