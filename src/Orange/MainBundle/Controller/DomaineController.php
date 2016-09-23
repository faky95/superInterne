<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Domaine;
use Orange\MainBundle\Form\DomaineType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Criteria\DomaineCriteria;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Domaine controller.
 *
 */
class DomaineController extends BaseController
{

    /**
     * Lists all Domaine entities.
     * @QMLogger(message="Liste des domaines")
     * @Route("/les_domaine", name="les_domaine")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction($espace_id=null)
    {
        $this->get('session')->set('domaine_criteria', new Request());
    	return array('espace_id'=>$espace_id);
    }
    
    /**
     * Lists all Domaine by espace.
     *
     * @Route("/{espace_id}/les_domaine_by_espace", name="les_domaine_by_espace")
     * @Method("GET")
     * @Template("OrangeMainBundle:Domaine:index.html.twig")
     */
    public function domainesEspaceAction($espace_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	if ($espace_id){
    		$entity = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    		$user = $em->getRepository('OrangeMainBundle:Utilisateur')->find($this->getUser()->getId());
    		$membre=$em->getRepository('OrangeMainBundle:MembreEspace')->findOneBy(
    				array('utilisateur' => $user, 'espace' => $entity));
    		$actions = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->allActionEspace($espace_id);
    		$act = $this->getDoctrine()->getRepository('OrangeMainBundle:Action')->listActionsUserByEspace($this->getUser()->getId(), $espace_id);
    		$gestionnaire = $membre->getIsGestionnaire();
    	}
    	$this->get('session')->set('domaine_criteria', new Request());
    	$espace=$this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    	return array('espace_id'=>$espace_id, 'espace'=>$espace, 'gest' => $gestionnaire, 'nbrTotal' => count($actions), 'nbr' => count($act));
    }
    
