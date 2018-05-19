<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\QuickMakingBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Panier controller.
 */
class PanierController extends BaseController
{
	
	/**
	 * @Route("/{id}/ajouter_dans_panier", name="ajouter_dans_panier")
	 * @Template()
	 */
	public function newAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Panier')->findMine();
		if($entity==null) {
			$entity = new \Orange\MainBundle\Entity\Panier();
			$entity->setUtilisateur($this->getUser());
		}
		$entity->addAction($em->getReference('OrangeMainBundle:Action', $id));
		$em->persist($entity);
		$em->flush();
		return new JsonResponse();
	}
	/**
	 * @Route("/mon_panier", name="mon_panier")
	 * @Template()
	 */
	public function showMineAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:Panier')->findMine();
		return $entity ? $this->forward('Orange\MainBundle\Controller\PanierController::showAction', array('id' => $entity->getId())) : array();
	}
    
    /**
     * @Route("/{id}/details_panier", name="details_panier")
     * @Template()
     */
    public function showAction(Request $request, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Panier')->find($id);
    	$actions = $em->getRepository('OrangeMainBundle:Action')->listByPanier($id);
    	return array('entity' => $entity, 'actions' => $actions);
    }
}
