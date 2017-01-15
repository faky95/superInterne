<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\ActionCyclique;
use Orange\MainBundle\Form\ActionCycliqueType;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\Statut;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Criteria\ActionCriteria;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\MainBundle\Entity\Action;

/**
 * ActionCyclique controller.
 * @Route("/actioncyclique")
 */
class ActionCycliqueController extends BaseController
{

	/**
	 * Lists all ActionCyclique entities.
	 * @Route("/", name="actioncyclique")
	 * @Method("GET")
	 * @Template()
	 */
	public function indexAction() {
		return array();
	}

	/**
	 *  @Route("/liste_actions_cycliques", name="liste_actions_cycliques")
	 */
	public function listeCycliqueAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new ActionCriteria());
		//$this->modifyRequestForForm($request, $this->get('session')->get('action_criteria'), $form);
		$criteria = $form->getData();
		$queryBuilder = $em->getRepository('OrangeMainBundle:ActionCyclique')->filter($criteria);
		return $this->paginate($request, $queryBuilder);
	}

	/**
	 * Creates a new ActionCyclique entity.
	 * @QMLogger(message="Nouvelle action cyclique")
	 * @Route("/creer_action_cyclique", name="actioncyclique_create")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:ActionCyclique:new.html.twig")
	 */
	public function createAction(Request $request) {
		$entity = new ActionCyclique();
		$form = $this->createCreateForm($entity,'ActionCyclique');
		$form->handleRequest($request);
		if ($request->getMethod() === 'POST' ) {
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$entity->getAction()->setAnimateur($this->getUser());
				$em->persist($entity);
				$em->flush();
				ActionUtils::setReferenceAction($em, $entity->getAction());
				ActionUtils::changeStatutAction($em, $entity->getAction(), Statut::ACTION_NOUVELLE, $this->getUser(), "Nouvelle action créée.");
				return $this->redirect($this->generateUrl('actioncyclique_show', array('id' => $entity->getId())));
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
	 * Displays a form to create a new ActionCyclique entity.
	 * @Route("/nouvelle_action_cyclique", name="actioncyclique_new")
	 * @Method("GET")
	 * @Template()
	 */
	public function newAction() {
		$entity = new ActionCyclique();
		$entity->setAction(new Action());
		$form   = $this->createCreateForm($entity, 'ActionCyclique');
		return array('entity' => $entity, 'form' => $form->createView());
	}

	/**
	 * Finds and displays a ActionCyclique entity.
	 * @QMLogger(message="Visualisation action cyclique")
	 * @Route("/{id}/details_action_cyclique", name="actioncyclique_show")
	 * @Method("GET")
	 * @Template()
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:ActionCyclique')->find($id);
		$taches = $em->getRepository('OrangeMainBundle:Tache')->findByActionCyclique($id);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ActionCyclique entity.');
		}
		return array('entity' => $entity, 'taches' => $taches);
	}

	/**
	 * Displays a form to edit an existing ActionCyclique entity.
	 * @QMLogger(message="Modification action cyclique")
	 * @Route("/{id}/edition_action_cyclique", name="actioncyclique_edit")
	 * @Method("GET")
	 * @Template()
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:ActionCyclique')->find($id);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find ActionCyclique entity.');
		}
		$editForm = $this->createEditForm($entity);
		return array('entity' => $entity, 'edit_form' => $editForm->createView());
	}

	/**
	 * Creates a form to edit a ActionCyclique entity.
	 * @param ActionCyclique $entity The entity
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(ActionCyclique $entity) {
		$form = $this->createForm(new ActionCycliqueType(), $entity, array(
				'action' => $this->generateUrl('actioncyclique_update', array('id' => $entity->getId())),
				'method' => 'POST',
			));
		$form->add('submit', 'submit', array('label' => 'Update'));
		return $form;
	}
	
	/**
	 * Edits an existing ActionCyclique entity.
	 * @Route("/{id}/modifier_action_cyclique", name="actioncyclique_update")
	 * @Method("POST")
	 * @Template("OrangeMainBundle:ActionCyclique:edit.html.twig")
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('OrangeMainBundle:ActionCyclique')->find($id);
		if(!$entity) {
			throw $this->createNotFoundException('Unable to find ActionCyclique entity.');
		}
		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);
		if($editForm->isValid()) {
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('actioncyclique_show', array('id' => $id)));
		}
		return array('entity' => $entity, 'edit_form' => $editForm->createView());
	}
	
	/**
	 * Deletes a ActionCyclique entity.
	 * @Route("/{id}/supprimer_action_cyclique", name="actioncyclique_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('OrangeMainBundle:ActionCyclique')->find($id);
			if (!$entity) {
				throw $this->createNotFoundException('Unable to find ActionCyclique entity.');
			}
			$em->remove($entity);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('actioncyclique'));
	}

	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le r�sultat de la requ�te
	 * @param \Orange\MainBundle\Entity\ActionCyclique $entity
	 * @return array
	 */
	protected function addRowInTable($entity) {
		return array(
				$entity->getInstance() ? $entity->getInstance()->__toString() : 'non renseigné',
				$entity->getLibelle(),
				$entity->getPorteur() ? $entity->getPorteur()->__toString() : 'non renseigné',
				$this->showEntityStatus($entity->getAction(), 'etat'),
				$this->get('orange_main.actions')->generateActionsForActionCyclique($entity)
			);
	}
	
	/**
	 * @todo ajoute un filtre
	 * @param sfWebRequest $request
	 */
	protected function setFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $aColumns, Request $request) {
		parent::setFilter($queryBuilder, array('a.reference', 'a.libelle', 'insta.libelle','sr.libelle', 'port.nom', 'port.prenom'), $request);
	}
}
