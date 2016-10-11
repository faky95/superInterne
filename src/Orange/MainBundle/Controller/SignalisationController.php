<?php

namespace Orange\MainBundle\Controller;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Criteria\SignalisationCriteria;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\Entity\SignalisationStatut;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Form\LoadingType;
use Orange\MainBundle\Form\ReloadActionType;
use Orange\MainBundle\Form\SignalisationType;
use Orange\MainBundle\OrangeMainEvents;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Utils\InstanceUtils;
use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\Utils\SignalisationUtils;
use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * Signalisation controller.
 * 
 */
class SignalisationController extends BaseController
{

	
	protected $web_dir = WEB_DIRECTORY;
	
	/**
	 * Lists all Signalisation entities.
	 * @QMLogger(message="Liste des signalisations")
	 * @Route("/les_signalisations", name="les_signalisations")
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new SignalisationCriteria(), null, array('attr'=>array( 'user'=>$this->getUser())));
		$data = $request->get($form->getName());
		if($request->getMethod()=='POST') {
			if(isset($data['effacer'])) {
				$this->get('session')->set('signalisation_criteria', new Request());
			} else {
				$this->get('session')->set('signalisation_criteria', $request->request->get($form->getName()));
				$form->handleRequest($request);
			}
		} else {
			$this->get('session')->set('signalisation_criteria', new Request());
		}
		return array('form' => $form->createView());
	}
	
	/**
	 * Lists  entities.
	 *
	 *@Route("/liste_des_signalisations", name="liste_des_signalisations")
	 * @Method("GET")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new SignalisationCriteria(), null, array('attr'=>array( 'user'=>$this->getUser())));
		$this->modifyRequestForForm($request, $this->get('session')->get('signalisation_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Signalisation')->listAllElements($criteria);
		$this->get('session')->set('data',array('query' => $queryBuilder->getDql(),'param' =>$queryBuilder->getParameters()) );
		$queryCanevas = $em->getRepository('OrangeMainBundle:Signalisation')->forCanevas($criteria);
		$this->get('session')->set('canevas',array('query' => $queryCanevas->getDql(),'param' =>$queryCanevas->getParameters()) );
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @QMLogger(message="Extraction des signalisation")
	 * @Route("/export_signalisation", name="export_signalisation")
	 * @Template()
	 */
	public function exportAction() {
		$em = $this->getDoctrine()->getEntityManager();
		$queryBuilder = $this->get('session')->get('data', array());
		$query = $em->createQuery($queryBuilder['query']);
		$query->setParameters($queryBuilder['param']);
		$statut = $em->getRepository('OrangeMainBundle:Statut')->listAllStatutSign();
		$data = $this->get('orange.main.data')->exportSignalisation($query->execute(),  $statut->getQuery()->execute());
		$objWriter = $this->get('orange.main.extraction')->exportSignalisation($data);
		$filename = sprintf("Extraction_des_signalisations_du_%s.xlsx", date('d-m-Y'));
		$objWriter->save($this->web_dir."/upload/reporting/$filename");
		return $this->redirect($this->getUploadDir().$filename);
	}
	/**
	 * @QMLogger(message="Extraction des canevas")
	 * @Route("/export_canevas", name="export_canevas")
	 * @Template()
	 */
	public function exportCanevasAction() {
		$em = $this->getDoctrine()->getEntityManager();
		$queryBuilder = $this->get('session')->get('canevas', array());
		$query = $em->createQuery($queryBuilder['query']);
		$query->setParameters($queryBuilder['param']);
		$data = $this->get('orange.main.data')->exportCanevas($query->execute());
		$objWriter = $this->get('orange.main.extraction')->exportCanevas($data);
		$filename = sprintf("Extraction_canevas_actions_du_%s.csv", date('d-m-Y'));
		$objWriter->save($this->web_dir."/upload/reporting/$filename");
		return $this->redirect($this->getUploadDir().$filename);
	}
	
	
    /**
     * @Route("/traitement_signalisation/{signalisation_id}", name="traitement_signalisation")
     * @Template()
     */
    public function traitementAction($signalisation_id){
    	$em = $this->getDoctrine()->getManager();
    	$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
    	
    	$animateur = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('signalisation' => $signalisation_id, 'actif' => true));
    	