    /**
     *
     * @Method("GET")
     * @Template()
     */
    public function listeAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$entities = $em->getRepository('OrangeMainBundle:Domaine')->findAll();
    	return array(
    			'entities' => $entities,
    	);
    }
    
    /**
     * Creates a new Domaine entity.
     * @QMLogger(message="Création de domaine")
     * @Route("/creer_domaine", name="creer_domaine")
     * @Route("/{espace_id}/creer_domaine_to_espace", name="creer_domaine_to_espace")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request,$espace_id=null){
        $entity = new Domaine();
        if($espace_id!=null)
        $espace=$this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
        $form = $this->createCreateForm($entity,'Domaine', array(
        										'attr' => array('security_context' => $this->get('security.context'))
        ));
        
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN'))
        {
        	$form->remove('bu');
        }
        $form->handleRequest($request);
        if($request->getMethod() === 'POST'){
		        if ($form->isValid()) {
		            $em = $this->getDoctrine()->getManager();
		            $em->persist($entity);
		            	if($espace_id!=null){
		            		$espace->getInstance()->addDomaine($entity);
		            		$em->persist($espace->getInstance());
		            	}else{
			            		if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')){
					            	$bu = $this->getUser()->getStructure()->getBuPrincipal();
					            	$bu->addDomaine($entity);
			            		}
		            	}
		            
		            $em->flush();
		            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Le domaine a été créé avec succès'));
		            if($espace_id!=null)
		            	return new JsonResponse(array('url' => $this->generateUrl('les_domaine_by_espace', array('espace_id'=>$espace_id))));
		            else
		          	  return new JsonResponse(array('url' => $this->generateUrl('les_domaine')));
				}
        }
        return $this->render('OrangeMainBundle:Domaine:new.html.twig',
        		array(
        				'entity' => $entity,
        				'form'   => $form->createView(),
        		), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
    /**
     * Displays a form to create a new Domaine entity.
     * @QMLogger(message="Nouvelle domaine")
     * @Route("/nouveau_domaine", name="nouveau_domaine")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction($espace_id=null)
    {
        $entity = new Domaine();
        $form   = $this->createCreateForm($entity,'Domaine', array(
        										'attr' => array('security_context' => $this->get('security.context'))
        ));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'espace_id'=> $espace_id
        );
    }
    
    /**
     * Displays a form to create a new Domaine entity.
     *
     * @Route("/{espace_id}/nouveau_domaine_to_espace", name="nouveau_domaine_to_espace")
     * @Method("GET")
     * @Template("OrangeMainBundle:Domaine:new.html.twig")
     */
    public function newDomaineEspaceAction($espace_id)
    {
    	$entity = new Domaine();
    	$form   = $this->createCreateForm($entity,'Domaine', array(
    			'attr' => array('security_context' => $this->get('security.context'))
    	));
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView(),
    			'espace_id' =>$espace_id,
    	);
    }

    /**
     * Finds and displays a Domaine entity.
     * @QMLogger(message="Visualisation de domaine")
     * @Route("/details_domaine/{id}/", name="details_domaine")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Domaine entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Domaine entity.
     * @QMLogger(message="Modification de domaine")
     * @Route("/edition_domaine/{id}/", name="edition_domaine")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Domaine entity.');
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
    * Creates a form to edit a Domaine entity.
    *
    * @param Domaine $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Domaine $entity)
    {
        $form = $this->createForm(new DomaineType(), $entity, array(
            'action' => $this->generateUrl('modifier_domaine', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Domaine entity.
     *
     * @Route("/modifier_domaine/{id}/", name="modifier_domaine")
     * @Method("POST")
     * @Template("OrangeMainBundle:Domaine:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);
        $form = $this->createCreateForm($entity,'Domaine');
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Le domaine a été modifié avec succès'));
        		return new JsonResponse(array('url' => $this->generateUrl('les_domaine')));
        	}
        }
        return $this->render('OrangeMainBundle:Domaine:edit.html.twig',
        		array(
        				'entity' => $entity,
        				'edit_form'   => $form->createView(),
        		), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
  /**
     * Deletes a Domaine entity.
     *
     * @Route("/{id}/supprimer_domaine", name="supprimer_domaine")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);

            if ($entity) {
            	
            	if($entity->getAction()->count()>0){
            		$this->container->get('session')->getFlashBag()->add('failed', array (
    					'title' =>'Notification',
    					'body' => 'Cette structure est rattache a des actions ! '
    				));
            	}else {
            		$em->remove($entity);
           			$em->flush();
           			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Le domaine a été supprimé avec succès'));
            	}
            }else{
                throw $this->createNotFoundException('Domaine inexistant.');
            }
        
        return $this->redirect($this->generateUrl('les_domaine'));
    }
    

    /**
     * Creates a form to delete a Domaine entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_domaine', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_domaines", name="liste_des_domaines")
     *@Route("/{espace_id}/liste_des_domaines_by_espace", name="liste_des_domaines_by_espace")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request,$espace_id=null) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new DomaineCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('domaine_criteria'), $form);
    	if($espace_id!=null)
    		$queryBuilder = $em->getRepository('OrangeMainBundle:Domaine')->getDomainesByEspace($espace_id);
    	else
    		$queryBuilder = $em->getRepository('OrangeMainBundle:Domaine')->listQueryBuilder();
    	$this->get('session')->set('data',array('query' => $queryBuilder->getDql(),'param' =>$queryBuilder->getParameters()) );
    	return $this->paginate($request, $queryBuilder);
    }
    
    
    /**
     * @Route("/filtrer_les_domaines", name="filtrer_les_domaines")
     * @Template()
     */
    
    public function filterAction(Request $request) {
    	$form = $this->createForm(new DomaineCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('domaine_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('domaine_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Domaine $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getLibelleDomaine(),
    			$this->get('orange_main.actions')->generateActionsForDomaine($entity)
    	);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('d.libelleDomaine'), $request);
    }
}
