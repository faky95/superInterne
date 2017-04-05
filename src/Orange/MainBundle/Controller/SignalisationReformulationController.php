<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\SignalisationReformulation;
use Orange\QuickMakingBundle\Controller\BaseController;

/**
 * SignalisationReformulation controller.
 */
class SignalisationReformulationController extends BaseController
{

    /**
     *
     * @Route("/details_reformulation/{id}", name="details_reformulation")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('OrangeMainBundle:SignalisationReformulation')->find($id);
        return array('entity' => $entity);
    }

}
