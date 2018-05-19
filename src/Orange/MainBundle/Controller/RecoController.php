<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Reco;
use Orange\MainBundle\Form\RecoType;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\Statut;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Criteria\ActionCriteria;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Entity\Action;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\Criteria\RecoCriteria;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reco controller.
 */
class RecoController extends BaseController
{

	/**
	 * Lists all reco entities.
	 * @Route("/", name="reco")
	 * @Method({"GET","POST"})
	 * @Template()
	 */
	public function indexAction(Request $request) {
		$form = $this->createForm(new RecoCriteria());
		$this->get('session')->set('reco_criteria', array());
		if($request->getMethod()=='POST') {
			if(isset($data['effacer'])) {
				$this->get('session')->set('reco_criteria', array());
			} else {
				$this->get('session')->set('reco_criteria', $request->request->get($form->getName()));
				$form->handleRequest($request);
			}
		}
		return array('form'=>$form->createView());
	}

	/**
	 *  @Route("/liste_des_recos", name="liste_des_recos")
	 */
	public function listAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		$this->modifyRequestForForm($request, $this->get('session')->get('reco_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:Reco')->filter($criteria);
		$queryBuilderExport = $em->getRepository('OrangeMainBundle:Reco')->filterForExport($criteria);
		$qbOccurence = $em->getRepository('OrangeMainBundle:Tache')->filterForExport($criteria);
		$this->get('session')->set('occurence', array(
				'query' => $qbOccurence->getDql(), 'param' => $qbOccurence->getParameters(), 'totalNumber' => $this->getLengthResults($qbOccurence, 'id')
			));
		$this->get('session')->set('data', array('query' => $queryBuilderExport->getDql(), 'param' => $queryBuilderExport->getParameters()));
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * Creates a new reco entity.
	 * @QMLogger(message="Nouvelle reco")
	 * @Route("/creer_reco", name="creer_reco")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:reco:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new Reco();
		$form = $this->createCreateForm($entity,'reco');
		$form->handleRequest($request);
		if ($request->getMethod() === 'POST' ) {
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$entity->getAction()->setAnimateur($this->getUser());
				$em->persist($entity);
				$em->flush();
				ActionUtils::setReferenceAction($em, $entity->getAction());
				if(null!=$tache=$entity->newTache($this->getParameter('pas'))) {
					$contributeurs = array();
					foreach($entity->getAction()->getContributeur() as $contributeur) {
						$contributeurs[] = $contributeur->getUtilisateur()->getEmail();
					}
					$this->get('orange.main.mailer')->notifNewTache(array($entity->getAction()->getPorteur()->getEmail()), $contributeurs, $tache);
				}
				$em->persist($entity);
				$em->flush();
				ActionUtils::changeStatutAction($em, $entity->getAction(), Statut::ACTION_NOUVELLE, $this->getUser(), "Nouvelle action créée.");
				return $this->redirect($this->generateUrl('reco_show', array('id' => $entity->getId())));
			}
			if(!$form->isValid()) {
				if(!empty($form->getErrorsAsString())) {
					$this->container->get('session')->getFlashBag()->add('error', array (
								'title' => 'Notification', 'body' => 'Des erreurs sont survenues. Veuillez réessayer .'
							));
				}
			}
		}
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * Displays a form to create a new reco entity.
	 * @Route("/nouvelle_reco", name="nouvelle_reco")
	 * @Method("GET")
	 * @Template()
	 */
	public function newAction() {
		$entity = new Reco();
		$entity->setAction(new Action());
		$form   = $this->createCreateForm($entity, 'reco');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * Finds and displays a reco entity.
	 * @QMLogger(message="Visualisation d'une reco")
	 * @Route("/{id}/details_reco", name="details_reco")
	 * @Method("GET")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:reco')->find($id);
		$taches = $em->getRepository('OrangeMainBundle:Tache')->findByreco($id);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find reco entity.');
		}
		return array('entity' => $entity, 'taches' => $taches);
	}

	/**
	 * Displays a form to edit an existing reco entity.
	 * @QMLogger(message="Modification d'une reco")
	 * @Route("/{id}/edition_reco", name="reco_edit")
	 * @Method("GET")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:reco')->find($id);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find reco entity.');
		}
		$editForm = $this->createEditForm($entity);
		return array('entity' => $entity, 'edit_form' => $editForm->createView());
	}
	
	/**
	 * Edits an existing reco entity.
	 * @Route("/{id}/modifier_reco", name="modifier_reco")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:reco:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:reco')->find($id);
		if(!$entity) {
			throw $this->createNotFoundException('Unable to find reco entity.');
		}
		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);
		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('reco_show', array('id' => $id)));
		}
		return array('entity' => $entity, 'edit_form' => $editForm->createView());
	}
	
	/**
	 * @QMLogger(message="Exportation les recos")
	 * @Route("/export_reco", name="export_reco")
	 * @Method("GET")
	 */
	public function exportAction(){
		$em = $this->getDoctrine()->getEntityManager();
		$response = new Response();
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$response->headers->set('Content-Disposition', sprintf('attachment; filename=Extraction des actions cycliques du %s.xlsx', date('YmdHis')));
		$response->sendHeaders();
		$queryBuilder = $this->get('session')->get('data');
		$query = $em->createQuery($queryBuilder['query']);
		$query->setParameters($queryBuilder['param']);
		$statut = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
		$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
		$actions       = $query->getArrayResult();
		$objWriter     = $this->get('orange.main.extraction')->exportreco($actions, $statut->getQuery()->execute());
		$objWriter->save('php://output');
		return $response;
	}

	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requéte
	 * @param \Orange\MainBundle\Entity\reco $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
		return array(
				$entity->getAction()->getReference(),
				$entity->getLibelle(),
				$entity->getPorteur() ? $entity->getPorteur()->__toString() : 'non renseigné',
				$entity->getPas() ? $entity->getPas()->getLibelle() : 'Non renseignée',
				$entity->getTache()->count() ? $this->showEntityStatus($entity->getTache()->last(), 'etat') : 'NA',
				$this->get('orange_main.actions')->generateActionsForreco($entity)
			);
	}
	
	/**
	 * @todo ajoute un filtre
	 * @param sfWebRequest $request
	 */
	protected function setFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('a.reference', 'a.libelle', 'insta.libelle', 'sr.libelle', 'port.nom', 'port.prenom'), $request);
	}
}
