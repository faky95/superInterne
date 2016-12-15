<?php

namespace Orange\MainBundle\Controller;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Form\UtilisateurType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Criteria\UtilisateurCriteria;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Form\LoadingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DoctrineExtensions\Query\Mysql\Date;
use Orange\QuickMakingBundle\Annotation\QMLogger;

/**
 * Utilisateur controller.
 */
class UtilisateurController extends BaseController
{
    /**
     * Lists all Utilisateur entities.
     * @QMLogger(message="Liste des utilisateurs")
     * @Route("/les_utilisateurs", name="les_utilisateurs")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
    	$this->get('session')->set('utilisateur_criteria', new Request());
        return array(
        );
    }
    
    /**
     * Lists  entities.
     *
     *@Route("/liste_des_utilisateurs", name="liste_des_utilisateurs")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new UtilisateurCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('utilisateur_criteria'), $form);
    	$criteria = $form->getData();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Utilisateur')->listAllElements($criteria);
    	$this->get('session')->set('data',array('query' => $queryBuilder->getDql(),'param' =>$queryBuilder->getParameters()) );
    	return $this->paginate($request, $queryBuilder);
    }
    	

    /**
     * Finds and displays a Utilisateur entity.
     * @QMLogger(message="Détail d'un utilisateurs")
     * @Route("/details_utilisateur/{id}", name="details_utilisateur")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Utilisateur entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Finds and displays a Utilisateur entity.
     * @QMLogger(message="Transfert actions en masse")
     * @Route("/transfert_action/{id}", name="transfert_action")
     * @Method("GET")
     * @Template("OrangeMainBundle:Utilisateur:transfert.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function transfertAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    
    	$entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
    	$actions = $em->getRepository('OrangeMainBundle:Action')->actionsTransfert($id);
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Utilisateur entity.');
    	}
    
    
    	return array(
    			'entity'      => $entity,
    	);
    }
    
    
    /**
     * Finds and displays a Utilisateur entity.
     *
     * @Route("/details_utilisateur_espace/{user_id}/{espace_id}", name="details_utilisateur_espace")
     * @Method("GET")
     * @Template("OrangeMainBundle:Utilisateur:showEspace.html.twig")
     */
    public function showEspace($user_id, $espace_id)
    {
    	$em = $this->getDoctrine()->getManager();
    
    	$entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($user_id);
    	$espace = $em->getRepository('OrangeMainBundle:Espace')->find($espace_id)->getLibelle();
    	$action = $em->getRepository('OrangeMainBundle:Action')->listActionsUserByEspace($user_id, $espace_id);
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Utilisateur entity.');
    	}
    
    	$deleteForm = $this->createDeleteForm($user_id);
    
