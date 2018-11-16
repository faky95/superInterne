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
use Orange\MainBundle\Entity\Extraction;

/**
 * Signalisation controller.
 */
class SignalisationController extends BaseController
{
	
	/**
	 * Lists all Signalisation entities.
	 * @QMLogger(message="Liste des signalisations")
	 * @Route("/les_signalisations", name="les_signalisations")
	 * @Template()
	 */
	public function indexAction(Request $request)
	{
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
	 * @QMLogger(message="Liste des signalisations avec pagination")
	 * @Route("/liste_des_signalisations", name="liste_des_signalisations")
	 * @Method("GET")
	 * @Template()
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new SignalisationCriteria(), null, array('attr'=>array( 'user'=>$this->getUser())));
		$this->modifyRequestForForm($request, $this->get('session')->get('signalisation_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Signalisation')->listAllElements($criteria);
		$queryBuilderExport = $em->getRepository('OrangeMainBundle:Signalisation')->listAllForExport($criteria);
		$this->get('session')->set('data',array('query' => $queryBuilderExport->getDql(),'param' =>$queryBuilderExport->getParameters()) );
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
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $this->get('session')->get('data', array());
		if($queryBuilder['totalNumber'] > 5000) {
			$type = \Orange\MainBundle\Entity\Extraction::$types['signalisation'];
			$extraction = Extraction::nouvelleTache($queryBuilder['totalNumber'], $this->getUser(), $queryBuilder['query'], serialize($queryBuilder['param']), $type);
			$em->persist($extraction);
			$em->flush();
			$this->addFlash('warning', "L'extraction risque de prendre du temps, le fichier vous sera envoyé par mail");
			return $this->redirect($this->getRequest()->headers->get('referer'));
		}
		$query = $em->createQuery($queryBuilder['query']);
		$query->setParameters($queryBuilder['param']);
		$statut = $em->getRepository('OrangeMainBundle:Statut')->listAllStatutSign();
		$data = $this->getMapping()->getExtraction()->exportSignalisation($query->execute(), $statut->getQuery()->execute());
		//var_dump($data[0]); exit();
		$objWriter = $this->get('orange.main.extraction')->exportSignalisation($data);
		$filename = sprintf("Extraction_des_signalisations_du_%s.xlsx", date('d-m-Y'));
		$objWriter->save($this->get('kernel')->getWebDir()."/upload/reporting/$filename");
		return $this->redirect($this->getUploadDir().$filename);
	}
	/**
	 * @QMLogger(message="Extraction des signalisations sous le canevas du fichier de chargment des actions")
	 * @Route("/export_canevas", name="export_canevas")
	 * @Template()
	 */
	public function exportCanevasAction() {
        $response = new Response();
		$em = $this->getDoctrine()->getManager();
		$queryBuilder = $this->get('session')->get('canevas', array());
		$query = $em->createQuery($queryBuilder['query']);
		$query->setParameters($queryBuilder['param']);
		$data = $this->getMapping()->getExtraction()->setEntityManager($em)->exportCanevas($query->execute());
		$objWriter = $this->get('orange.main.extraction')->exportCanevas($data);
		$filename = sprintf("Extraction_canevas_actions_du_%s.csv", date('d-m-Y'));
        $response->headers->set('Content-Type', 'text/csv; charset=ISO-8859-1;');
        $response->headers->set('content-disposition', 'attachement; filename = '.$filename);
        $response->sendHeaders();
        $objWriter->save('php://output');
		return $response;
	}
	
