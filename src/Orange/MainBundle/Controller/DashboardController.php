<?php
namespace Orange\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\QuickMakingBundle\Annotation\QMLogger;
use Orange\MainBundle\Entity\Reporting;
/**
 * Controlleur du Tableau de bord
 * @author madiagne
 *
 */
class DashboardController extends Controller {
	
	CONST NBSEM=53;
	
	/**
	 * Tableau de bord 
	 * @QMLogger(message="Page d'accueil")
	 * @Route("/", name="dashboard")
	 * @Method("GET")
	 * @Template()
	 */
	public function dashboardAction() {
		$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
		$user=$this->getUser();
		$bu=$user->getStructure()->getBuPrincipal();
		$reqEvP=$rep->getStatsUserBySemaine($user, 1, null);
		$res = $this->container->get('orange.main.calcul')->cumul($reqEvP);
		$colors = $this->getDoctrine()->getRepository('OrangeMainBundle:Formule')->listColorOfBu($bu);
		$dataEvP = $this->container->get('orange.main.calcul')->stats($bu, $res);
		$statsEvP = $this->container->get('orange.main.dataStats')->mappingDataStatsEvo($dataEvP, 'semaine');
		$graphe=array();
		foreach($statsEvP['taux'] as $key=>$value) {
			$graphe[$key]=array();
		}
		foreach ($statsEvP['semaine'] as $key=>$values) {
			$i=0;
			foreach ($values['data'] as $cle=>$val){
				if(isset($graphe[$cle]))
					$graphe[$cle][]=$val;
			}
		}
		$semaines=array();
		for ($i=1;$i<=Date("W");$i++)
			$semaines[$i-1]=$i;
		return array('semaines'=>$semaines, 'graphe'=>$graphe, 'couleurs'=>$colors);
	}

	public function getStatus($bu) {
		$formule=$this->getDoctrine()->getManager()->getRepository('OrangeMainBundle:Formule')->getTauxStatsByBu($bu);
		$statuts = array(
				'nbAbandon' => 'Abandon',
				'nbDemandeAbandon' => "Demande d'abandon",
				'nbFaiteDelai' => "Faite dans les délais",
				'nbFaiteHorsDelai' => "Faite hors délai",
				'nbNonEchue' => "Non échue",
				'nbEchueNonSoldee' => "Echue non soldée",
				'nbSoldeeHorsDelais' => 'Soldée hors délai',
				'nbSoldeeDansLesDelais' => "Soldée dans les délais",
				'total' => "Total",
			);
		if(count($formule)>0) {
			foreach ($formule as $form) {
				$statuts[$form['libelle']] = $form['libelle'];
			}
		}
		return $statuts;
	
	}
	
	public function mapIds($data){
		$array = array();
		foreach($data as $value){
			array_push($array, $value['id']);
		}
		return $array;
	}
	
	/**
	 * @Route("/testst", name="test")
	*/
	public function testAction() {
		$em = $this->getDoctrine()->getManager();
		$etats = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
		$reporting = $em->getRepository('OrangeMainBundle:Reporting')->find(89);
		$query = $em->createQuery($reporting->getRequete());
		$query->setParameters(unserialize($reporting->getParameter()));
// 		if ($reporting->getQuery()) {
			$query2 = $em->createQuery($reporting->getQuery());
			$query2->setParameters(unserialize($reporting->getParameter()));
			//$idActions = $this->mapIds($query2->execute());
// 			$actions = $em->getRepository('OrangeMainBundle:Action')->filterExportReporting($idActions);
			$actions = array();
// 		}
		$req = $this->get('orange.main.dataStats')->combineTacheAndActionByPorteur($query->getArrayResult());
		$arrType=unserialize($reporting->getArrayType());
		$map= $this->get('orange.main.dataStats')->transformRequeteToPorteur($req, $arrType);
		$bu = $reporting->getUtilisateur()->getStructure()->getBuPrincipal();
		$data = $this->get('orange.main.calcul')->stats($bu, $map);
		$data = $this->get('orange.main.dataStats')->mappingDataStats($data, 'instance', $arrType, $bu);
		$objWriter = $this->get('orange.main.reporting')->reportinginstanceAction($data, $this->getStatus($bu), $actions, $etats->getQuery()->execute());
		$filename = $reporting->getLibelle().date("Y-m-d_H-i").'.xlsx';
		$objWriter->save($this->get('kernel')->getRootDir()."//..//web//upload//reporting//$filename");
		return $this->redirect(sprintf('/super/web/upload/reporting/%s', $filename));
	} 
	
	/**
	 * @Method("GET")
	 *  @Template()
	 */
	public function tauxRealisationAction(){
		$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
		$actionTotal=$rep->getTotalAction();
		$actionRealiseeDelai=$rep->getActionRealiseeDansLesDelais();
 		$actionRealiseeDelai=$rep->getActionRealiseeDansLesDelais();
		$actionRealisee=$rep->getActionRealisee();
		if($actionTotal!=0){
		$tauxRealisationGlobaleDelais=number_format(($actionRealiseeDelai/$actionTotal)*100,2);
		$tauxRealisationGlobale=number_format(($actionRealisee/$actionTotal)*100,2); 
		}else{
			$tauxRealisationGlobaleDelais=0;
			$tauxRealisationGlobale=0;
		}
		return  array(
			 	'tauxRealisationGlobaleDelais'=>$tauxRealisationGlobaleDelais,
				'tauxRealisationGlobale'=>$tauxRealisationGlobale,
				'actionRealiseeDelai'=>$actionRealiseeDelai,
				'actionRealisee'=>$actionRealisee, 
				'actionRealiseeDelai'=>$actionRealiseeDelai,
				'actionTotal'=>$actionTotal
				
		);
	}
	/**
	 * @Method("GET")
	 * @Template()
	 * @Route("/testgf", name="testgf")
	 * 
	 */
	public function statistiqueGeneralAction(){
		$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
		$map=array();
		if($this->getUser()->hasRole(Utilisateur::ROLE_ADMIN))
			$role=Utilisateur::ROLE_ADMIN;
		elseif($this->getUser()->hasRole(Utilisateur::ROLE_ANIMATEUR))
			$role=Utilisateur::ROLE_ANIMATEUR;
		elseif ($this->getUser()->hasRole(Utilisateur::ROLE_RAPPORTEUR))
		   $role=Utilisateur::ROLE_RAPPORTEUR;
		elseif($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
			$role=Utilisateur::ROLE_MANAGER;
		}else{
			$role=Utilisateur::ROLE_PORTEUR;
		}
		$rq=$rep->listAllElementsGeneral($role);
// 		var_dump($rq);exit;
		$map= $this->container->get('orange.main.dataStats')->transformRequeteToSimpleNull($rq);
		return array(
				'req'=>$map,
		);
	}
	
	
}
