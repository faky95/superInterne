<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\ParametrageBu;
use Orange\MainBundle\Form\ParametrageBuType;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * ParametrageBu controller.
 *
 */
class ParametrageBuController extends BaseController
{
    /**
     * Lists all ParametrageBu entities.
     *
     * @Route("/les_parametrages", name="les_parametrages")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:ParametrageBu')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ParametrageBu entity.
     *
     * @Route("/creer_parametrage", name="creer_parametrage")
     * @Method("POST")
     * @Template("OrangeMainBundle:ParametrageBu:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ParametrageBu();
        $form = $this->createCreateForm($entity,'ParametrageBu');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

           // return $this->redirect($this->generateUrl('parametragebu_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Displays a form to create a new ParametrageBu entity.
     *
     * @Route("/parametrer", name="parametrer_bu")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ParametrageBu();
        $form   = $this->createCreateForm($entity,'ParametrageBu');

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ParametrageBu entity.
     *
     * @Route("/{id}/details_param", name="details_param")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:ParametrageBu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ParametrageBu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
   
}
