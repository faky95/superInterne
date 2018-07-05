<?php
namespace Orange\MainBundle\Controller;

use Orange\MainBundle\Entity\Action;
use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\Entity\Extraction;

/**
 * Tache controller.
 */
class TacheController extends BaseController
{

    /**
     * Finds and displays a Action entity.
     * @QMLogger(message="Visualisation d'une occurence")
     * @Route("/{id}/details_occurence", name="details_occurence")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $tache = $em->getRepository('OrangeMainBundle:Tache')->find($id);
        //$this->denyAccessUnlessGranted('read', $tache, 'Unauthorized access!');
    	if(!$tache) {
    		$this->addFlash('error', "Impossible de voir les détails, cette action n'est pas reconnue");
    		return $this->redirect($this->generateUrl('mes_actions'));
    	}
        return array('tache' => $tache);
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
     * @QMLogger(message="Extraction des occurences")
     * @Route("/export_occurence", name="export_occurence")
     */
    public function exportAction() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	$response->headers->set('Content-Disposition', sprintf('attachment; filename=Extraction des occurences du %s.xlsx', date('YmdHis')));
    	$response->sendHeaders();
    	$queryBuilder = $this->get('session')->get('occurence', array());
    	if($queryBuilder['totalNumber'] > 10000) {
    		$type = \Orange\MainBundle\Entity\Extraction::$types['tache'];
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
    	$occurences       = $query->getArrayResult();
    	$objWriter     = $this->get('orange.main.extraction')->exportOccurence($occurences, $statut->getQuery()->execute());
    	$objWriter->save('php://output');
    	return $response;
    }
    
    
}
