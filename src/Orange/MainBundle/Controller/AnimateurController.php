<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Animateur;
use Orange\MainBundle\Form\AnimateurType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Animateur controller.
 *
 * @Route("/animateur")
 */
class AnimateurController extends Controller
{
	/**
	 * Displays a form to create a new Bu entity.
	 *
	 * @Route("/nouveau_animateur/{instance_id}", name="nouveau_animateur", requirements={"instance_id" = "\d+"}, defaults={"instance_id" = null})
	 * @Method("GET")
	 * @Template()
	 */
	public function newAction($instance_id)
	{
		$entity = new Animateur();
		$form   = $this->createCreateForm($entity,'Animateur');
	
		return array(
				'entity' 	  => $entity,
				'form'   	  => $form->createView(),
				'instance_id' => $instance_id
		);
	}
	
	
    /**
     * Creates a new Bu entity.
     *
     * @Route("/creer_bu", name="creer_animateur", requirements={"instance_id" = "\d+"}, defaults={"instance_id" = null})
     * @Template("OrangeMainBundle:Animateur:new.html.twig")
     */
    public function createAction(Request $request, $instance_id)
    {
        $entity = new Animateur();
        $form = $this->createCreateForm($entity,'Animateur');
        $form->handleRequest($request);
       
        	if ($form->isValid()) 
        	{
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($entity);
	            $em->flush();
	            
	            return new JsonResponse(array('url' => $this->generateUrl('les_instance')));
        	}
	            
       	return $this->render('OrangeMainBundle:Animateur:new.html.twig',
        			array(
        					'entity' => $entity,
        					'form'   => $form->createView(),
        					
        				 ), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
    
    
}