    /**
	 * @QMLogger(message="Traitement d'une signalisation")
     * @Route("/traitement_signalisation/{signalisation_id}", name="traitement_signalisation")
     * @Template()
     */
    public function traitementAction($signalisation_id) {
    	$em = $this->getDoctrine()->getManager();
    	$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
    	if(!$signalisation) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
    	}
    	$animateur = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('signalisation' => $signalisation_id, 'actif' => true));
    	if($animateur){
    		$animateur = $animateur->getUtilisateur();
    		$manager = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $animateur->getStructure()->getId(), 'isAdmin' => true));
    	} else {
    		$animateur = trim(' ');
    		$manager = trim(' ');
    	}
    	return array('entity' => $signalisation, 'animateur' => $animateur, 'manager'	=> $manager);
    }    
    
    /**
     * Creates a new Signalisation entity.
     * @QMLogger(message="Créer une signalisation")
     * @Route("/creer_signalisation", name="creer_signalisation")
     * @Method("POST")
     * @Template("OrangeMainBundle:Signalisation:new.html.twig")
     */
    public function createAction(Request $request) {
    	$dispatcher = $this->container->get('event_dispatcher');
    	$em = $this->getDoctrine()->getManager();
        $entity = new Signalisation();
        $form = $this->createCreateForm($entity,'Signalisation', array(
        		'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
        	));
        $form->handleRequest($request);
        if ($request->getMethod() == 'POST' ) {
	        if ($form->isValid()) {
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
						'title' => 'Notification', 'body' => 'Enregistrement effectué avec succès'
	            	));
	            return $this->redirect($this->generateUrl('details_signalisation', array('id' => $entity->getId())));
	        }
	        if(!$form->isValid()) {
	        	$this->get('session')->getFlashBag()->add('error', array (
						'title' => 'Notification', 'body' => 'Une erreur est survenue. Veuillez réessayer.'
	        		));
	        }
        }
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Displays a form to create a new Signalisation entity.
     * @QMLogger(message="Ajout d'une signalisation")
     * @Route("/nouvelle_signalisation", name="nouvelle_signalisation")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Signalisation();
        $form   = $this->createCreateForm($entity,'Signalisation', array(
	   			'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
        	));
        return array('entity' => $entity, 'form'   => $form->createView());
    }

    /**
     * Finds and displays a Signalisation entity.
     * @QMLogger(message="Visualisation d'une signalisation")
     * @Route("/details_signalisation/{id}", name="details_signalisation")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
        if(!$entity) {
    		$this->addFlash('error', "Impossible d'afficher les détails, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
        }
        return array('entity' => $entity);
    }
	
    /**
     * Displays a form to edit an existing Signalisation entity.
     * @QMLogger(message="Edition d'une signalisation")
     * @Route("/{id}/edition_signalisation/", name="edition_signalisation")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
        if(!$entity) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }
    
   /**
    * Creates a form to edit a Signalisation entity.
    * @param Signalisation $entity The entity
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
     * @Route("/modification_signalisation/{id}", name="modification_signalisation")
     * @QMLogger(message="Modifier une signalisation")
     * @Method("POST")
     * @Template("OrangeMainBundle:Signalisation:edit.html.twig")
     */
    public function updateSignalisation(Request $request, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
    	if(!$entity) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
    	}
    	$form = $this->createCreateForm($entity, 'Signalisation', array(
        		'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
    		));
    	$request = $this->get('request');
    	$today = new \DateTime();
    	$today = $today->format('Y-m-d');
    	if ($request->getMethod() == 'POST') {
    		$form->handleRequest($request);
    		if ($form->isValid()) {
    			$em->persist($entity);
    			$em->flush();
    			return $this->redirect($this->generateUrl('edition_signalisation', array('id' => $id)));
    		}
    	}
    	return array('entity' => $entity, 'edit_form' => $form->createView());
    }
    
	/**
     * Deletes a Signalisation entity.
     * @Route("/{id}/supprimer_signalisation", name="supprimer_signalisation")
     * @QMLogger(message="Supprimer une signalisation")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
        if($entity) {
           	if($entity->getAction()->count()>0) {
           		$this->get('session')->getFlashBag()->add('failed', array ('title' =>'Notification', 'body' => 'La suppresion impossible ! '));
           	} else {
	            $em->remove($entity);
       			$em->flush();
       			$this->get('session')->getFlashBag()->add('success', array (
       					'title' =>'Notification', 'body' => 'La suppresion de la signalisation a été annulé avec succes ! '
           			 ));
            	}
        } else {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('details_signalisation', array('id' => $id)));
        }
        return $this->redirect($this->generateUrl('les_signalisations'));
    }
    
    /**
     * Creates a form to delete a Signalisation entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_signalisation', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    
    /**
     * @Route("/ss_instance", name="ss_instance")
     * @QMLogger(message="Liste des périmètres d'une instance")
     */
    public function ssInstanceAction(Request $request) {
   		$instance_id = $request->request->get('instance_id');
   		$em = $this->getDoctrine()->getManager();
    	$queryBuilder = $em->createQueryBuilder();
    	$queryBuilder->select('i')
	    	->from('OrangeMainBundle:Instance', 'i')
	    	->where('i.parent = :p')
	    	->setParameter('p', $instance_id);
   		return new JsonResponse($instance_id ? $queryBuilder->getQuery()->getArrayResult() : array()); 
    }
    
    /**
     * @Route("/validation_signalisation/{valide}/{id}", name="valider_signalisation")
     * @QMLogger(message="Prendre en charge ou rejeter une signalisation")
     */
    public function validerSignalisationAction(Request $request, $valide, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
    	$typeStatut = $em->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_SIGNALISATION);
    	$statutSignalisation = $em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $valide, 'typeStatut' => $typeStatut));
    	if(!$signalisation || !$statutSignalisation) {
    		$this->addFlash('erreur', "Cette opération ne peut être effectuée, la signalisation ou bien son statut n'est pas reconnu");
    		return $this->redirect($this->generateUrl('details_signalisation', array('id' => $id)));
    	}
    	return $this->redirect($this->generateUrl('signalisationstatut_new', array(
    			'id' => $signalisation->getId(), 'valide' => $statutSignalisation->getCode()
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
     * @QMLogger(message="Liste des actions correctives d'une signalisation")
     * @Template()
     */
   	public function actionsAction($signalisation_id) {
   		$em = $this->getDoctrine()->getManager();
   		$actionId = $em->getRepository('OrangeMainBundle:Signalisation')->actionSignalisationId($signalisation_id);
		$actionCorrectives = array();
		foreach ($actionId as $id) {
			$action =  $em->getRepository('OrangeMainBundle:Action')->find(intval($id['action_id']));
			array_push($actionCorrectives, $action);
		}
   		return array('actions' => $actionCorrectives);
   	}
   	
   	
   	/**
   	 * @Route("/reload_actions/{signalisation_id}", name="reload_actions")
     * @QMLogger(message="Remettre en <Non échu> les actions échues d'une signalisation")
   	 * @Method({"GET", "POST"})
   	 * @Template()
   	 */
   	public function reloadAction(Request $request, $signalisation_id) {
   		$em = $this->getDoctrine()->getManager();
   		$actionId = $em->getRepository('OrangeMainBundle:Signalisation')->actionSignalisationId($signalisation_id);
   		$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
   		$allActionStatut = $em->getRepository("OrangeMainBundle:SignalisationStatut")->findBySignalisation($signalisation_id);
		   $actionStatut = count($allActionStatut) ? $allActionStatut[count($allActionStatut) - 1 ] : array();

		$membresEmail = $this->getSignalisationMembresEmail($em, $signalisation);
		   
   		$now = new \DateTime ();
   		$now = $now->format ('d-m-Y') . " à " . $now->format('H:i:s');
   		$helper = $this->get('orange.main.mailer');
   		if(!empty($actionId)) {
			  
   			$actionsReload = array();
   			foreach ($actionId as $id) {
				
				$action =  $em->getRepository('OrangeMainBundle:Action')->find(intval($id['action_id']));
   				array_push($actionsReload, $action);
			   }
			   
   			//Here is my problem , how can i my $questions into form? ---
   			$form = $this->createFormBuilder()
   				->add('isReload', 'collection', array('type' => new ReloadActionType(), 'allow_add' => false, 'allow_delete' => false, 'label' => false))
   				->add('save', 'submit', array('label' => 'Reload'))
   				->getForm();
   			$form->setData(array('isReload' => $actionsReload));
   			$form->handleRequest($request);
   			if ($form->isValid()) {
   				SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::SIGNALISATION_RETRAITER, $signalisation, "Cette signalisation a été reconduite suite à un mauvais traitement! Les actions mal traitées ont été rechargées !");
				   $this->updateEntityEtat($em, Statut::SIGNALISATION_RETRAITER, $signalisation);
   				foreach ($actionsReload as $action) {
   					if($action->getIsReload()) {
						$emailPorteur=$action->getPorteur()->getEmail();
							ActionUtils::changeStatutAction($em, $action, Statut::ACTION_NON_ECHUE, $this->getUser(), " Cette action a été rechargée suite à un mauvais traitement de la signalisation ");
							$action->setEtatCourant(Statut::ACTION_NON_ECHUE);
							$action->setEtatReel(Statut::ACTION_NON_ECHUE);
							$subject = 'Traitement de la signalisation '.$signalisation->getLibelle();
							$commentaire = 'Le ' . $now . ', l\'action intitulé : ' . $action->getLibelle () . ' a été rechargé suite à un mauvais traitement de la signalisation
										 à l\'origne de cette action. '.$action->getPorteur().' est invité à se connecter et confirmer la prise en charge de cette action,
										 ou faire une contre proposition au besoin. L\'animateur en charge du suivi de cette action peut modifier ultérieurement cette action rechargée';
							$em->persist($action);
							$em->flush();
							$membre=array_merge($membresEmail, array($emailPorteur),array("fatoukine.ndao@orange-sonatel.com","madiagne.sylla@orange-sonatel.com"));
   						Notification::notification ($helper, $subject, $membre, $commentaire,$actionStatut,$action );
					   }


				   }
				   
				 
				 

   				return $this->redirect($this->generateUrl('details_signalisation', array('id' =>$signalisation_id)));
   			}
   		} else {
   			$this->addFlash('warning', "Cette signalisation n'a pas d'actions correctives");
   			return $this->redirect($this->generateUrl('details_signalisation', array('id' =>$signalisation_id)));
   		}
   		return $this->render('OrangeMainBundle:Signalisation:reload.html.twig', array("id" =>$signalisation_id, 'entity' => $signalisation, "form" => $form->createView()));
   	}
   	
   	
   	public function getSignalisationMembresEmail($em, $entity) {
   		$membreEmail = array();
   		if(!$entity) {
   			return array();
   		}
		$source = $entity->getSource()->getUtilisateur();
		array_push($membreEmail, $source->getEmail());
		$animateur = $em->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('actif' => true, 'signalisation' => $entity->getid()));  
		if($animateur) {
		array_push($membreEmail, $animateur->getUtilisateur()->getEmail());
		$structureAnimateur = $animateur->getUtilisateur()->getStructure();
		// $managerAnimateur = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $structureAnimateur->getid(), 'manager' => true));
		// if($managerAnimateur) {
		// 	array_push($membreEmail, $managerAnimateur->getEmail());
		// }
		}
   		return $membreEmail;
   	}
   	
   	public function updateEntityEtat($entityManager, $currentStatus, $entity) {
   		$entity->setEtatCourant($currentStatus);
   		$entityManager->persist($entity);
   		$entityManager->flush();
   	}
   	
   	/**
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
     * @QMLogger(message="Importer des signalisations")
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
                	Notification::notificationSignWithCopy($helper, $subject, $destinataire, array($value->getSource()->getUtilisateur()->getEmail()), $commentaire, $value);
                }
                $this->get('session')->getFlashBag()->add('success', "Le chargement s'est effectué avec succés! Nombre de signalisation chargé: $nbr");
                return $this->redirect($this->generateUrl('les_signalisations'));
            } catch(ORMException $e) {
            	$this->get('session')->getFlashBag()->add('error', array ('title' => "Message d'erreur", 'body' => nl2br($e->getMessage())
            	));
            }
        }
        return $this->render('OrangeMainBundle:Signalisation:loading.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @Route("/typesignalisation_by_instance", name="typesignalisation_by_instance")
     * @QMLogger(message="Liste des types d'action d'une instance")
     * @Template()
     */
    public function listTypeSignalisationByInstanceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$instance = empty($request->request->get('id')) ? null : $em->getRepository('OrangeMainBundle:Instance')->find($request->request->get('id'));
    	if($instance) {
	    	$id = $instance->getParent() ? $instance->getParent()->getId() : $instance->getId();
	    	$arrData = $em->getRepository('OrangeMainBundle:TypeAction')->listTypeByInstance($id);
    	} else {
    		$arrData = array();
    	}
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
     * @QMLogger(message="Liste des domaines d'une instance parente et d'un périmétre")
     * @Template()
     */
    public function listDomaineSignalisationByInstanceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$instance = empty($request->request->get('id')) ? null : $em->getRepository('OrangeMainBundle:Instance')->find($request->request->get('id'));
    	$parent = $instance->getParent() ? $instance->getParent() : null;
    	if(!$instance) {
    		$arrData = array();
    	} elseif($parent && $parent->getConfiguration()) {
    		$arrData = $em->getRepository('OrangeMainBundle:Domaine')->listDomaineByInstance($parent, $instance->getLibelle());
    	} elseif($parent) {
    		$arrData = $em->getRepository('OrangeMainBundle:Domaine')->listByInstance($parent->getId());
    	} else {
    		$arrData = $em->getRepository('OrangeMainBundle:Domaine')->listByInstance($instance->getId());
    	}
        $output = array(0 => array('id' => null, 'libelle' => 'Choisir un domaine  ...'));
        foreach ($arrData as $data) {
            $output[] = array('id' => $data['id'], 'libelle' => $data['libelleDomaine']);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response->setContent(json_encode($output));
    }
    
    /**
     * @QMLogger(message="Reformulation d'une signalisation")
     * @Route("/{id}/reformulation_signalisation", name="reformulation_signalisation")
     * @Template()
     */
    public function reformulationAction(Request $request, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$dispatcher = $this->get('event_dispatcher');
    	$old = $entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
    	if(!$entity || ($entity && $entity->getEtatCourant() != Statut::SIGNALISATION_INVALIDER)) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
    	}
    	$form = $this->createCreateForm($entity, 'Signalisation', array(
    			'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
    	));
    	if ($request->getMethod() == 'POST') {
    		$form->handleRequest($request);
    		if ($form->isValid()) {
    			SignalisationUtils::createReformulationSignalisation($em, $this->getUser(), $old);
    			SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::NOUVELLE_SIGNALISATION, $entity, "Reformulation de la signalisation. En attente de prise en charge !");
    			$em->persist($entity);
    			$em->flush();
    			$event = $this->get('orange_main.signalisation_event')->createForSignalisation($entity);
    			$dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_REFORMULATION, $event);
    			return $this->redirect($this->generateUrl('details_signalisation', array('id' => $id)));
    		}
    	}
    	return array('entity' => $entity, 'form' => $form->createView());
    }
    
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('sign.reference', 'sign.libelle'), $request);
    }
    
//     protected function setOrder(QueryBuilder $queryBuilder, $aColumns, Request $request) {
//     	parent::setOrder($queryBuilder, array(null, 'a.reference', 'mi.libelle', 'a.libelle', 'mp.prenom', 'sr.libelle'), $request);
//     }
    
}
