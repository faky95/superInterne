<?php

namespace Orange\MainBundle\Controller;

use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Orange\MainBundle\Entity\Priorite;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Priorite controller.
 *
 */
class PrioriteController extends BaseController
{

    /**
     * Lists all Priorite entities.
     *
     * @Route("/les_priorites", name="les_priorites")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
    	return array();
    }
    /**
     * Creates a new Priorite entity.
     *
     * @Route("/creer_priorite", name="creer_priorite")
     * @Method("POST")
     * @Template("OrangeMainBundle:Priorite:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Priorite();
        $form = $this->createCreateForm($entity,'Priorite');
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
        	$form->remove('bu');
        }
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $bu = $this->getUser()->getStructure()->getBuPrincipal();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('les_priorites'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Instance entity.
     *
     * @Route("/nouvelle_priorite", name="nouvelle_priorite")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction()
    {
        $entity = new Priorite();
        $form   = $this->createCreateForm($entity,'Priorite');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

}
