<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\QuickMakingBundle\Model\EntityManager;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Criteria\TypeActionCriteria;
use Orange\MainBundle\Form\TypeActionType;

/**
 * TypeAction controller.
 */
class TypeActionController extends BaseController
{

    /**
     * @QMLogger(message="Liste des types d'action")
     * @Route("/les_types_action", name="les_types_action")
     * @Route("/{espace_id}/les_types_action_by_espace", name="les_types_action_by_espace")
     * @Route("/{projet_id}/les_types_action_by_projet", name="les_types_action_by_projet")
     * @Route("/{chantier_id}/les_types_action_by_chantier", name="les_types_action_by_chantier")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($espace_id=null, $projet_id=null, $chantier_id=null)
    {
    	$em = $this->getDoctrine()->getManager();
    	$data = $this->findEntities($em, $espace_id, $projet_id);
        $this->get('session')->set('type_action_criteria', new Request());
    	return array('espace' => $data['espace'], 'projet' => $data['projet'], 'chantier' => $data['chantier']);
    }

    /**
     * Lists  entities.
     * @Route("/liste_des_types_action", name="liste_des_types_action")
     * @Route("/{espace_id}/liste_des_types_action_by_espace", name="liste_des_types_action_by_espace")
     * @Route("/{projet_id}/liste_des_types_action_by_projet", name="liste_des_types_action_by_projet")
     * @Route("/{projet_id}/liste_des_types_action_by_chantier", name="liste_des_types_action_by_chantier")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request, $espace_id=null, $projet_id=null, $chantier_id=null) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new TypeActionCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('type_action_criteria'), $form);
    	if($espace_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:TypeAction')->getTypesByEspace($espace_id);
    	} elseif($projet_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:TypeAction')->getTypeActionByProjet($projet_id);
    	} elseif($chantier_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:TypeAction')->getTypeActionByChantier($chantier_id);
    	} else {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:TypeAction')->listQueryBuilder();
    	}
    	$this->get('session')->set('data', array('query' => $queryBuilder->getDql(), 'param' =>$queryBuilder->getParameters()) );
    	return $this->paginate($request, $queryBuilder);
    }
    
    /**
     * Creates a new TypeAction entity.
     * @QMLogger(message="Création de type d'action")
     * @Route("/creer_type_action", name="creer_type_action")
     * @Route("/{espace_id}/creer_type_action_to_espace", name="creer_type_action_to_espace")
     * @Route("/{projet_id}/creer_type_action_to_projet", name="creer_type_action_to_projet")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request, $espace_id=null, $projet_id=null) {
        $entity = new TypeAction();
        $em = $this->getDoctrine()->getManager();
        $data = $this->findEntities($em, $espace_id, $projet_id);
        $form = $this->createCreateForm($entity, 'TypeAction', array(
   				'attr' => array('security_context' => $this->get('security.context'))
        	));
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
        	$form->remove('bu');
        }
        $form->handleRequest($request);
        if($request->getMethod() === 'POST') {
		    if($form->isValid()) {
		        $em->persist($entity);
		        if($espace_id!=null) {
		            $data['espace']->getInstance()->addTypeAction($entity);
		            $em->persist($data['espace']);
		        } elseif($projet_id!=null) {
		            $data['projet']->getInstance()->addTypeAction($entity);
		            $em->persist($data['projet']);
		        } elseif(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
	            	$bu = $this->getUser()->getStructure()->getBuPrincipal();
	            	$bu->addTypeAction($entity);
           		}
           		$em->flush();
           	}
            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  "Le type d'action a été créé avec succès"));
            if($espace_id!=null) {
            	return $this->redirect($this->generateUrl('les_types_action_by_espace', array('espace_id'=>$espace_id)));
            } elseif($projet_id!=null) {
            	return $this->redirect($this->generateUrl('les_types_action_by_projet', array('projet_id'=>$projet_id)));
            } else {
            	return $this->redirect($this->generateUrl('les_types_action'));
            }
       	}
        return $this->render('OrangeMainBundle:TypeAction:new.html.twig', array(
        				'entity' => $entity, 'form'   => $form->createView(),
        		), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
    /**
     * Displays a form to create a new TypeAction entity.
     * @QMLogger(message="Nouveau type d'action")
     * @Route("/nouveau_type_action", name="nouveau_type_action")
     * @Method("GET")
     * @Template()
     */
    public function newAction($espace_id=null, $projet_id=null)
    {
        $entity = new TypeAction();
        $em = $this->getDoctrine()->getManager();
        $data = $this->findEntities($em, $espace_id, $projet_id);
        $form   = $this->createCreateForm($entity, 'TypeAction', array(
        		'attr' => array('security_context' => $this->get('security.context'))
        	));
        return array('entity' => $entity, 'form'   => $form->createView(), 'espace'=> $data['espace'], 'projet'=> $data['projet']);
    }
    
