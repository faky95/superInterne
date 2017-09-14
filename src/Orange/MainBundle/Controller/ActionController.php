<?php
namespace Orange\MainBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\ORMException;
use Orange\MainBundle\Criteria\ActionCriteria;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Reporting;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Form\LoadingType;
use Orange\MainBundle\Utils\ActionUtils;
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
use Orange\MainBundle\Entity\ActionStatut;
use Orange\MainBundle\OrangeMainEvents;
use Orange\MainBundle\Entity\Espace;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\MembreEspace;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Form\ActionType;
use Orange\MainBundle\OrangeMainForms;
use Orange\MainBundle\Entity\Projet;
use Orange\MainBundle\Entity\Chantier;
use Orange\MainBundle\Entity\Extraction;

/**
 * Action controller.
 */
class ActionController extends BaseController
{

	/**
	 * @Route("/les_mails", name="les_mails")
	 */
	public function mailAction() {
		$bu = $projet = $espace = array();
		$em = $this->getDoctrine()->getManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->userToAlertDepassement($bu, $projet, $espace);
		$this->getMapping()->getRelance()->setEntityManager($em)->mapDataforAlertDepassement($actions);
		return array();
	}

	/**
	 * Lists all Action entities.
	 * @QMLogger(message="Liste des actions")
	 * @Route("{code}/les_actions_validees", name="les_actions_validees")
	 * @Route("/les_actions", name="les_actions")
	 * @Route("{code_statut}/{espace_id}/les_actions_par_statut_espace", name="les_actions_by_statut_espace")
	 * @Route("{code_statut}/{projet_id}/les_actions_par_statut_projet", name="les_actions_by_statut_projet")
	 * @Route("{code_statut}/les_actions_by_statut", name="les_actions_by_statut")
	 * @Route("{instance_id}/les_actions_by_instance", name="les_actions_by_instance")
	 * @Route("{structure_id}/les_actions_by_structure", name="les_actions_by_structure")
	 * @Route("{espace_id}/les_actions_by_espace", name="les_actions_by_espace")
	 * @Route("{projet_id}/les_actions_by_projet", name="les_actions_by_projet")
	 * @Route("{chantier_id}/les_actions_by_chantier", name="les_actions_by_chantier")
	 * @Template()
	 */
	public function indexAction(Request $request, $code_statut=null, $instance_id=null, $structure_id=null, $espace_id=null, $projet_id=null, $chantier_id=null, $code=null) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria(), null, array('attr'=>array( 'espace_id'=> $espace_id)));
		$data = $request->get($form->getName());
		if($request->getMethod()=='POST') {
			if(isset($data['effacer'])) {
				$this->get('session')->set('action_criteria', new Request());
			} else {
				$this->get('session')->set('action_criteria', $request->request->get($form->getName()));
				$form->handleRequest($request);
			}
		} else {
			$this->get('session')->set('action_criteria', new Request());
		}
		$espace		= $espace_id ? $em->getRepository('OrangeMainBundle:Espace')->find($espace_id) : null;
		$projet		= $projet_id ? $em->getRepository('OrangeMainBundle:Projet')->find($projet_id) : null;
		$chantier	= $chantier_id ? $em->getRepository('OrangeMainBundle:Chantier')->find($chantier_id) : null;
		return array(
				'form' => $form->createView(), 'code' => $code, 'code_statut' => $code_statut, 'instance_id' => $instance_id, 
				'structure_id' => $structure_id, 'espace'=> $espace, 'projet'=> $projet, 'chantier'=> $chantier
			);
	}

	/**
	 * Lists my Action entities.
	 * @Route("/mes_actions", name="mes_actions")
	 * @Template()
	 */
	public function mineAction(Request $request) {
		$form = $this->createForm(new ActionCriteria());
		$data = $request->get($form->getName());
		if($request->getMethod()=='POST') {
			if(isset($data['effacer'])) {
				$this->get('session')->set('action_criteria', new Request());
			} else {
				$this->get('session')->set('action_criteria', $request->request->get($form->getName()));
				$form->handleRequest($request);
			}
		} else {
			$this->get('session')->set('action_criteria', new Request());
		}
		return array('form' => $form->createView());
	}
	
	/**
	 *  @Route("/actions_collaborateurs", name="actions_collaborateurs")
	 *  @Template()
	 */
	public function paCollaborateursAction(Request $request) {
		$form = $this->createForm(new ActionCriteria());
		$data = $request->get($form->getName());
		if($request->getMethod()=='POST') {
			if(isset($data['effacer'])) {
				$this->get('session')->set('action_criteria', new Request());
			} else {
				$this->get('session')->set('action_criteria', $request->request->get($form->getName()));
				$form->handleRequest($request);
			}
		} else {
			$this->get('session')->set('action_criteria', new Request());
		}
		return array('form' => $form->createView());
	}
	
	/**
	 * @Route("/liste_actions_collaborateurs", name="liste_actions_collaborateurs")
	 */
	public function listePaCollaborateursAction(Request $request){
		$logger = $this->get('my_service.logger');
		$logger->info('Liste actions collaborateurs :{  Utilisateur/login: '.$this->getUser()->getNomComplet().'/'.$this->getUser()->getUsername().' | Date: '.date('d-m-Y H:i:s').' | IP: '.$_SERVER["REMOTE_ADDR"].' | URL: '.$_SERVER['REQUEST_URI'].' }');
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->getActionCollaborateurs($criteria);
		/**
		 * @var QueryBuilder $queryExport
		 */
		$queryExport = $em->getRepository('OrangeMainBundle:Action')->getActionCollaborateursForExport($criteria);
		$this->get('session')->set('data', array('query' => $queryExport->getDql(), 'param' =>$queryExport->getParameters()));
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * Lists entities.
	 * @Route("/liste_des_actions", name="liste_des_actions")
	 * @Route("{code}/liste_des_actions_validees", name="liste_des_actions_validees")
	 * @Route("{code_statut}/liste_des_actions_by_statut", name="liste_des_actions_by_statut")
	 * @Route("{instance_id}/liste_des_actions_by_instance", name="liste_des_actions_by_instance")
	 * @Route("{structure_id}/liste_des_actions_by_structure", name="liste_des_actions_by_structure")
	 * @Method("GET")
	 * @Template()
	 */
	public function listAction(Request $request, $code_statut=null, $instance_id=null, $structure_id=null, $code=null) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
		$criteria = $form->getData();
		if($code_statut!=null) {
			$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->getActionByCodeStatut($code_statut, $criteria);
			$queryExport = $em->getRepository('OrangeMainBundle:Action')->getActionByCodeStatutForExport($code_statut, $criteria);
		} elseif($code!=null) {
			$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->getActionValide($code, $criteria);
			$queryExport = $em->getRepository('OrangeMainBundle:Action')->getActionValideForExport($code_statut, $criteria);
		} elseif($instance_id!=null) {
			$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->getActionByInstance($instance_id, $criteria);
			$queryExport = $em->getRepository('OrangeMainBundle:Action')->getActionByInstanceForExport($instance_id, $criteria);
		} elseif($structure_id!=null) {
			$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->getActionByStruct($structure_id, $criteria);
			$queryExport = $em->getRepository('OrangeMainBundle:Action')->getActionByStructForExport($structure_id, $criteria);
		} else {
			$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->listAllElements($criteria);
			$queryExport = $em->getRepository('OrangeMainBundle:Action')->listAllElementsForExport($criteria);
		}
		$this->get('session')->set('data',array('query' => $queryExport->getDql(),'param' =>$queryExport->getParameters()) );
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * Lists  entities.
	 *@Route("/liste_de_mes_actions", name="liste_de_mes_actions")
	 * @Method("GET")
	 * @Template()
	 */
	public function myListAction(Request $request,$code_statut=null,$instance_id=null,$structure_id=null) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->myActions($criteria, $this->getUser());
		$queryExport = $em->getRepository('OrangeMainBundle:Action')->listAllElementsForExport($criteria, $this->getUser());
		$this->get('session')->set('data', array('query' => $queryExport->getDql(), 'param' =>$queryExport->getParameters()));
		return $this->paginate($request, $queryBuilder ,'addRowInTableWithCheckBox');
	}
	
	/**
	 * Lists  entities.
	 * @Route("/{espace_id}/liste_by_espace", name="liste_by_espace")
	 * @Route("{code_statut}/{espace_id}/liste_des_actions_par_statut_et_espace", name="liste_des_actions_by_statut_espace")
	 * @Method("GET")
	 * @Template()
	 */
	public function myListEspaceAction(Request $request, $espace_id=null, $code_statut=null) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
		$criteria = $form->getData();
		$espace = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id);
		$membre = $em->getRepository('OrangeMainBundle:MembreEspace')->findOneBy(array('utilisateur' => $this->getUser(), 'espace' => $espace));
		$var = $membre->getIsGestionnaire();
		if($code_statut!=null) {
			$criteria->statut = $code_statut;
		}
		$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->listActionsByEspace($criteria, $espace_id, $var, $this->getUser()->getId());
		$queryExport = $em->getRepository('OrangeMainBundle:Action')->listActionsByEspaceForExport($criteria, $espace_id, $var, $this->getUser()->getId());
		$this->get('session')->set('data', array('query' => $queryExport->getDql(), 'param' =>$queryExport->getParameters()));
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * Lists  entities.
	 * @Route("/{projet_id}/liste_by_projet", name="liste_by_projet")
	 * @Route("/{chantier_id}/liste_by_chantier", name="liste_by_chantier")
	 * @Route("{code_statut}/{projet_id}/liste_des_actions_par_statut_et_projet", name="liste_des_actions_by_statut_projet")
	 * @Route("{code_statut}/{projet_id}/liste_des_actions_par_statut_et_chantier", name="liste_des_actions_by_statut_chantier")
	 * @Method("GET")
	 * @Template()
	 */
	public function myListProjetAction(Request $request, $projet_id=null, $chantier_id=null, $code_statut=null) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
		$criteria = $form->getData();
		if($code_statut!=null) {
			$criteria->statut = $code_statut;
		}
		$queryBuilder = $em->getRepository('OrangeMainBundle:Action')->listActionsByProjet($criteria, $projet_id, $chantier_id);
		$queryExport = $em->getRepository('OrangeMainBundle:Action')->listActionsByProjetForExport($criteria, $projet_id, $chantier_id);
		$this->get('session')->set('data', array('query' => $queryExport->getDql(), 'param' =>$queryExport->getParameters()));
		return $this->paginate($request, $queryBuilder);
	}
	
	/**
	 * @Route("/filtrer_actions", name="filtrer_actions")
	 * @Template()
	 */
	public function filtreAction(Request $request) {
		$form = $this->createForm(new ActionCriteria());
		if($request->getMethod()=='POST') {
			$this->get('session')->set('action_criteria', $request->request->get($form->getName()));
			return new JsonResponse();
		} else {
			$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
			return array('form' => $form->createView());
		}
	}
	
	/**
	 * @QMLogger(message="Extraction des actions")
	 * @Route("/export_action", name="export_action")
	 */
	public function exportAction() {
		$em = $this->getDoctrine()->getEntityManager();
		$response = new Response();
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$response->headers->set('Content-Disposition', sprintf('attachment; filename=Extraction des actions du %s.xlsx', date('YmdHis')));
		$response->sendHeaders();
		$queryBuilder = $this->get('session')->get('data', array());
		if($queryBuilder['totalNumber'] > 10000) {
			$extraction = Extraction::nouvelleTache($queryBuilder['totalNumber'], $this->getUser(), $queryBuilder['query'], serialize($queryBuilder['param']));
			$em->persist($extraction);
			$em->flush();
			$this->addFlash('warning', "L'extraction risque de prendre du temps, le fichier vous sera envoyé par mail");
			return $this->redirect($this->getRequest()->headers->get('referer'));
		}
 		$query = $em->createQuery($queryBuilder['query']);
 		$query->setParameters($queryBuilder['param']);
 		$statut = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
 		$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
 		$actions       = $query->getArrayResult();
 		$ids           = array_column($actions, 'id');
 		$contributeurs = $em->getRepository('OrangeMainBundle:Contributeur')->findContributeursForManyAction($ids);
 		$avancement    = $em->getRepository('OrangeMainBundle:ActionAvancement')->findForManyAction($ids);
 		$divers        = array('contributeur'=>$contributeurs,'avancement'=>$avancement);
 		$objWriter     = $this->get('orange.main.extraction')->exportAction($actions, $statut->getQuery()->execute(),$divers);
		$objWriter->save('php://output');
		return $response;
	}
		
     /**
     * Creates a new Action entity.
     * @QMLogger(message="Création d'une action")
     * @Route("/creer_action", name="creer_action")
     * @Route("/{espace_id}/creer_action_to_espace", name="creer_action_to_espace")
     * @Route("/{chantier_id}/creer_action_to_chantier", name="creer_action_to_chantier")
     * @Method("POST")
     * @Template("OrangeMainBundle:Action:new.html.twig")
     */
    public function createAction(Request $request, $espace_id=null, $chantier_id=null) {
    	$entity = new Action();
     	$dispatcher = $this->container->get('event_dispatcher');
     	$espace = $chantier = null;
    	if($espace_id!=null) {
    		$espace = $this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    		$entity->setInstance($espace ? $espace->getInstance() : null);
    		$entity->setInstance($espace->getInstance());
    		$entity->setEtatCourant(Statut::ACTION_NON_ECHUE);
    		$entity->setStatutChange(Statut::ACTION_NON_ECHUE);
    	} else {
    		$entity->setStatutChange(Statut::ACTION_NOUVELLE);
    	}
    	if($chantier_id!=null) {
    		$chantier = $this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($chantier_id);
    		$entity->setInstance($chantier ? $chantier->getInstance() : null);
    	}
    	$entity->setAnimateur($this->getUser());
    	$form = $this->createCreateForm($entity, 'Action', array('attr'=>array('espace_id' => $espace_id, 'chantier_id' => $chantier_id)));
    	$form->handleRequest($request);
    	if($entity->getPorteur() && !$espace_id) {
    		$entity->setStructure($entity->getPorteur()->getStructure());
    	}
    	if($request->getMethod()=='POST') {
    		if($form->isValid()) {
    			$em = $this->getDoctrine()->getManager();
                $entity->setAnimateur($this->getUser());
                if($entity->getErq()->getFile()) {
                	$entity->getErq()->setUtilisateur($this->getUser());
                	$entity->addDocument($entity->getErq(), $this->getMyParameter('types', array('creation')));
                }
                $em->persist($entity);
                $em->flush();
    			if($espace_id==null) {
    				$entity->setEtatCourant(Statut::ACTION_NOUVELLE);
    				$entity->setEtatReel(Statut::ACTION_NOUVELLE);
    				ActionUtils::setReferenceAction($em, $entity);
    				ActionUtils::changeStatutAction($em, $entity, Statut::ACTION_NOUVELLE, $this->getUser(), "Nouvelle action créée par ".$entity->getAnimateur()->getNomComplet());
    				$event = $this->get('orange_main.action_event')->createForAction($entity);
    				$dispatcher->dispatch(OrangeMainEvents::ACTION_CREATE_NOUVELLE, $event);
    			} else {
    				$espace = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id)->getLibelle();
    				ActionUtils::setReferenceAction($em, $entity);
    				ActionUtils::changeStatutAction($em, $entity, Statut::ACTION_NON_ECHUE, $this->getUser(), "Nouvelle action créée dans l'espace ".$espace);
    				$event = $this->get('orange_main.action_event')->createForAction($entity);
    				$dispatcher->dispatch(OrangeMainEvents::ACTION_ESPACE_CREATE_NOUVELLE, $event);
    			}
    			if($form->get('save_and_add')->isClicked()) {
    				if($espace_id!=null) {
    					return $this->redirect($this->generateUrl('nouvelle_action_to_espace', array('espace_id' => $espace_id)));
    				} else {
    					return $this->redirect($this->generateUrl('nouvelle_action'));
    				}
    			}
//     			$event = $this->get('orange_main.action_event')->createForAction($entity);
//     			$dispatcher->dispatch(OrangeMainEvents::ACTION_CREATE_NOUVELLE, $event);
    			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action créée avec succès'));
    			if($espace_id) {
    				return $this->redirect($this->generateUrl('details_action_espace', array('id' => $entity->getId(), 'id_espace' => $espace_id)));
    			} elseif($chantier_id) {
    				return $this->redirect($this->generateUrl('details_action_chantier', array('id' => $entity->getId(), 'chantier_id' => $chantier_id)));
    			} else {
    				return $this->redirect($this->generateUrl('details_action' ,array('id' => $entity->getId())));
    			}
    		}
    		if(!$form->isValid()) {
    			$form_errors = $this->get('form_errors')->getArray($form);
    			foreach ($form_errors as $error){
    				$this->container->get ('session')->getFlashBag()->add('error', array ('title' => 'Erreur', 'body' => $error[0]." "));
    			}
    		}
    	}
    	return array('entity' => $entity, 'form' => $form->createView(), 'espace'=> $espace);
    }
    
    /**
     * Displays a form to create a new Action entity.
     * @QMLogger(message="Nouvelle action")
     * @Route("/nouvelle_action", name="nouvelle_action")
	 * @Route("/{instance_id}/nouvelle_action_to_instance", name="nouvelle_action_to_instance")
     * @Route("/{espace_id}/nouvelle_action_to_espace", name="nouvelle_action_to_espace")
     * @Route("/{chantier_id}/nouvelle_action_to_chantier", name="nouvelle_action_to_chantier")
     * @Method("GET")
     * @Template() 
     */
    public function newAction($espace_id=null, $chantier_id=null, $instance_id=null) {
    	$bu = $this->getUser()->getStructure()->getBuPrincipal()->getId();
        $entity = new Action();
        if($espace_id) {
       		$espace = $this->getDoctrine()->getRepository('OrangeMainBundle:Espace')->find($espace_id);
	        if(!$espace) {
	            $this->addFlash('error', "Espace non reconnu");
	            return $this->redirect($this->generateUrl('dashboard'));
	        }
      		$entity->setInstance($espace->getInstance());
        } elseif($chantier_id) {
       		$chantier = $this->getDoctrine()->getRepository('OrangeMainBundle:Chantier')->find($chantier_id);
	        if(!$chantier) {
	            $this->addFlash('error', "Chantier non reconnu");
	            return $this->redirect($this->generateUrl('dashboard'));
	        }
      		$entity->setInstance($chantier->getInstance());
        }
        $form = $this->createCreateForm($entity,'Action', array('attr'=>array(
        		'espace_id' => $espace_id, 'chantier_id' => $chantier_id, 'instance_id' => $instance_id, 'bu_id'=> $bu
        	)));
        return array('entity' => $entity, 'form' => $form->createView(), 'espace' => isset($espace) ? $espace : null);
    }
    
    /**
     * Displays a form to create a new Action entity.
     * @QMLogger(message="Changer statut d'une action")
     * @Route("/{entity_id}/changer_statut", name="changer_statut")
     * @Method({"GET", "POST"})
     * @Template("OrangeMainBundle:Action:changerStatut.html.twig")
     */
    public function changerStatutAction(Request $request, $entity_id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Action')->find($entity_id);
    	$form   = $this->createCreateForm($entity, 'ActionChange');
    	if($request->isMethod('POST')) {
    		$form->handleRequest($request);
    		if($form->isValid()) {
    			$this->get('orange.main.change_statut')->ChangeStatutAction($entity, $this->getUser());
    			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Statut changé avec succés.'));
    			return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $entity->getId()))));
    		} else {
    		     return $this->render('OrangeMainBundle:Action:changerStatut.html.twig', array('action' => $entity, 'form' => $form->createView()), new Response(null, 303));
    		}
    	}
    	return array('action' => $entity, 'form' =>$form->createView());
    }
    
    /**
     * Displays a form to reassign an action entity.
     * @QMLogger(message="Réassignatin d'une action")
     * @Route("/{action_id}/reassignation_action", name="reassignation_action")
     * @Template() 
     */
    public function reassignationAction(Request $request, $action_id=null) {
    	$dispatcher = $this->container->get('event_dispatcher');
   		$em = $this->getDoctrine()->getManager();
   		$entity = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
    	$entity->setStatutChange($entity->getActionStatut()->count() ? $entity->getActionStatut()->first()->getStatut() : null);
        $form   = $this->createForm(new ActionType(OrangeMainForms::ACTION_REAFFECTATION), $entity);
    	if($entity->getPorteur()) {
    		$entity->setStructure($entity->getPorteur()->getStructure());
    	}
        if($request->isMethod('POST')) {
        	$form->handleRequest($request);
        	if($form->isValid()) {
        		$actionStatut = new ActionStatut();
        		$actionStatut->setUtilisateur($this->getUser());
        		$actionStatut->setCommentaire(sprintf("Ré-assignation de l'action à %s", $entity->getPorteur()->getNomComplet()));
        		$actionStatut->setStatut($em->getRepository('OrangeMainBundle:Statut')->findOneByCode($entity->getEtatCourant()));
        		$entity->addActionStatut($actionStatut);
        		$em->persist($entity);
        		$em->flush();
        		$event = $this->get('orange_main.action_event')->createForAction($entity);
        		$dispatcher->dispatch(OrangeMainEvents::ACTION_REASSIGNATION, $event);
        		$this->get('session')->getFlashBag()->add('success', "L'action a été ré-assigné à ".$entity->getPorteur()->getNomComplet()." avec succès");
        		return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $entity->getId()))));
        	}
        	return $this->render('OrangeMainBundle:Action:reassignation.html.twig', array('entity' => $entity, 'form' => $form->createView()), new Response(null, 303));
        }
        return array('entity' => $entity, 'form' => $form->createView());
    }
    
    /**
     * Displays a form to create a new Action entity for a signalisation.
     * @QMLogger(message="Nouvelle signalisation action")
     * @Route("/signalisation/{signalisation_id}/nouvelle_action", name="nouvelle_signalisation_action")
     * @Method("GET")
     */
    public function newSignalisationAction(Request $request, $signalisation_id) {
    	$em = $this->getDoctrine()->getManager();
    	$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
    	if(!$signalisation) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
    	}
    	$entity = new Action();
   		$entity->setLibelle($signalisation->getLibelle());
   		$entity->setDescription($signalisation->getDescription());
   		$entity->setDateDebut(new \DateTime('NOW'));
   		$entity->setInstance($signalisation->getInstance()->getParent() ? $signalisation->getInstance()->getParent() : $signalisation->getInstance());
   		$entity->setDomaine($signalisation->getDomaine());
   		$entity->setTypeAction($signalisation->getTypeSignalisation());
    	$form   = $this->createForm(new ActionType(OrangeMainForms::ACTION_BU), $entity);
    	return $this->render('OrangeMainBundle:Action:signalisationAction.html.twig', array(
    			'entity' => $entity, 'form' => $form->createView(), 'signalisation_id' => $signalisation_id, 'instance' => $signalisation->getInstance()
    		));
    }

    /**
     * Creates a new Signalisation Action entity.
     * @QMLogger(message="Création d'une action")
     * @Route("/creer_signalisation_action/{signalisation_id}/signalisation", name="creer_signalisation_action")
     * @Method("POST")
     */
    public function createSignalisationAction(Request $request, $signalisation_id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = new Action();
     	$dispatcher = $this->container->get('event_dispatcher');
    	$signalisation = $em->getRepository('OrangeMainBundle:Signalisation')->find($signalisation_id);
    	if(!$signalisation) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette signalisation n'est pas reconnue");
    		return $this->redirect($this->generateUrl('les_signalisations'));
    	}
    	$entity->setAnimateur($this->getUser());
    	$entity->setStatutChange(Statut::ACTION_NOUVELLE);
    	$form = $this->createForm(new ActionType(OrangeMainForms::ACTION_BU), $entity);
    	$form->handleRequest($request);
    	if($entity->getPorteur()) {
    		$entity->setStructure($entity->getPorteur()->getStructure());
    	}
    	if($form->isValid()) {
	    	$entity->setEtatCourant(Statut::ACTION_NOUVELLE);
	    	$entity->setEtatReel(Statut::ACTION_NOUVELLE);
    		$em->persist($entity);
    		$entity->addSignalisation($signalisation);
    		$em->flush();
    		ActionUtils::setReferenceActionSignalisation($em, $entity, $signalisation);
    		SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::TRAITEMENT_SIGNALISATION, $signalisation, 'Une action corrective a été ajoutée pour traiter cette signalisation');
    		ActionUtils::changeStatutAction($em, $entity, Statut::ACTION_NOUVELLE, $this->getUser(), 'Nouvelle action corrective.');
			$event = $this->get('orange_main.action_event')->createForAction($entity);
			$dispatcher->dispatch(OrangeMainEvents::ACTION_CREATE_NOUVELLE, $event);
    		$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action créée avec succés.'));
    		if($form->get('save_and_add')->isClicked()) {
    			return $this->redirect($this->generateUrl('nouvelle_signalisation_action', array('signalisation_id' => $signalisation_id)));
    		}
    		return $this->redirect($this->generateUrl('details_action', array('id' => $entity->getId())));
   		}
    	return $this->render('OrangeMainBundle:Action:signalisationAction.html.twig', array(
    			'entity' => $entity, 'form' => $form->createView(), 'signalisation_id' => $signalisation_id,
    		));
    }

    /**
     * Finds and displays a Action entity.
     * @QMLogger(message="Visualisation d'une action")
     * @Route("/details_action/{id}", name="details_action")
     * @Route("/details_action_espace/{id}/{id_espace}", name="details_action_espace")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, $id_espace=null) {
        $em = $this->getDoctrine()->getManager();
        $action = $em->getRepository('OrangeMainBundle:Action')->find($id);
        $this->denyAccessUnlessGranted('read', $action, 'Unauthorized access!');
    	if(!$action) {
    		$this->addFlash('error', "Impossible de voir les détails, cette action n'est pas reconnue");
    		return $this->redirect($this->generateUrl('mes_actions'));
    	}
    	if($action->getActionCyclique()) {
    		return $this->redirect($this->generateUrl('actioncyclique_show', array('id' => $action->getActionCyclique()->getId())));
    	}
        return array('action' => $action, 'espace'=> $id_espace ? $em->getRepository('OrangeMainBundle:Espace')->find($id_espace) : null);
    }

    /**
     * Displays a form to edit an existing Action entity.
     * @QMLogger(message="Modification d'une action")
     * @Route("/edition_action/{id}", name="edition_action")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Action')->find($id);
        $this->denyAccessUnlessGranted('update', $entity, 'Unauthorized access!');
        if (!$entity) {
    		$this->addFlash('error', "Impossible de voir les détails, cette action n'est pas reconnue");
    		return $this->redirect($this->generateUrl('mes_actions'));
        }
        $editForm = $this->createEditForm($entity);
        if($entity->getEspace()) {
        	$editForm = $this->createEditForm($entity, $entity->getEspaceId());
        }
        return array('entity' => $entity, 'edit_form' => $editForm->createView(), 'espace_id' => $entity->getEspaceId());
    }

    /**
    * Creates a form to edit a Action entity.
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Action $entity, $espace_id=null) {
    	$form   = $this->createCreateForm($entity, 'Action', array('attr'=>array('espace_id'=>$espace_id),
    			'action' => $this->generateUrl('modifier_action', array('id' => $entity->getId())), 'method' => 'PUT'));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    
    /**
     * Edits an existing Action entity.
     * @Route("/modifier_action/{id}", name="modifier_action")
     * @Method("POST")
     * @Template("OrangeMainBundle:Action:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Action')->find($id);
        $porteur = $entity->getPorteur();
        $form = $this->createCreateForm($entity, 'Action');
        $request = $this->get('request');
        $statut = new ActionStatut();
        $today = new \DateTime();
        if($request->getMethod() == 'POST') {
        	$entity->setStatutChange($entity->getActionStatut()->count() ? $entity->getActionStatut()->first()->getStatut() : null);
        	$form->handleRequest($request);
        	if($form->isValid()) {
        		if($entity->getDateInitial() > $today){
        			$s = $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_NON_ECHUE);
					$statut->setAction($entity);
					$statut->setUtilisateur($this->getUser());
					$statut->setCommentaire("Délai initial modifié par: ".$this->getUser()->getNomComplet());
					$statut->setStatut($s);
					$em->persist($statut);
        		}
        		if($entity->getPorteur()->getId() != $porteur->getId()){
        			$this->get('orange.main.mailer')->NotifUpdatePorteur($entity->getPorteur()->getEmail(), $entity);
        		}
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('details_action', array('id' => $id)));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView(),'espace_id' => $entity->getEspaceId());
    }
    
    /**
     * Deletes a Action entity.
     * @QMLogger(message="Suppression d'une action")
     * @Route("/supprimer_action/{id}", name="supprimer_action")
     * @Template("OrangeMainBundle:Action:delete.html.twig")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Action')->find($id);
        if(!$entity) {
    		$this->addFlash('error', "Impossible de faire cette opération, cette action n'est pas reconnue");
    		return $this->redirect($this->generateUrl('mes_actions'));
        }
    	if($request->getMethod() === 'POST') {
    		if($entity) {
    			$em->remove($entity);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Action supprimée avec succès!'));
    			return new JsonResponse(array('url' => $this->generateUrl('les_actions')));
    		} else {
    			$this->get('session')->getFlashBag()->add('failed', array('title' => 'Notification', 'body' =>  'Action inexistante!'));
    		}
    	}
    	return array('id' => $id);
    }
    
    /**
     * Deletes a Document entity.
     * @QMLogger(message="Suppression Erq")
     * @Route("/supprimer_erq/{id}", name="supprimer_erq")
     * @Security("has_role('ROLE_ANIMATEUR') or has_role('ROLE_ADMIN') or has_role('ROLE_PORTEUR')")
     */
    public function deleteErqAction($id) {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Document')->find($id);
    	if($entity==null) {
    		$this->createAccessDeniedException("L'ERQ de l'action n'existe pas");
    	} else {
    		if($this->get('request')->getMethod()=='POST') {
		    	$em->remove($entity);
		    	$em->flush();
		    	$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  "Suppression de l'ERQ effectuée avec succès!"));
		    	return new JsonResponse(array('url' => $this->generateUrl('details_action', array('id' => $entity->getAction()->getId()))));
    		}
    	}
    	return $this->render('OrangeMainBundle:Action:deleteErq.html.twig', array('entity' => $entity));
    }
    
    /**
     * Creates a form to delete a Action entity by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_action', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le r�sultat de la requ�te
     * @param \Orange\MainBundle\Entity\Action $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
                '<span align="center" style="margin-left: 15px; width:20px; height:20px; background:'.($entity->getPriorite()?$entity->getPriorite()->getCouleur():'') .'">&nbsp;&nbsp;&nbsp;&nbsp;</span>',
    			$entity->getReference(),
    			$entity->getInstance()->__toString(),
    			$entity->getLibelle(),
     			$entity->getPorteur()->getPrenom().' '.$entity->getPorteur()->getNom(),
     			$this->showEntityStatus($entity, 'etat'),
      			$this->get('orange_main.actions')->generateActionsForAction($entity)
    		);
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le r�sultat de la requ�te
     * @param \Orange\MainBundle\Entity\Action $entity
     * @return array
     */
    protected function addRowInTableWithCheckBox($entity) {
    	return array(
    			$this->get('orange_main.actions')->showCheckBoxForOrientationAction($entity),
    			'<span align="center" style="margin-left: 15px; width:20px; height:20px; background:'.($entity->getPriorite()?$entity->getPriorite()->getCouleur():'') .'">&nbsp;&nbsp;&nbsp;&nbsp;</span>',
    			$entity->getReference(),
    			$entity->getInstance()->__toString(),
    			$entity->getLibelle(),
    			$entity->getPorteur()->getPrenom().' '.$entity->getPorteur()->getNom(),
    			$this->showEntityStatus($entity, 'etat'),
    			$this->get('orange_main.actions')->generateActionsForAction($entity)
    	);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('a.reference', 'a.libelle', 'mi.libelle', 'mp.prenom', 'mp.nom', 'sr.libelle'), $request);
    }
    
    protected function setOrder(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setOrder($queryBuilder, array(null, 'a.reference', 'mi.libelle', 'a.libelle', 'mp.prenom', 'sr.libelle'), $request);
    }
     
    /**
     * @Route("/chargement_action/{isCorrective}", name="chargement_action")
     * @Template()
     */
    public function loadingAction($isCorrective=0) {
        $form = $this->createForm(new LoadingType());
        $form->add('isCorrective','hidden');
        return array('form' => $form->createView(),'isCorrective'=>$isCorrective);
    }
    
    /**
     * @QMLogger(message="Importation d'action")
     * @Route("/importer_action", name="importer_action")
     * @Route("/importer_action_espace/{espace_id}", name="importer_action_espace")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function importAction(Request $request, $espace_id=null, $action_generique_id=null) {
    	$em   = $this->getDoctrine()->getManager();
    	$this->denyAccessUnlessGranted('import', new Action(), 'Unauthorized access!');
    	$array =  array();
        $form = $this->createForm(new LoadingType());
        $form->add('isCorrective','hidden');
        $users=$em->getRepository('OrangeMainBundle:Utilisateur')->getArrayUtilisateur();
        $instances=$em->getRepository('OrangeMainBundle:Instance')->getArrayInstance();
        $form->handleRequest($request);
        if($form->isValid()) {
            $data = $form->getData();
            $isCorrective = intval($data['isCorrective']);
            try {
            	if($espace_id) {
            		$idsMembres = $em->getRepository('OrangeMainBundle:MembreEspace')->getAllMembres($espace_id);
            		foreach ($idsMembres as $value){
            			array_push($array, $value['id']);
            		}
            	}
                $number = $this->get('orange.main.loader')->loadAction($data['file'], $this->getUser(), $users, $instances, $data['isCorrective']);
                $actions = $em->getRepository('OrangeMainBundle:Action')->getActions($number['id']);
                $nbr = $number['nbr'];
                foreach ($actions as $value) {
                	$body = $this->renderView('OrangeMainBundle:Notification:nouvelleAction.html.twig', array('data' => $value));
                	$this->get('orange.main.mailer')->send($value->getPorteur()->getEmail(), $value->getEmailContributeurs(), 'Nouvelle Action', $body);
                }
                $em->flush();
                $this->get('orange.main.setStructure')->setStructureForAction();
                $this->get('session')->getFlashBag()->add('success', array (
                		'title' => 'Notification',
                		'body' => "Le chargement s'est effectué avec succés! Nombre d'actions chargées:  $nbr"
                	));
                return $this->redirect($this->generateUrl('les_actions'));
            } catch(ORMException $e) {
            	$this->get('session')->getFlashBag()->add('error', array ('title' => 'Message d\'erreur', 'body' => nl2br($e->getMessage())));
            }
        }
        if($espace_id) {
        	return $this->render('OrangeMainBundle:Action:loadingForEspace.html.twig', array('form' => $form->createView(), 'espace_id' => $espace_id));
        } else {
        	return $this->render('OrangeMainBundle:Action:loading.html.twig', array('form' => $form->createView(), 'isCorrective'=>$isCorrective));
        }
    }
    
    /*
     * reperetoire de sauvegarde des reporting
    */
    private function getUploadDir() {
    	return $this->getRequest()->getBaseUrl().($this->get('kernel')
    			->getEnvironment()=='prod' ? '' : '/..')."/upload/reporting/";
    }
    
    
    /**
     * @Route("/select_utilisateurs", name="select_utilisateurs")
     */
    public function porteursAction(Request $request)
    {
    	$instance_id = $request->request->get('instance_id');
   		$em = $this->getDoctrine()->getManager();
		$qb = $em->getRepository('OrangeMainBundle:Utilisateur')
						->createQueryBuilder('u')
						->join('u.structure', 's')
						->add('from', 'OrangeMainBundle:Structure s1',true)
						->join('s1.instance', 'i1')
						->select('u')
						->where('i1.id=:instance_id')->setParameter('instance_id', $instance_id)
					    ->andWhere('s.id =s1.id ')
						->andWhere('s.lvl >= s1.lvl')
						->andWhere('s.root = s1.root')
						->andWhere('s.lft  >= s1.lft')
						->andWhere('s.rgt <= s1.rgt')
		                ->getQuery()->getArrayResult();
    	return new JsonResponse($qb);
    }
    
    /**
     * @Route("/porteur_by_espace", name="porteur_by_espace")
     * @Template()
     */
    public function listPorteurByEspaceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$instance = $em->getRepository('OrangeMainBundle:Instance')->find($request->request->get('id'));
    	$output = array(0 => array('id' => null, 'libelle' => 'Choisir un porteur ...'));
    	if ($instance->getEspace()){
    		$arrData = $em->getRepository('OrangeMainBundle:MembreEspace')->membreOfEspace($instance->getEspace()->getId());
    		foreach ($arrData as $data) {
    			$output[] = array('id' => $data->getUtilisateur()->getId(), 'libelle' => $data->getUtilisateur()->__toString());
    		}    		
    	}
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/json');
    	return $response->setContent(json_encode($output));
    }
    
    /**
     * @Route("/porteur_by_instance", name="porteur_by_instance")
     * @Template()
     */
    public function listPorteurByInstanceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$structures = array();
    	$structs = $em->getRepository('OrangeMainBundle:Structure')->listAllStructures($request->request->get('id'));
    	foreach ($structs as $struct){
    		array_push($structures, $struct->getId());
    	}
    	$arrData = $em->getRepository('OrangeMainBundle:Utilisateur')->listByInstance($structures)->getQuery()->execute();
    	$output = array(0 => array('id' => null, 'libelle' => 'Choisir un porteur ...'));
    	foreach ($arrData as $data) {
    		$output[] = array('id' => $data->getId(), 'libelle' => $data->__toString());
    	}
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/json');
    	return $response->setContent(json_encode($output));
    }

    /**
     * @Route("/user_by_instance", name="user_by_instance")
     * @Template()
     */
    public function listUserByInstanceAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$structures = array();
    	$structs = $em->getRepository('OrangeMainBundle:Structure')->listAllStructures($request->request->get('id'));
    	foreach ($structs as $struct){
    		array_push($structures, $struct->getId());
    	}
    	exit($em->getRepository('OrangeMainBundle:Utilisateur')->listByInstance($structures)->getQuery()->getSQL());
    	$arrData = $em->getRepository('OrangeMainBundle:Utilisateur')->listByInstance($structures)->getQuery()->execute();
    	$output = array(0 => array('id' => null, 'libelle' => 'Choisir un porteur ...'));
    	foreach ($arrData as $data) {
    		$output[] = array('id' => $data->getId(), 'text' => $data->__toString());
    	}
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/json');
    	return $response->setContent(json_encode($output));
    }

     /**
     * @Route("/type_by_instance", name="type_by_instance")
     * @Template()
     */
    public function listTypeByInstanceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $arrData = $em->getRepository('OrangeMainBundle:TypeAction')->listByInstance($request->request->get('id'));
        $output = array(0 => array('id' => null, 'libelle' => 'Choisir un type  ...'));
        foreach ($arrData as $data) {
            $output[] = array('id' => $data['id'], 'libelle' => $data['type']);
      	}
     	$response = new Response();
      	$response->headers->set('Content-Type', 'application/json');
      	return $response->setContent(json_encode($output));
    }
    
    /**
     * @Route("/domaine_by_instance", name="domaine_by_instance")
     * @Template()
     */
    public function listDomaineByInstanceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $arrData = $em->getRepository('OrangeMainBundle:Domaine')->listByInstance($request->request->get('id'));
        $output = array(0 => array('id' => null, 'libelle' => 'Choisir un type  ...'));
        foreach ($arrData as $data) {
            $output[] = array('id' => $data['id'], 'libelle' => $data['libelleDomaine']);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response->setContent(json_encode($output));
    }

    /**
     * Displays a form to create a new Action entity.
     * @Route("/nouveau_reporting", name="nouveau_reporting")
     * @Method("GET")
     * @Template()
     */
    public function newReportingAction() {
    	$parameters = array();
    	$req = $this->get('session')->get('reporting', array());
    	foreach($req['param'] as $value) {
    		$parameters[$value->getName()] = $value->getValue();
    	}
    	$entity = new Reporting();
    	$entity->setRequete( $req['reporting']);
    	$entity->setParameter(serialize($parameters));
    	$form   = $this->createCreateForm($entity,'Reporting');
    	return array('entity' => $entity, 'form'   => $form->createView());
    }
    
    /**
     * Creates a new Action entity.
     * @Route("/creer_reporting", name="creer_reporting")
     * @Method({"POST","GET"})
     * @Template("OrangeMainBundle:Action:newReporting.html.twig")
     */
    public function createReportingAction(Request $request) {
    	$entity = new Reporting();
    	$form = $this->createCreateForm($entity,'Reporting');
    	$form->handleRequest($request);
    	if ($form->isValid()) {
    		$em = $this->getDoctrine()->getManager();
    		$entity->setUtilisateur($this->getUser());
    		$this->container->get('orange.main.envoi')->generateEnvoi($entity);
    		$em->persist($entity);
    		$em->flush();
    		//	ActionUtils::changeStatutAction($em, $entity, Statut::ACTION_NOUVELLE, $this->getUser(), "Nouvelle action créée.");
    		return new JsonResponse(array('url' => $this->generateUrl('les_actions')));
    	}
    	return $this->render('OrangeMainBundle:Action:newReporting.html.twig', array(
    					'entity' => $entity, 'form'   => $form->createView(),
    			), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
    
}
