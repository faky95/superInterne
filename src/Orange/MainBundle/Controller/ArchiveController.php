<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Criteria\ActionCriteria;
use Doctrine\ORM\QueryBuilder;
use Orange\QuickMakingBundle\Controller\BaseController;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\Entity\Extraction;

/**
 * Archive controller.
 */
class ArchiveController extends BaseController
{

    /**
     * Lists all archived Action entities.
     * @Route("/les_actionarchivees", name="les_actionarchivees")
     * @Template()
     */
    public function indexAction(Request $request)
    {
    	$form = $this->createForm(new ActionCriteria());
    	$data = $request->get($form->getName());
    	if($request->getMethod()=='POST') {
    		if(isset($data['effacer'])) {
    			$this->get('session')->set('action_archived_criteria', new Request());
    		} else {
    			$this->get('session')->set('action_archived_criteria', $request->request->get($form->getName()));
    			$form->handleRequest($request);
    		}
    	} else {
    		$this->get('session')->set('action_archived_criteria', new Request());
    	}
        return array('form' => $form->createView());
    }

    /**
     * @Route ("/liste_des_actionarchivees", name="liste_des_actionarchivees")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ActionCriteria());
        $this->modifyRequestForForm($request, $this->get('session')->get('action_archived_criteria'), $form);
        $criteria = $form->getData();
        $queryBuilder = $em->getRepository('OrangeMainBundle:Action')->listArchivedQueryBuilder($criteria);
        $queryExport = $em->getRepository('OrangeMainBundle:Action')->listArchivedForExport($criteria);
        $this->get('session')->set('data',array('query' => $queryExport->getDql(),'param' => $queryExport->getParameters()));
        return $this->paginate($request, $queryBuilder);
    }
    
    /**
     * @QMLogger(message="Extraction des actions archivées")
     * @Route("/export_actionarchivee", name="export_actionarchivee")
     */
    public function exportAction()
    {
       // return new Response('ok');
        $em = $this->getDoctrine()->getManager();
		$response = new Response();
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$response->headers->set('Content-Disposition', sprintf('attachment; filename=Extraction des actions archivées du %s.xlsx', date('YmdHis')));
		$response->sendHeaders();
        $queryBuilder = $this->get('session')->get('data', array());
        // var_dump($queryBuilder); exit();
		if($queryBuilder['totalNumber'] > 1000) {
			$type = \Orange\MainBundle\Entity\Extraction::$types['action'];
			$extraction = Extraction::nouvelleTache($queryBuilder['totalNumber'], $this->getUser(), $queryBuilder['query'], serialize($queryBuilder['param']), $type);
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
        $objWriter     = $this->get('orange.main.extraction')->exportArchiveAction($actions, $statut->getQuery()->execute());
        $objWriter->save('php://output');
       // var_dump($objWriter); exit();
        return $response;
        
    	

    }

    /**
     * @todo retourne le nombre d'enregistrements renvoyer par le r�sultat de la requ�te
     * @param QueryBuilder $queryBuilder
     * @return integer
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
     * (non-PHPdoc)
     * @see \Orange\MainBundle\Controller\BaseController::setFilter()
     */
    protected function setFilter(QueryBuilder$queryBuilder, $aColumns, Request $request) {
        parent::setFilter($queryBuilder, array('a.libelle'), $request);
    }

    /**
     * (non-PHPdoc)
     * @see \Orange\MainBundle\Controller\BaseController::setOrder()
     */
    protected function setOrder(QueryBuilder $queryBuilder, $aColumns, Request $request) {
        parent::setOrder($queryBuilder, array('a.libelle'), $request);
    }
}
