<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\TacheStatut;
use Orange\MainBundle\Form\TacheStatutType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Entity\Statut;
use Symfony\Component\HttpFoundation\Response;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * TacheStatut controller.
 *
 * @Route("/tachestatut")
 */
class TacheStatutController extends BaseController
{

    /**
     * Lists all TacheStatut entities.
     *
     * @Route("/", name="tachestatut")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:TacheStatut')->findAll();

        return array(
            'entities' => $entities,
        );
    }
//     /**
//      * Creates a new TacheStatut entity.
//      *
//      * @Route("/", name="tachestatut_create")
//      * @Method("POST")
//      * @Template("OrangeMainBundle:TacheStatut:new.html.twig")
//      */
//     public function createAction(Request $request)
//     {
//         $entity = new TacheStatut();
//         $form = $this->createCreateForm($entity);
//         $form->handleRequest($request);

//         if ($form->isValid()) {
//             $em = $this->getDoctrine()->getManager();
//             $em->persist($entity);
//             $em->flush();

//             return $this->redirect($this->generateUrl('tachestatut_show', array('id' => $entity->getId())));
//         }

//         return array(
//             'entity' => $entity,
//             'form'   => $form->createView(),
//         );
//     }

    
    /**
     * Creates a new Domaine entity.
     *
     * @Route("/creer_tache_statut/{tache_id}/{etat}", name="tachestatut_create")
     * @Method("POST")
     * @Template("OrangeMainBundle:TacheStatut:new.html.twig")
     */
    public function createAction(Request $request, $tache_id, $etat)
    {
    	$entity = new TacheStatut();
    	$form = $this->createCreateForm($entity,'TacheStatut');
    	$form->handleRequest($request);
    
    	if ($form->isValid()) 
    	{
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($entity);
    		$tache = $em->getRepository('OrangeMainBundle:Tache')->find($tache_id);
    		$actionCyclique = $tache->getActionCyclique();
    		$today = new \DateTime();
    		$dateCloture = $tache->getDateCloture();
    		
    		switch ($etat)
			{
				case 'FAITE':
					$statut = Statut::TACHE_FAITE;
				break;
				case 'SOLDER':
					if($today > $dateCloture)
					{
						$statut = Statut::TACHE_ECHUE_SOLDER;
					}
					else
					{
						$statut = Statut::TACHE_NON_ECHUE_SOLDER;
					}
				break;
				case 'NON_SOLDER':
					if($today > $dateCloture)
					{
						$statut = Statut::TACHE_ECHUE_NON_SOLDER;
					}
					else 
					{
						$statut = Statut::TACHE_NON_ECHUE_NON_SOLDE;
					}
				break;
			}
			
			$statutEntity = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode($statut);
			
			$entity->setUtilisateur($this->getUser());
			$entity->setStatut($statutEntity);
			$entity->setTache($tache);
			$tache->setEtatCourant($statut);
			
			$em->flush();
			return new JsonResponse(array('url' => $this->generateUrl('actioncyclique_show', array('id' => $actionCyclique->getId()))));
    	}
    	return new Response($this->renderView('OrangeMainBundle:TacheStatut:new.html.twig', array('entity' => $entity, 'etat' => $etat, 'form' => $form->createView())), 303);
    }
    
    
    

//     /**
//      * Creates a form to create a TacheStatut entity.
//      *
//      * @param TacheStatut $entity The entity
//      *
//      * @return \Symfony\Component\Form\Form The form
//      */
//     private function createCreateForm(TacheStatut $entity)
//     {
//         $form = $this->createForm(new TacheStatutType(), $entity, array(
//             'action' => $this->generateUrl('tachestatut_create'),
//             'method' => 'POST',
//         ));

//         $form->add('submit', 'submit', array('label' => 'Create'));

//         return $form;
//     }

//     /**
//      * Displays a form to create a new TacheStatut entity.
//      *
//      * @Route("/new", name="tachestatut_new")
//      * @Method("GET")
//      * @Template()
//      */
//     public function newAction()
//     {
//         $entity = new TacheStatut();
//         $form   = $this->createCreateForm($entity);

//         return array(
//             'entity' => $entity,
//             'form'   => $form->createView(),
//         );
//     }
    