    /**
     * Finds and displays a TypeAction entity.
     * @QMLogger(message="Visualisation de type d'action")
     * @Route("/details_type_action/{id}/", name="details_type_action")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        if(!$entity) {
            throw $this->createNotFoundException('Unable to find TypeAction entity.');
        }
        return array('entity' => $entity);
    }

    /**
     * Displays a form to edit an existing TypeAction entity.
     * @QMLogger(message="Modification d'un type d'action")
     * @Route("/edition_type_action/{id}/", name="edition_type_action")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TypeAction entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a TypeAction entity.
    * @param TypeAction $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TypeAction $entity)
    {
        $form = $this->createForm(new TypeActionType(), $entity, array(
	            'action' => $this->generateUrl('modifier_type_action', array('id' => $entity->getId())),
	            'method' => 'PUT',
	        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }

    /**
     * Edits an existing TypeAction entity.
     * @Route("/modifier_type_action/{id}/", name="modifier_type_action")
     * @Method("POST")
     * @Template("OrangeMainBundle:TypeAction:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        $form = $this->createCreateForm($entity,'TypeAction');
        $request = $this->get('request');
        if($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  "Le type d'action a été modifié avec succès"));
        		return new JsonResponse(array('url' => $this->generateUrl('les_types_action')));
        	}
        }
        return $this->render('OrangeMainBundle:TypeAction:edit.html.twig', array(
        				'entity' => $entity, 'edit_form' => $form->createView()
        		), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
  	/**
     * Deletes a TypeAction entity.
     * @Route("/{id}/supprimer_type_action", name="supprimer_type_action")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:TypeAction')->find($id);
        if($entity) {
            if($entity->getAction()->count()>0) {
           		$this->container->get('session')->getFlashBag()->add('failed', array(
    					'title' =>'Notification', 'body' => 'Cette structure est rattachée à des actions'
    				));
           	} else {
           		$em->remove($entity);
       			$em->flush();
       			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  "Le type d'action a été supprimé avec succès"));
           	}
        } else {
        	throw $this->createNotFoundException('TypeAction inexistant.');
        }
        return $this->redirect($this->generateUrl('les_types_action'));
    }
    
    /**
     * @param EntityManager $em
     * @param number $espace_id
     * @param number $chantier_id
     * @return void
     */
    private function findEntities($em, $espace_id = null, $projet_id = null, $chantier_id = null) {
    	$data = array('response' => null, 'espace' => null, 'projet' => null, 'chantier' => null);
    	if($espace_id) {
    		$data['espace'] = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    		if($data['espace']==null) {
    			$this->addFlash('error', "Espace non reconnu");
    			return $this->redirect($this->generateUrl('dashboard'));
    		}
    	} elseif($projet_id) {
    		$data['projet'] = $em->getRepository('OrangeMainBundle:Projet')->find($projet_id);
    		if($data['projet']==null) {
    			$this->addFlash('error', "Projet non reconnu");
    			return $this->redirect($this->generateUrl('dashboard'));
    		}
    	} elseif($chantier_id) {
    		$data['chantier'] = $em->getRepository('OrangeMainBundle:Chantier')->find($chantier_id);
    		if($data['chantier']==null) {
    			$this->addFlash('error', "Chantier non reconnu");
    			return $this->redirect($this->generateUrl('dashboard'));
    		}
    	}
    	return $data;
    }
    
    /**
     * @Route("/filtrer_les_types_actions", name="filtrer_les_types_actions")
     * @Template()
     */
    public function filterAction(Request $request) {
    	$form = $this->createForm(new TypeActionCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('type_action_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('type_action_criteria'), $form);
    		return array('form' => $form->createView());
    	}
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\TypeAction $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
    			sprintf('<span style="background-color: %s;padding-left: 10px;">&nbsp;</span>', $entity->getCouleur()),
    			$entity->getType(),
    			$this->get('orange_main.actions')->generateActionsForTypeAction($entity)
    		);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('t.type'), $request);
    }
}