    	return array(
    			'espace' => $espace,
    			'entity'      => $entity,
    			'delete_form' => $deleteForm->createView(),
    			'actions' => $action
    	);
    }
    
  

    /**
     * Displays a form to edit an existing Utilisateur entity.
     * @QMLogger(message="Modification utilisateur")
     * @Route("/edition_utilisateur/{id}", name="edition_utilisateur")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Utilisateur entity.');
        }
        
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');
        
       	$form = $formFactory->createForm();
        $form->setData($entity);
        
        $form->handleRequest($request);
        	
        	if ($form->isValid()) {
        		/** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        		$userManager = $this->get('fos_user.user_manager');
				if($entity->getIsAdmin()){
					$entity->addRole('ROLE_ADMIN');
				}else{
					$entity->removeRole('ROLE_ADMIN');
				}        		
        		$event = new FormEvent($form, $request);
        		$userManager->updateUser($entity);
        		if (null === $response = $event->getResponse()) {
        		   $url = $this->generateUrl('les_utilisateurs');
        		   $response = new RedirectResponse($url);
				}
        		return $response;
        	}
        return array(
            'entity'      => $entity,
        	'edit_form'   => $form->createView()
        );
    }

    /**
    * Creates a form to edit a Utilisateur entity.
    *
    * @param Utilisateur $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Utilisateur $entity)
    {
        $form = $this->createForm(new UtilisateurType($entity), array(
            'action' => $this->generateUrl('edition_utilisateur', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Utilisateur entity.
     *  @QMLogger(message="Mise à jour utilisateur")
     * @Route("/{id}/modifier_utilisateur", name="modifier_utilisateur", requirements={ "id"=  "\d+"})
     * @Method("POST")
     * @Template("OrangeMainBundle:Utilisateur:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
        $form = $this->createCreateForm($entity);
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
        	$form->handleRequest($request);
        	if ($form->isValid()) {
        		$em->persist($entity);
        		$em->flush();
        		return $this->redirect($this->generateUrl('les_utilisateurs'));
        	}
        }
        return array('entity' => $entity, 'edit_form' => $form->createView());
    }
     /**
     * Deletes a Utilisateur entity.
     * @QMLogger(message="Suppression utilisateur")
     * @Route("/{id}/supprimer_utilisateur", name="supprimer_utilisateur")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
			
            if ($entity) {
            	if($entity->isEnabled()==true){
            		$entity->setEnabled(false);
            		$em->persist($entity);
            		$em->flush();
            		$this->container->get('session')->getFlashBag()->add('sucess', array (
            				'title' =>'Notification',
            				'body' => 'Cet utilisateur a  ete desactive! '
            		));
            	}else{
            		$entity->setEnabled(true);
            		$em->persist($entity);
            		$em->flush();
            		$this->container->get('session')->getFlashBag()->add('sucess', array (
            				'title' =>'Notification',
            				'body' => 'Cet utilisateur a  ete active! '
            		));
            	}
            	
            }else{
                throw $this->createNotFoundException('Unable to find Utilisateur entity.');
            }

        return $this->redirect($this->generateUrl('les_utilisateurs'));
    }

    /**
     * Creates a form to delete a Utilisateur entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('supprimer_utilisateur', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Supprimer utilisateur
     * @QMLogger(message="Changement utilisateur")
     * @Route("/changement_etat_utilisateur/{etat}/{id}", name="utilisateur_etat")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerAction($id, $etat){
    
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Utilisateur')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Cet utilisateur n\'existe pas .');
    	}
    	 
    	switch ($etat){
    		case 'activer' :
    			$entity->setEnabled(true);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => 'L\'utilisateur à été activé avec succes ! '
    			));
    			
    		break;
    		case 'desactiver':
    			$entity->setEnabled(false);
    			$em->flush();
    			$this->get('session')->getFlashBag()->add('success', array (
    					'title' =>'Notification',
    					'body' => ' L\'utilisateur à été désactivé avec succes ! '
    			));
    		break; 
    	}
    
    	return $this->redirect($this->generateUrl('utilisateur'));
    }
	    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Utilisateur $entity
     * @return array
     */
    
    protected function addRowInTable($entity) {
    	return array(
    			$entity->__toString(),
    			$entity->getMatricule(),
    			$entity->getEmail(),
    			$entity->getUsername(),
    			$this->showEntityStatus($entity, 'etat'),
    			$this->get('orange_main.actions')->generateActionsForUtilisateur($entity)
    	);
    }
    
    /**
     * @QMLogger(message="Exportation utilisateur")
     * @Route("/export_utilisateur", name="export_utilisateur")
     * @Template()
     */
    public function exportAction() {
    	$queryBuilder = $this->get('session')->get('data', array());
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery($queryBuilder['query']);
    	$query->setParameters($queryBuilder['param']);
    	$data = $this->get('orange.main.data')->exportUtilisateur($query->execute());
    	$objWriter = $this->get('orange.main.extraction')->exportUser($data);
    	$filename = sprintf("Extraction des utilisateurs du %s.xlsx", date('d-m-Y à H:i:s'));
    	$objWriter->save($this->get('kernel')->getWebDir()."/upload/user/$filename");
    	return $this->redirect($this->getUploadDir().$filename);
    }
    
    /**
     * @Route("/filtrer_utilisateurs", name="filtrer_utilisateurs")
     * @Template()
     */
     
    public function filtreAction(Request $request) {
    	$form = $this->createForm(new UtilisateurCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('utilisateur_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('utilisateur_criteria'), $form);
    		return array('form' => $form->createView());
    		 
    	}
    	 
    }
    
    /*
     * reperetoire de sauvegarde des reporting
     */
    private function getUploadDir() {
    	return $this->getRequest()->getBaseUrl().($this->get('kernel')
    			->getEnvironment()=='prod' ? '' : '/..')."/upload/user/";
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array("CONCAT(CONCAT(u.prenom, ' '), u.nom)",  'u.nom', 'u.prenom', 'u.username', 'u.matricule'), $request);
    }
    /**
     * @Route("/chargement_utilisateur", name="chargement_utilisateur")
     * @Template()
     */
    public function loadingAction() {
        $form = $this->createForm(new LoadingType());
        return array('form' => $form->createView());
    }
    
    /**
     * @Route("/importer_utilisateur", name="importer_utilisateur")
     * @Method("POST")
     * @Template()
     */
    public function importAction(Request $request) {
			$em = $this->getDoctrine()->getManager();
        $buP=$this->getUser()->getStructure()->getBuPrincipal()->getId();
        $form = $this->createForm(new LoadingType());
        $form->handleRequest($request);
        if($form->isValid()) {
            $data = $form->getData();
            try {
                $number = $this->get('orange.main.loader')->loadUtilisateur($data['file'], $buP);
				$users = $em->getRepository('OrangeMainBundle:Utilisateur')->getUsers($number['ids']);
				$nbr = $number['nbr'];
                foreach ($users as $value){
                	$this->get('orange.main.mailer')->notifNewUser($value->getEmail(), $this->getUser()->getEmail(), $value);
                }
                $this->get('session')->getFlashBag()->add('success', "Le chargement s'est effectué avec succès! Nombre d'utilisateurs chargées: $nbr");
                return $this->redirect($this->generateUrl('les_utilisateurs'));
            } catch(\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            }
        }
        return $this->render('OrangeMainBundle:Utilisateur:loading.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @Route("/send_mail", name="send_mail")
     * @
     */
    public function sendMailSupportAction(Request $request){
    	$to=array("mamekhady.diouf@orange-sonatel.com","mamekhady.diouf@orange-sonatel.com");
    	$subject= $request->request->get('error');
    	$body = $request->request->get('page');
    	$doc="".date("Y_m_d");
    	$dossier = $this->get('kernel')->getWebDir()."/upload/bugs/".$doc;
    	if(! file_exists(_dossier))
    		mkdir($dossier, 0777, true);
    	$file="bug-".Date("His").".html";
    	$chemin=$dossier."/".$file;
    	file_put_contents($chemin, $body);
    	$sendMail = $this->container->get('orange.main.mailer');
    	$sendMail->send($to, $cc = null, $subject,  $this->renderView("OrangeMainBundle:Utilisateur:sendMailSupport.html.twig", array('file'=>$file, 'dossier'=>$doc)));
    	return $this->render("OrangeMainBundle:Utilisateur:sendMailSupport.html.twig", array('file'=>$file, 'dossier'=>$doc));
    }
    
}
