<?php
namespace Orange\MainBundle\Controller;

use Orange\QuickMakingBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * Controlleur du Tableau de bord
 * @author madiagne
 *
 */
class DashboardController extends BaseController {
	
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
		$res = $this->get('orange.main.calcul')->cumul($reqEvP);
		$colors = $this->getDoctrine()->getRepository('OrangeMainBundle:Formule')->listColorOfBu($bu);
		$dataEvP = $this->get('orange.main.calcul')->stats($bu, $res);
		$statsEvP = $this->getMapping()->getReporting()->setEntityManager($this->getDoctrine()->getManager())->mappingDataStatsEvo($dataEvP, 'semaine');
		$graphe=array();
		foreach(array_keys($statsEvP['taux']) as $key) {
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
		for($i=0;$i<40;$i++) {
			$this->get('orange.main.mailer')->send(array('madiagne.sylla@orange-sonatel.com' => 'Madiagne SYLLA'), null, 'test SUPER', sprintf('Action %s', $i));
		}
		return $this->redirect($this->generateUrl('dashboard'));
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
		return array(
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
		if($this->getUser()->hasRole(Utilisateur::ROLE_ADMIN)) {
			$role=Utilisateur::ROLE_ADMIN;
		} elseif($this->getUser()->hasRole(Utilisateur::ROLE_ANIMATEUR))
			$role=Utilisateur::ROLE_ANIMATEUR;
		elseif ($this->getUser()->hasRole(Utilisateur::ROLE_RAPPORTEUR))
		   $role=Utilisateur::ROLE_RAPPORTEUR;
		elseif($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
			$role=Utilisateur::ROLE_MANAGER;
		}else{
			$role=Utilisateur::ROLE_PORTEUR;
		}
		$rq=$rep->listAllElementsGeneral($role);
		$map= $this->getMapping()->getReporting()->transformRequeteToSimpleNull($rq);
		return array('req'=>$map);
	}
	/**
	 * Tableau de bord
	 * @QMLogger(message="Page d'accueil")
	 * @Route("/mail", name="mail")
	 * @Method("GET")
	 * @Template()
	 */
	public function indexAction()
	{
		$message = (new \Swift_Message('Hello Email'))
		->setFrom(array('orange@orange.sn'=>'super'))
        ->setTo('fatoukine.ndao@orange-sonatel.com')
        ->setCc('madiagne.sylla@orange-sonatel.com')
        ->setBody('ok');
		 $this->get('mailer')->send($message);
		return $this->render('OrangeMainBundle:Dashboard:mail.html.twig');
	}
	
	
}