    /**
     * Displays a form to create a new TacheStatut entity.
     *
     * @Route("/tache_statut_nouveau/{tache_id}/{etat}", name="tachestatut_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($tache_id, $etat)
    {
    	$em = $this->getDoctrine()->getManager();
    	$tache = $em->getRepository("OrangeMainBundle:Tache")->find($tache_id);
    	$entity = new TacheStatut();
    	$form = $this->createCreateForm($entity,'TacheStatut');
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView(),
    			'tache'	=> $tache,
    			'etat'	=> $etat
    	);
    }
    
    
    

    /**
     * Finds and displays a TacheStatut entity.
     *
     * @Route("/{id}", name="tachestatut_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TacheStatut entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TacheStatut entity.
     *
     * @Route("/{id}/edit", name="tachestatut_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TacheStatut entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a TacheStatut entity.
    *
    * @param TacheStatut $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TacheStatut $entity)
    {
        $form = $this->createForm(new TacheStatutType(), $entity, array(
            'action' => $this->generateUrl('tachestatut_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TacheStatut entity.
     *
     * @Route("/{id}", name="tachestatut_update")
     * @Method("PUT")
     * @Template("OrangeMainBundle:TacheStatut:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TacheStatut entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tachestatut_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a TacheStatut entity.
     *
     * @Route("/{id}", name="tachestatut_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:TacheStatut')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TacheStatut entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tachestatut'));
    }

    /**
     * Creates a form to delete a TacheStatut entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tachestatut_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Validation
     *
     * @Route("/solder_la_tache/{tache_id}", name="solder_tache")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function solderAction(Request $request, $tache_id, $etat){
    	$em   = $this->getDoctrine()->getManager();
    	$tache = $em->getRepository('OrangeMainBundle:Tache')->find($tache_id);
    	$today = new \DateTime();
    	$dateCloture = $tache->getDateCloture();
    	$dateCloture = new \DateTime($dateCloture);
    	$actionCyclique = $em->getRepository('OrangeMainBundle:Tache')->find($tache_id)->getActionCyclique();
    	$statut = Statut::TACHE_NON_ECHUE_NON_SOLDE;
    	$commentaire = 'La tâche n\'est pas échue, et pas soldée . ';
    	if($request->getMethod() == 'POST') {
			switch ($etat)
			{
				case 'FAITE':
					$statut = Statut::TACHE_FAITE;
					$commentaire = 'La tâche a été exécutée. En attent ede validation . ';
				break;
				case 'SOLDER':
					if($today > $dateCloture)
					{
						$statut = Statut::TACHE_ECHUE_SOLDER;
						$commentaire = 'La tâche est échue et non soldée . ';
					}
					else
					{
						$statut = Statut::TACHE_NON_ECHUE_SOLDER;
						$commentaire = 'La tâche n\'est pas échue, mais soldée. ';
					}
				break;
				case 'NON_SOLDER':
					if($today > $dateCloture)
					{
						$statut = Statut::TACHE_ECHUE_NON_SOLDER;
						$commentaire = 'La tâche est échue mais n\'est pas soldée . ';
					}
					else 
					{
						$statut = Statut::TACHE_NON_ECHUE_NON_SOLDE;
						$commentaire = 'La tâche n\'est pas échue, et pas soldée . ';
					}
				break;
			}    		
    		
			$tache->setEtatCourant($statut);
			$em->persist($tache);
			$em->flush();
			
    		ActionUtils::changeStatutTache($em, $tache, $statut, $this->getUser(), $commentaire);
    		return new JsonResponse(array('url' => $this->generateUrl('actioncyclique_show', array('id' => $actionCyclique->getId()))));
    	}
    	return array('tache' => $tache);
    }
}
