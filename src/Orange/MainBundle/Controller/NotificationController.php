<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Entity\Notification;
use Orange\MainBundle\Criteria\NotificationCriteria;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Entity\Extraction;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notification controller.
 */
class NotificationController extends BaseController
{

    /**
     * Lists all notifications entities.
     * @QMLogger(message="Liste des notifications")
     * @Route("/les_notifications", name="les_notifications")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
    	$form = $this->createForm(new NotificationCriteria());
    	$data = $request->get($form->getName());
    	if($request->getMethod()=='POST') {
    		if(isset($data['effacer'])) {
    			$this->get('session')->set('notification_criteria', new Request());
    		} else {
    			$this->get('session')->set('notification_criteria', $request->request->get($form->getName()));
    			$form->handleRequest($request);
    		}
    	} else {
    		$this->get('session')->set('notification_criteria', new Request());
    	}
    	return array('form' => $form->createView());
    }
    
    /**
     * Lists all Notifications entities.
     * @Route("/liste_des_notifications", name="liste_des_notifications")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$form = $this->createForm(new NotificationCriteria());
    	$this->modifyRequestForForm($request, $this->get('session')->get('notification_criteria'), $form);
    	$criteria = $form->getData();
    	$queryBuilder = $em->getRepository('OrangeMainBundle:Notification')->listNotifQueryBuilder($criteria);
    	$this->get('session')->set('data',array('query' => $queryBuilder->getDql(),'param' =>$queryBuilder->getParameters()));
    	return $this->paginate($request, $queryBuilder);
    }

    /**
     * Finds and displays a Notification entity.
     * @QMLogger(message="Visuslaisation d'une notification")
     * @Route("/{id}/details_notification", name="details_notification")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OrangeMainBundle:Notification')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notification entity.');
        }
        return array('entity' => $entity);
    }
    
    /**
     * @Route("/filtrer_les_notifications", name="filtrer_les_notifications")
     * @Template()
     */
    public function filterAction(Request $request) {
    	$form = $this->createForm(new NotificationCriteria());
    	if($request->getMethod()=='POST') {
    		$this->get('session')->set('bu_criteria', $request->request->get($form->getName()));
    		return new JsonResponse();
    	} else {
    		$this->modifyRequestForForm($request, $this->get('session')->get('notification_criteria'), $form);
    		return array('form' => $form->createView());
    
    	}
    
    }
    
    /**
     * @QMLogger(message="Extraction des notifications")
     * @Route("/export_notification", name="export_notification")
     */
    public function exportAction() {
    	$em = $this->getDoctrine()->getManager();
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	$response->headers->set('Content-Disposition', sprintf('attachment; filename=Extraction des notifications du %s.xlsx', date('YmdHis')));
    	$response->sendHeaders();
    	$queryBuilder = $this->get('session')->get('data', array());
    	if($queryBuilder['totalNumber'] > 10000) {
    		$type = \Orange\MainBundle\Entity\Extraction::$types['notification'];
    		$extraction = Extraction::nouvelleTache($queryBuilder['totalNumber'], $this->getUser(), $queryBuilder['query'], serialize($queryBuilder['param']), $type);
    		$em->persist($extraction);
    		$em->flush();
    		$this->addFlash('warning', "L'extraction risque de prendre du temps, le fichier vous sera envoyé par mail");
    		return $this->redirect($this->getRequest()->headers->get('referer'));
    	}
    	$query = $em->createQuery($queryBuilder['query']);
    	$query->setParameters($queryBuilder['param']);
    	$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
    	$objWriter     = $this->get('orange.main.extraction')->exportNotification($query->getResult());
    	$objWriter->save('php://output');
    	return $response;
    }
    
    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
     * @param \Orange\MainBundle\Entity\Notification $entity
     * @return array
     */
    protected function addRowInTable($entity) {
    	return array(
    			$entity->getTypeNotification()->getLibelle(),
    			$entity->getDestinataireInShort(),
    			$entity->getCopyInShort(),
    			$entity->getDate()->format('d F Y à H:i'),
    			$this->showEntityStatus($entity, 'etat'),
    		);
    }
    
    /**
     * @todo ajoute un filtre
     * @param sfWebRequest $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
    	parent::setFilter($queryBuilder, array('b.libelle'), $request);
    }
}
