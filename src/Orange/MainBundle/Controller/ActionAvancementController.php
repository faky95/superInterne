<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\ActionAvancement;
use Orange\MainBundle\Form\ActionAvancementType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * ActionAvancement controller.
 */
class ActionAvancementController extends Controller
{

    /**
     * Creates a new Signalisation Action entity.
     * @QMLogger(message="Nouveau avancement")
     * @Route("/creer_action_avancement/{action_id}", name="actionavancement_create")
     * @Method("POST")
     */
    public function createActionAvancementAction(Request $request, $action_id)
    {
        $em = $this->getDoctrine()->getManager();
        $action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
        $entity = new ActionAvancement();
        $form = $this->createCreateForm($entity, $action->getId());
        $form->handleRequest($request);
        if($form->isValid()){
            $em->persist($entity);
            $entity->setAction($action);
            $entity->setAuteur($this->getUser());
            $em->flush();
            return new JsonResponse(array('url' =>$this->generateUrl('details_action', array('id' => $action_id))));
        }
        return $this->render('OrangeMainBundle:ActionAvancement:new.html.twig', array(
                'entity' => $entity, 'form'   => $form->createView(), 'action_id' => $action_id,
        	), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    

    /**
     * Creates a form to create a ActionAvancement entity.
     * @param ActionAvancement $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ActionAvancement $entity, $action_id)
    {
        $form = $this->createForm(new ActionAvancementType(), $entity, array(
	            'action' => $this->generateUrl('actionavancement_create', array('action_id' => $action_id)),
	            'method' => 'POST',
	        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

    /**
     * Displays a form to create a new ActionAvancement entity.
     * @Route("/nouvel_avancement/{action_id}", name="nouvel_avancement")
     * @Method("GET")
     * @Template()
     */
    public function newAction($action_id)
    {
        $em = $this->getDoctrine()->getManager();
        $action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
        $entity = new ActionAvancement();
        $entity->setAction($action);
        $form   = $this->createCreateForm($entity, $action_id);
        return array('entity' => $entity, 'action_id' => $action_id, 'form' => $form->createView(), 'action' => $action);
    }

   
}
