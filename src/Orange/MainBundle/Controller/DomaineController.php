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
use Orange\QuickMakingBundle\Model\EntityManager;
use Orange\MainBundle\Entity\Chantier;

/**
 * Domaine controller.
 */
class DomaineController extends BaseController
{

    /**
     * @QMLogger(message="Liste des domaines")
     * @Route("/les_domaine", name="les_domaine")
     * @Route("/{espace_id}/les_domaine_by_espace", name="les_domaine_by_espace")
     * @Route("/{projet_id}/les_domaine_by_projet", name="les_domaine_by_projet")
     * @Route("/{chantier_id}/les_domaine_by_chantier", name="les_domaine_by_chantier")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($espace_id=null, $projet_id=null, $chantier_id=null)
    {
    	$em = $this->getDoctrine()->getManager();
    	$data = $this->findEntities($em, $espace_id, $projet_id, $chantier_id);
        $this->get('session')->set('domaine_criteria', new Request());
    	return array('espace' => $data['espace'], 'projet' => $data['projet'], 'chantier' => $data['chantier']);
    }

    /**
     * Lists  entities.
     * @Route("/liste_des_domaines", name="liste_des_domaines")
     * @Route("/{espace_id}/liste_des_domaines_by_espace", name="liste_des_domaines_by_espace")
     * @Route("/{projet_id}/liste_des_domaines_by_projet", name="liste_des_domaines_by_projet")
     * @Route("/{chantier_id}/liste_des_domaines_by_chantier", name="liste_des_domaines_by_chantier")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request, $espace_id=null, $projet_id=null, $chantier_id=null) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new DomaineCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('domaine_criteria'), $form);
    	if($espace_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:Domaine')->getDomainesByEspace($espace_id);
    	} elseif($projet_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:Domaine')->getDomainesByProjet($projet_id);
    	} elseif($chantier_id!=null) {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:Domaine')->getDomainesByChantier($chantier_id);
    	} else {
    		$queryBuilder = $em->getRepository('OrangeMainBundle:Domaine')->listQueryBuilder();
    	}
    	$this->get('session')->set('data', array('query' => $queryBuilder->getDql(), 'param' =>$queryBuilder->getParameters()) );
    	return $this->paginate($request, $queryBuilder);
    }
    
    /**
     * Creates a new Domaine entity.
     * @QMLogger(message="Création de domaine")
     * @Route("/creer_domaine", name="creer_domaine")
     * @Route("/{espace_id}/creer_domaine_to_espace", name="creer_domaine_to_espace")
     * @Route("/{projet_id}/creer_domaine_to_projet", name="creer_domaine_to_projet")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request, $espace_id=null, $projet_id=null) {
        $entity = new Domaine();
        $em = $this->getDoctrine()->getManager();
        $data = $this->findEntities($em, $espace_id, $projet_id);
        $form = $this->createCreateForm($entity, 'Domaine', array(
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
		            $data['espace']->getInstance()->addDomaine($entity);
		            $em->persist($data['espace']);
		        } elseif($projet_id!=null) {
		            $data['projet']->getInstance()->addDomaine($entity);
		            $em->persist($data['projet']);
		        } elseif(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
	            	$bu = $this->getUser()->getStructure()->getBuPrincipal();
	            	$bu->addDomaine($entity);
           		}
           		$em->flush();
           	}
            $this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Le domaine a été créé avec succès'));
            if($espace_id!=null) {
            	return new JsonResponse(array('url' => $this->generateUrl('les_domaine_by_espace', array('espace_id'=>$espace_id))));
            } elseif($projet_id!=null) {
            	return new JsonResponse(array('url' => $this->generateUrl('les_domaine_by_projet', array('projet_id'=>$projet_id))));
            } else {
          	  	return new JsonResponse(array('url' => $this->generateUrl('les_domaine')));
            }
       	}
        return $this->render('OrangeMainBundle:Domaine:new.html.twig', array(
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
     */
    public function newAction($espace_id=null, $projet_id=null)
    {
        $entity = new Domaine();
        $em = $this->getDoctrine()->getManager();
        $data = $this->findEntities($em, $espace_id, $projet_id);
        $form   = $this->createCreateForm($entity, 'Domaine', array(
        		'attr' => array('security_context' => $this->get('security.context'))
        	));
        return array('entity' => $entity, 'form'   => $form->createView(), 'espace'=> $data['espace'], 'projet'=> $data['projet']);
    }
    
    /**
     * Finds and displays a Domaine entity.
     * @QMLogger(message="Visualisation de domaine")
     * @Route("/details_domaine/{id}/", name="details_domaine")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);
        if(!$entity) {
            throw $this->createNotFoundException('Unable to find Domaine entity.');
        }
        return array('entity' => $entity);
    }

    /**
     * Displays a form to edit an existing Domaine entity.
     * @QMLogger(message="Modification d'un domaine")
     * @Route("/edition_domaine/{id}/", name="edition_domaine")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Domaine entity.');
        }
        $editForm = $this->createEditForm($entity);
        return array('entity' => $entity, 'edit_form' => $editForm->createView());
    }

    /**
    * Creates a form to edit a Domaine entity.
    * @param Domaine $entity The entity
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
        if($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Le domaine a été modifié avec succès'));
        		return new JsonResponse(array('url' => $this->generateUrl('les_domaine')));
        	}
        }
        return $this->render('OrangeMainBundle:Domaine:edit.html.twig', array(
        				'entity' => $entity, 'edit_form' => $form->createView()
        		), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
  	/**
     * Deletes a Domaine entity.
     * @Route("/{id}/supprimer_domaine", name="supprimer_domaine")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Domaine')->find($id);
        if($entity) {
            if($entity->getAction()->count()>0) {
           		$this->container->get('session')->getFlashBag()->add('failed', array(
    					'title' =>'Notification', 'body' => 'Cette structure est rattachée à des actions'
    				));
           	} else {
           		$em->remove($entity);
       			$em->flush();
       			$this->get('session')->getFlashBag()->add('success', array('title' => 'Notification', 'body' =>  'Le domaine a été supprimé avec succès'));
           	}
        } else {
        	throw $this->createNotFoundException('Domaine inexistant.');
        }
        return $this->redirect($this->generateUrl('les_domaine'));
    }
    
    /**
     * @param EntityManager $em
     * @param number $espace_id
     * @param number $projet_id
     * @param number $chantier_id
     * @return void
     */
    private function findEntities($em, $espace_id, $projet_id, $chantier_id) {
    	$data = array('response' => null);
    	if($espace_id) {
    		$data['espace'] = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id);
    		if($data['espace']) {
    			$this->addFlash('error', "Espace non reconnu");
    			return $this->redirect($this->generateUrl('dashboard'));
    		}
    	} elseif($projet_id) {
    		$data['projet'] = $em->getRepository('OrangeMainBundle:Projet')->find($projet_id);
    		if($data['projet']) {
    			$this->addFlash('error', "Projet non reconnu");
    			return $this->redirect($this->generateUrl('dashboard'));
    		}
    	} elseif($chantier_id) {
    		$data['chantier'] = $em->getRepository('OrangeMainBundle:Chantier')->find($chantier_id);
    		if($data['chantier']) {
    			$this->addFlash('error', "Chantier non reconnu");
    			return $this->redirect($this->generateUrl('dashboard'));
    		}
    	}
    	return $data;
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