    	if($animateur){
    		$animateur = $animateur->getUtilisateur();
    		$manager = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $animateur->getStructure()->getId(), 'isAdmin' => true));
    	}else{
    		$animateur = trim(' ');
    		$manager = trim(' ');
    	}
    	
    	return array('entity' => $signalisation,
    				 'animateur' => $animateur,
    				 'manager'	=> $manager);
    }    
    
    /**
     * Creates a new Signalisation entity.
     * @QMLogger(message="Création signalisation")
     * @Route("/creer_signalisation", name="creer_signalisation")
     * @Method("POST")
     * @Template("OrangeMainBundle:Signalisation:new.html.twig")
     */
    public function createAction(Request $request)
    {
    	$dispatcher = $this->container->get('event_dispatcher');
    	$em = $this->getDoctrine()->getManager();
        $entity = new Signalisation();
        $form = $this->createCreateForm($entity,'Signalisation', array(
        														'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())));
		       
        $form->handleRequest($request);
	
        if ($request->getMethod() == 'POST' ) 
        {
	        if ($form->isValid()) 
	        {
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($entity);
	            $instance_id = $entity->getInstance()->getId();
	            $source = $em->getRepository('OrangeMainBundle:Source')->findOneBy(array('instance'=>$instance_id, 'utilisateur'=>$this->getUser()->getId()));
	            $entity->setSource($source);
	            $em->flush();
	            SignalisationUtils::setReferenceSignalisation($em, $entity);
	            SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::NOUVELLE_SIGNALISATION, $entity, "Nouvelle signalisation ajoutée. En attente de prise en charge !");
	            $event = $this->get('orange_main.signalisation_event')->createForSignalisation($entity);
	            $dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_CREATE_NOUVELLE, $event);
	            $this->get('session')->getFlashBag()->add('success', array (
									            		  'title' => 'Notification',
									            		  'body' => 'Enrégistrement effectué avec succès'
	            ));
	            return $this->redirect($this->generateUrl('details_signalisation', array('id' => $entity->getId())));
	        }
	        
	        if(!$form->isValid())
	        {
	        	$this->get('session')->getFlashBag()->add('error', array (
									        			  'title' => 'Notification',
									        			  'body' => 'Une erreur est survenue. Veuillez réessayer.'
	        	));
	        }
        }
    
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Signalisation entity.
     *
     * @param Signalisation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
    
    private function createCreateForm(Signalisation $entity, $usr_id, $em)
    {
        $form = $this->createForm(new SignalisationType($usr_id, $em), $entity, array(
            'action' => $this->generateUrl('creer_signalisation'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    } */

    /**
     * Displays a form to create a new Signalisation entity.
     * @QMLogger(message="Nouvelle signalisation")
     * @Route("/nouvelle_signalisation", name="nouvelle_signalisation")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
    	
        $entity = new Signalisation();
        $form   = $this->createCreateForm($entity,'Signalisation', array(
	   					'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
        	));
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Finds and displays a Signalisation entity.
     * @QMLogger(message="Visualisation signalisation")
     * @Route("/details_signalisation/{id}", name="details_signalisation")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Signalisation entity.');
        }
		
        return array(
            'entity'      => $entity
        );
    }
	
    /**
     * Displays a form to edit an existing Signalisation entity.
     * @QMLogger(message="Modification signalisation")
     * @Route("/{id}/edition_signalisation/", name="edition_signalisation")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Signalisation entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form'   => $editForm->createView());
    }
    
   /**
    * Creates a form to edit a Signalisation entity.
    *
    * @param Signalisation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Signalisation $entity)
    {
        $form = $this->createForm(new SignalisationType(), $entity, array(
            'action' => $this->generateUrl('modification_signalisation', array('id' => $entity->getId())),
            'method' => 'PUT',
        	'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
   /**
     * Edits an existing Signalisation entity.
     *
     * @Route("/modification_signalisation/{id}", name="modification_signalisation")
     * @Method("POST")
     * @Template("OrangeMainBundle:Signalisation:edit.html.twig")
     */
    public function updateSignalisation(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
    	$form = $this->createCreateForm($entity, 'Signalisation', array(
        					'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
    		));
    	$request = $this->get('request');
    	$today = new \DateTime();
    	$today = $today->format('Y-m-d');
    	if ($request->getMethod() == 'POST')
    	{
    		$form->handleRequest($request);
    		if ($form->isValid())
    		{
    			$em->persist($entity);
    			$em->flush();
    			return $this->redirect($this->generateUrl('edition_signalisation', array('id' => $id)));
    		}
    	}
    	return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    
    
    
		/**
	     * Deletes a Signalisation entity.
	     *
	     * @Route("/{id}/supprimer_signalisation", name="supprimer_signalisation")
	     * @Method("GET")
	     * @Security("has_role('ROLE_ADMIN')")
	     */
	    public function deleteAction(Request $request, $id)
	    {
	            $em = $this->getDoctrine()->getManager();
	            $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
	
	            if ($entity) {
	            	if($entity->getAction()->count()>0){
	            		$this->get('session')->getFlashBag()->add('failed', array (
	            				'title' =>'Notification',
	            				'body' => 'La suppresion impossible ! '
	            		));
	            	}else {
			            $em->remove($entity);
	           			 $em->flush();
	           			 $this->get('session')->getFlashBag()->add('success', array (
	           			 		'title' =>'Notification',
	           			 		'body' => 'La suppresion de la signalisation � �t� annule avec succes ! '
	           			 ));
	            	}
	            }else{
	                throw $this->createNotFoundException('Signalisation inexistant.');
	            }
	        return $this->redirect($this->generateUrl('les_signalisations'));
	    }
    

    /**
     * Creates a form to delete a Signalisation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_signalisation', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * @Route("/ss_instance", name="ss_instance")
     */
    public function ssInstanceAction(Request $request)
    {
    	
    	if($request->isXmlHttpRequest()) {
    		$instance_id = $request->request->get('instance_id');
    		$em = $this->getDoctrine()->getManager();
	    	$queryBuilder = $em->createQueryBuilder();
	    	
    	$queryBuilder->select('i')
    	->from('OrangeMainBundle:Instance', 'i')
    	->where('i.parent = :p')
    	->setParameter('p', $instance_id);
   
    	return new JsonResponse( $queryBuilder->getQuery()->getArrayResult()); 
    	}

    }
    
    /**
     * @Route("/validation_signalisation/{valide}/{id}", name="valider_signalisation")
     */
    public function validerSignalisationAction(Request $request, $valide, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
    	$typeStatut = $em->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_SIGNALISATION);
    	$statutSignalisation = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $valide, 'typeStatut' => $typeStatut));
    	return $this->redirect($this->generateUrl('signalisationstatut_new', array('signalisation_id' => $signalisation->getId(), 
    																			   'statut_id' => $statutSignalisation->getId()
    						  					 )));
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Signalisation $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getReference(),
    			$entity->getLibelle(),
    			$entity->getSource()->__toString(),
    			$entity->getDateConstat()->format("d-m-Y"),
    			$entity->getDateSignale()->format("d-m-Y"),
    			$this->get('orange_main.actions')->generateActionsForSignalisation($entity)
    	);
    }
    
    
    
    /**
     * @Route("/actions_correctives/{signalisation_id}", name="actions_correctives")
     * @Template()
     */
   	public function actionsAction($signalisation_id)
   	{
   		
   		$em = $this->getDoctrine()->getManager();
   		$actionId = $em->getRepository('OrangeMainBundle:Signalisation')->actionSignalisationId($signalisation_id);
   		
   		if(!$actionId)
   		{
   			
   		}
   		
		$actionCorrectives = array();
		foreach ($actionId as $id)
		{
			$action =  $em->getRepository('OrangeMainBundle:Action')->find(intval($id['action_id']));
			array_push($actionCorrectives, $action);
		}
   		
   		
   		return array('actions' => $actionCorrectives);
   	}
   	
   	
   	/**
   	 * @Route("/reload_actions/{signalisation_id}", name="reload_actions")
   	 * @Method({"GET", "POST"})
   	 * @Template()
   	 */
   	public function reloadAction(Request $request, $signalisation_id)
   	{
   		$em = $this->getDoctrine()->getManager();
   		$actionId = $em->getRepository('OrangeMainBundle:Signalisation')->actionSignalisationId($signalisation_id);
   		$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
   		$allActionStatut = $em->getRepository("OrangeMainBundle:SignalisationStatut")->findBySignalisation($signalisation_id);
   		$actionStatut = $allActionStatut[count($allActionStatut) - 1 ];
   		$membresEmail = $this->getSignalisationMembresEmail($em, $actionStatut);
   		$now = new \DateTime ();
   		$now = $now->format ('d-m-Y') . " à " . $now->format('H:i:s');
   		$helper = $this->get('orange.main.mailer');
   		
   		if(!empty($actionId))
   		{
   			$actionsReload = array();
   			foreach ($actionId as $id)
   			{
   				$action =  $em->getRepository('OrangeMainBundle:Action')->find(intval($id['action_id']));
   				array_push($actionsReload, $action);
   			}
   			
   			/**
   			 * -- Here is my problem , how can i my $questions into form? ---
   			 **/
   			$form = $this->createFormBuilder()
   			->add('isReload', 'collection', array(
   					'type' => new ReloadActionType() ,
   					'allow_add' => false,
   					'allow_delete' => false,
   					'label' => false))
   					->add('save', 'submit', array('label' => 'Reload'))
   					->getForm();
   			
   			$form->setData(array('isReload' => $actionsReload));
   			$form->handleRequest($request);
   			if ($form->isValid())
   			{
   				SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::TRAITEMENT_SIGNALISATION, $signalisation, "Cette signalisation a été reconduite suite à un mauvais traitement! Les actions mal traitées ont été rechargées !");
   				$this->updateEntityEtat($em, Statut::TRAITEMENT_SIGNALISATION, $signalisation);
   				foreach ($actionsReload as $action)
   				{
   					$action->setDateDebut(new \DateTime());
   					$action->setDateInitial(new \DateTime());
   					
   					if($action->getIsReload())
   					{
   						ActionUtils::changeStatutAction($em, $action, Statut::NOUVELLE_ACTION, $this->getUser(), " Cette action a été rechargée suite à un mauvais traitement de la signalisation ");
   						$action->setEtatCourant(Statut::NOUVELLE_ACTION);
   						$subject = 'Traitement de la signalisation '.$signalisation->getLibelle();
   						$commentaire = 'Le ' . $now . ', l\'action intitulé : ' . $action->getLibelle () . ' a été rechargé suite à un mauvais traitement de la signalisation
										à l\'origne de cette action. '.$action->getPorteur().' est invité à se connecter et confirmer la prise en charge de cette action,
										ou faire une contre proposition au besoin. L\'animateur en charge du suivi de cette action peut modifier ultérieurement de cette action rechargée';
   						$em->flush();
   						Notification::notification ( $helper, $subject, $membresEmail, $commentaire, $actionStatut );
   					}
   				}
   				return $this->redirect($this->generateUrl('details_signalisation', array('id' =>$signalisation_id)));
   			}
   		}
		   		
   		return $this->render('OrangeMainBundle:Signalisation:reload.html.twig', array("id" =>$signalisation_id, 'entity' => $signalisation, "form" => $form->createView()));
   	}
   	
   	
   	public function getSignalisationMembresEmail($em, $entity)
   	{
   		$membreEmail = array ();
   		$source = $entity->getSignalisation()->getSource()->getUtilisateur();
   		array_push ( $membreEmail, $source->getEmail () );
   		$animateur = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('actif' => true, 'signalisation' => $entity->getSignalisation()->getid()));
   		if($animateur)
   		{
   			array_push($membreEmail, $animateur->getUtilisateur()->getEmail());
   			$structureAnimateur = $animateur->getUtilisateur()->getStructure();
   			$managerAnimateur = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $structureAnimateur->getid(), 'manager' => true));
   			if($managerAnimateur)
   			{
   				array_push($membreEmail, $managerAnimateur->getEmail());
   			}
   		}

   		return $membreEmail;
   	}
   	
   	
   	public function updateEntityEtat($entityManager, $currentStatus, $entity)
   	{
   		$entity->setEtatCourant($currentStatus);
   		$entityManager->persist($entity);
   		$entityManager->flush();
   	}
   	
   	/*
   	 * reperetoire de sauvegarde des reporting
   	*/
   	private function getUploadDir() {
   		return $this->getRequest()->getBaseUrl().($this->get('kernel')
   				->getEnvironment()=='prod' ? '' : '/..')."/upload/reporting/";
   	}
   	
   	
	/**
     * @Route("/chargement_signalisation", name="chargement_signalisation")
     * @Template()
     */
    public function loadingAction() {
        $form = $this->createForm(new LoadingType());
        return array('form' => $form->createView());
    }
    
    /**
     * @QMLogger(message="Importation signalisation")
     * @Route("/importer_signalisation", name="importer_signalisation")
     * @Method("POST")
     * @Template()
     */
    public function importAction(Request $request) {
        $form = $this->createForm(new LoadingType());
        $em   = $this->getDoctrine()->getManager();
        $sources = $em->getRepository('OrangeMainBundle:Source')->getAllSources();
        $form->handleRequest($request);
        if($form->isValid()) {
            $data = $form->getData();
            try {
                $number = $this->get('orange.main.loader')->loadSignalisation($data['file'],$sources, $this->getUser());
           		$signs = $em->getRepository('OrangeMainBundle:Signalisation')->getSignalisations($number['id']);
                $nbr = $number['nbr'];
                foreach ($signs as $value){
                	$helper = $this->get('orange.main.mailer');
                	$subject = "Nouvelle Signalisation";
                	$instance = $value->getInstance();
                	$animateur = $instance->getAnimateur()->count()==0
						? $instance->getParent()->getAnimateur()->get(0)->getUtilisateur()->getNomComplet()
						: $instance->getAnimateur()->get(0)->getUtilisateur()->getNomComplet();
                	$destinataire = InstanceUtils::animateursEmail($em, $instance);
                	$commentaire = 'Une nouvelle signalisation a été postée par '.$this->getUser()->getCompletNom().' au périmétre: '.$instance->getLibelle().'. '
                			.$animateur.' est prié de prendre en charge cette signalisation. ';
                	$signStatut = $em->getRepository('OrangeMainBundle:SignalisationStatut')->getStatut($value->getId());
                	Notification::notificationSignWithCopy($helper, $subject, $destinataire, array('madisylla@orange.sn',$value->getSource()->getUtilisateur()->getEmail()), $commentaire, $signStatut[0]);
                	//$this->get('orange.main.mailer')->notifNewSignalisation($destinataire,array('madisylla@orange.sn', $value->getSource()->getUtilisateur()->getEmail()), $value);
                }
                $this->get('session')->getFlashBag()->add('success', "Le chargement s'est effectué avec succés! Nombre de signalisation chargé: $nbr");
                return $this->redirect($this->generateUrl('les_signalisations'));
            } catch(ORMException $e) {
            	$this->get('session')->getFlashBag()->add('error', array ('title' => 'Message d\'erreur', 'body' => nl2br($e->getMessage())
            	));
            }
        }
        return $this->render('OrangeMainBundle:Signalisation:loading.html.twig', array('form' => $form->createView()));
    }
    
    
    /**
     * @Route("/typesignalisation_by_instance", name="typesignalisation_by_instance")
     * @Template()
     */
    public function listTypeSignalisationByInstanceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$instance = $em->getRepository('OrangeMainBundle:Instance')->find($request->request->get('id'));
    	$id = $instance->getParent()?$instance->getParent()->getId():$instance->getId();
    	$arrData = $em->getRepository('OrangeMainBundle:TypeAction')->listTypeByInstance($id);
    	$output = array(0 => array('id' => null, 'libelle' => 'Choisir un type  ...'));
    	foreach ($arrData as $data) {
    		$output[] = array('id' => $data['id'], 'libelle' => $data['type']);
    	}
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/json');
    	return $response->setContent(json_encode($output));
    }
    
    /**
     * @Route("/domaine_signalisation_by_instance", name="domaine_signalisation_by_instance")
     * @Template()
     */
    public function listDomaineSignalisationByInstanceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$instance = $em->getRepository('OrangeMainBundle:Instance')->find($request->request->get('id'));
    	$parent = $instance->getParent();
    	if($parent && $parent->getConfiguration()) {
        	$arrData = $em->getRepository('OrangeMainBundle:Domaine')->listByInstance($parent);
    	} else {
    		$arrData = $em->getRepository('OrangeMainBundle:Domaine')->listDomaineByInstance($parent, $instance->getLibelle());
    	}
        $output = array(0 => array('id' => null, 'libelle' => 'Choisir un domaine  ...'));
        foreach ($arrData as $data) {
            $output[] = array('id' => $data['id'], 'libelle' => $data['libelleDomaine']);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response->setContent(json_encode($output));
    }
    
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('sign.reference', 'sign.libelle'), $request);
    }
    
//     protected function setOrder(QueryBuilder $queryBuilder, $aColumns, Request $request) {
//     	parent::setOrder($queryBuilder, array(null, 'a.reference', 'mi.libelle', 'a.libelle', 'mp.prenom', 'sr.libelle'), $request);
//     }
    
}
