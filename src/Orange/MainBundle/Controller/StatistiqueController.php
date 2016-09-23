<?php
namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Form\ActionType;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\SignalisationUtils;
use Orange\MainBundle\Utils\ActionUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Criteria\ActionCriteria;
use Orange\QuickMakingBundle\Controller\BaseController;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Form\LoadingType;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Criteria\StatistiqueActionCriteria;
use Orange\MainBundle\Criteria\StatistiqueCriteria;
use Orange\MainBundle\Entity\Reporting;
use Orange\MainBundle\Form\ReportingType;
use Orange\MainBundle\Entity\Statistique;
use Orange\QuickMakingBundle\Annotation\QMLogger;
class StatistiqueController extends BaseController
{
	 protected $web_dir = WEB_DIRECTORY;

     protected  $statuts= array(
                        'nbAbandon' => 'Abandonnée',
                        'nbDemandeAbandon' => "Demande d'abandon",
                        'nbFaiteDelai' => "Faite dans les délais",
                        'nbFaiteHorsDelai' => "Faite hors délai",
                        'nbNonEchue' => "Non échue",
                        'nbEchueNonSoldee' => "Echue non soldée",
                        'nbSoldeeHorsDelais' => 'Soldée hors délais',
                        'nbSoldeeDansLesDelais' => "Soldée dans les délais",
                        'total' => "Total",
                );
     
     protected  $effectifs= array(
            'nbUsers' => 'Nombre d utilisateur ',
            'nbMoyenActionByUser' => "Nombre moyen daction par utilisateur",
            'nbUsersActif' => "Nombre d\'utilisateur Actif",
            'nbMoyenActionByUserActif' => "Nombre moyen d\'action par utilisateur actif",
     );

	/**
     * Lists all Statistique entities.
     *
     * @Route("/", name="statistique")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:Statistique')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    //		statistique
    
    /**
     *  @Route("/tableauStatUtilisateur", name="tableauStatUtilisateur")
     *  @Method("GET")
     * @Template()
     *
     */
    public function tableauStatistiqueUtilisateurAction(){
    	$init=array();
    	$criteria=null;
    	$em=$this->getDoctrine()->getManager();
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
    	$bu=$this->getUser()->getStructure()->getBuPrincipal();
    	$instancesP=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->porteurQueryBuilder($init)->addSelect('i7.libelle')->distinct()->getQuery()->getArrayResult();
    	$statsP = $this->statistiqueByType("instance", Utilisateur::ROLE_PORTEUR, $instancesP, $criteria);
    	
    	$instancesC=$this->getDoctrine()->getRepository('OrangeMainBundle:instance')->getInstancesEnConributions($this->getUser()->getId());
    	$statsC = $this->statistiqueByType("instance", Utilisateur::ROLE_CONTRIBUTEUR, $instancesC, $criteria);
    	
    	$statutsM=$this->getStatus();
    	$statsM=array();
    	
    	if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
    		$id=$this->getUser()->getStructure()->getId();
    		$structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')
    										->getStructureAndStructureDirecteByStructure($id)->getQuery()->getArrayResult();
    		$statsM = $this->statistiqueByType("structure", Utilisateur::ROLE_MANAGER, $structures, $criteria);
    		foreach ($statsM['taux'] as $key => $taux){
    			$statutsM[$key] = $key;
    		}
    	}
    	$statuts=$this->getStatus();
    	return array(
    			'statut'=>$statuts,
    			'statutM'=>$statutsM,
    			'statsP'=>$statsP,
    			'statut'=> $statuts,
    			'statsC'=>$statsC,
    			'statsM'=>$statsM,
    			'nbTaux'=>$this->getNombreTaux(),
    
    	);
    }
     
    
    /**
     * @Route("/tableauStatUtilisateurEv", name="tableauStatUtilisateurEv")
     *  @Method("GET")
     * @Template()
     *
     */
    public function tableauStatistiqueEvoUtilisateurAction(){
    	$criteria=null;
    	$index=0; $init=array();
    	$semaines=$this->createArrSemaine();
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$bu=$this->getUser()->getStructure()->getBuPrincipal();
    	$user=$this->getUser();
    	$reqEvP=$rep->getStatsUserBySemaine($user, 1, $criteria);
    	$dataEvP = $this->container->get('orange.main.calcul')->stats($bu, $reqEvP);
    	$statsEvP = $this->container->get('orange.main.dataStats')->mappingDataStatsEvo($dataEvP, 'semaine');
    
    	$reqEvC=$rep->getStatsUserBySemaine($user, 2, $criteria);
    	$dataEvC = $this->container->get('orange.main.calcul')->stats($bu, $reqEvC);
    	$statsEvC = $this->container->get('orange.main.dataStats')->mappingDataStatsEvo($dataEvC, 'semaine');
    	$statsEvM=array();
    	if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
    		$id=$this->getUser()->getStructure()->getId();
    		$structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getStructureAndStructureDirecteByStructure($id)->getQuery()->getArrayResult();
    		$statsEvM=$this->createTableauEvoByType($structures, "structure",$criteria);
    	}
    	$statsEvAd=array();
    	if($this->getUser()->hasRole(Utilisateur::ROLE_ADMIN)){
    		$structures = $bu->getStructureInDashboardAsArray();
    		$statsEvAd = $this->createTableauEvoByType($structures, "structure",$criteria);
    	}
    
    	$statsEvAn=array();
    	if($this->getUser()->hasRole(Utilisateur::ROLE_ANIMATEUR)){
    		$instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->animateurQueryBuilder($init)->addSelect('i3.libelle')->getQuery()->getArrayResult();
    		$statsEvAn = $this->createTableauEvoByType($instances, "instance",$criteria);
    	}
    
    	$statuts=$this->getStatus();
    
    	return array(
    			'statut'=>$statuts,
    			'statsEvP'=>$reqEvP?$statsEvP:$reqEvP,
    			'statsEvC'=>$reqEvC?$statsEvC:$reqEvC,
    			'statsEvM'=>$statsEvM,
    			'statsEvAd'=>$statsEvAd,
    			'statsEvAn'=>$statsEvAn,
    			'semaines'=>$semaines,
    			'nbTaux'=>$this->getNombreTaux(),
    	);
    }
       
    /**
     * @Route("/tableauStatistiqueAdmin", name="tableauStatistiqueAdmin")
     * @Method("GET")
     * @Template()
     */
    public function tableauStatistiqueAdminAction(){
    	$criteria=null;
    	$structures = $this->getUser()->getstructure()->getBuPrincipal()->getStructureInDashboardAsArray();
    	$stats = $this->statistiqueByType("structure", Utilisateur::ROLE_ADMIN, $structures, $criteria);
    	$statuts=$this->getStatus();
    	foreach ($this->effectifs as $key => $eff){
    		$statuts[$key] = $eff;
    	}
    	return array(
    			'stats'=>$stats,
    			'statut'=>$statuts,
    			'effectif'=>$this->effectifs,
    			'nbTaux'=>$this->getNombreTaux(),
    	);
    }
    
   
    /**
     * Lists all Signalisation entities.
     *
     * @Route("/tb/signalisation", name="tableau_bord_signalisation")
     * @Template()
     */
    public function statSignalisationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $stats=$em->getRepository("OrangeMainBundle:Signalisation")->statsGroupByCode()->getQuery()->getArrayResult();
        $statsSign=array('Cloture'=>array(), 'Efficace'=>array(), 'Non efficace'=>array(), 'En cours'=>array(), 'abandonne'=>array());
        $total          = intval($em->getRepository("OrangeMainBundle:Signalisation")->totalSignalisation());
        foreach ($stats as $stat){
        	if ($stat['etatCourant']==Statut::SIGNALISATION_TRAITE_EFFICACEMENT){
        		$val=$statsSign['Efficace']['nombre']=intval($stat['total']);
        		$statsSign['Efficace']['taux']=($total==0)?"0%":number_format((($val/$total)*100),2)."%";
        	}elseif ($stat['etatCourant']==Statut::SIGNALISATION_TRAITE_NON_EFFICACEMENT){
        		$val=$statsSign['Non efficace']['nombre']=intval($stat['total']);
        		$statsSign['Non efficace']['taux']=($total==0)?"0%":number_format((($val/$total)*100),2)."%";
        	}elseif ($stat['etatCourant']==Statut::SIGNALISATION_VALIDER){
        		$val=$statsSign['En cours']['nombre']=intval($stat['total']);
        		$statsSign['En cours']['taux']=($total==0)?"0%":number_format((($val/$total)*100),2)."%";
        	}elseif ($stat['etatCourant']==Statut::SIGNALISATION_ABANDONNER){
        		$val=$statsSign['abandonne']['nombre']=intval($stat['total']);
        		$statsSign['abandonne']['taux']=($total==0)?"0%":number_format((($val/$total)*100),2)."%";
        	}
        }
        $statsSign['total']['nombre']=$total;
        $statsSign['total']['taux']="100%";
        return array('stats' => $statsSign);
    }
    
     /**
     *
     * @Route("/tableauStatistiqueAnimateur", name="tableauStatistiqueAnimateur")
     * @Template()
     */
    public function tableauStatistiqueAnimateurAction(Request $request){
        $criteria=null;
        $data=array();
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->animateurQueryBuilder($data)->addSelect('i3.libelle')->getQuery()->getArrayResult();
		$stats = $this->statistiqueByType("instance", Utilisateur::ROLE_ANIMATEUR, $instances, $criteria);
        $statuts=$this->getStatus();
        return $this->render("OrangeMainBundle:Statistique:simple_tableau_stats.html.twig",
        		 array('statut'   => $statuts,
                       'stats'    =>$stats,
        		 	   'type'	  =>'instance',
        		 	   'nbTaux'	  =>$this->getNombreTaux(),
        ));
    }
    
    /**
     * @Route("/{role}/statistique_generale", name="statistique_generale")
     * @Route("/vue_statique", name="vue_statique")
     * @Template("OrangeMainBundle:Statistique:vue_statique.html.twig")
     *
     */
    public function statsGeneraleAction(Request $request,$role=4){
    	$tabRoles=array(Utilisateur::ROLE_ADMIN, Utilisateur::ROLE_ANIMATEUR, Utilisateur::ROLE_MANAGER,
    					Utilisateur::ROLE_RAPPORTEUR, Utilisateur::ROLE_PORTEUR, Utilisateur::ROLE_CONTRIBUTEUR );
    	$tabByInstance=array();
    	$tabByStructure=array();
    	$tabCroise=array();
    	$bu=$this->getUser()->getStructure()->getBuPrincipal();
    	$repStruct=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure');
    	$repInst=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance');
    	$structures=null;
    	$instances=$repInst->getInstanceByRole($tabRoles[$role])->getQuery()->getArrayResult();
    	$graphe=array();
    	
    	if ($tabRoles[$role]==Utilisateur::ROLE_ADMIN){
    		$structures=$repStruct->getStructureAndStructureDirecteByStructure($this->getUser()->getStructure()->getRoot())->getQuery()->getArrayResult();
    	}
    	if ($tabRoles[$role]==Utilisateur::ROLE_RAPPORTEUR){
    		$tabStruct=$this->getUser()->getArrayRapporteurStructure();
    		$structures=(count($tabStruct)>1 ? $tabStruct : $repStruct->getStructureByRole($tabRoles[$role])->getQuery()->getArrayResult());
    	}
    		
    		
    	if ($tabRoles[$role]==Utilisateur::ROLE_ANIMATEUR || $tabRoles[$role]==Utilisateur::ROLE_MANAGER )
    		     $structures=$repStruct->getStructureByRole($tabRoles[$role])->getQuery()->getArrayResult();
    	
    	//var_dump($structures);exit;
    	$form=$this->createForm(new ActionCriteria(), null, array('attr'=>array( 'structures'=> $repStruct->getStructureByRole($tabRoles[$role]), 'instances'=>$repInst->getInstanceByRole($tabRoles[$role]) )));
    	$form->handleRequest($request);
    	if(($request->getMethod()=='POST') && $form->getData()){
    		$this->get('session')->set('action_criteria', $request->request->get($form->getName()));
    		if(count($form->getData()->instances)>0){
    			$insts=$form->getData()->instances;
	    		$instances=array();
	    		foreach($insts as $k=>$val)
	    			$instances[]=array('id'=>$val->getId(), 'libelle'=>$val->getLibelle());
    		}
    		if($form->getData()->getStructure()){
    			$str=$form->getData()->getStructure()->getId();
    			$structures=$repStruct->getStructureAndStructureDirecteByStructure($str)->getQuery()->getArrayResult();
    		}
    	}
    	$tabByInstance = $this->statistiqueByType('instance', $tabRoles[$role], $instances, $form->getData());
    	foreach ($tabByInstance['taux'] as $key => $taux){
    		$graphe[$key]=array();
    	}
    	$graphe=$this->createGraphe($tabByInstance['instance'],$graphe);
    	if($structures!=null)
    		$tabByStructure= $this->statistiqueByType('structure', $tabRoles[$role], $structures, $form->getData());
    	if($tabRoles[$role]==Utilisateur::ROLE_MANAGER){
    		$graphe=array();
    		foreach ($tabByStructure['taux'] as $key => $taux)
    			$graphe[$key]=array();
    		$graphe=$this->createGraphe($tabByStructure['structure'],$graphe);	
    	}
    	if($tabRoles[$role]==Utilisateur::ROLE_ADMIN || $tabRoles[$role]== Utilisateur::ROLE_RAPPORTEUR){
     			$req=$this->getDoctrine()->getRepository('OrangeMainBundle:Action')->getStatsByStructureInstance($tabRoles[$role],$form->getData());
     			$map=$this->container->get('orange.main.dataStats')->transformRequeteToCroise($req,$structures,$instances);
     			$data = $this->container->get('orange.main.calcul')->stats($bu, $map);
     			$tabCroise = $this->container->get('orange.main.dataStats')->mappingDataStatsCroise($data, 'structure','instance',$structures,$instances);
     			$graphe=$this->createManyGrapheStat($tabCroise,'structure','instance');
    	}
    	if($tabByInstance['instance']){
    		$tmp_inst = 1;
    	}else{
    		$tmp_inst = 0;
    	}
    	if ($tabByStructure){
    		$tmp_struct = 1;
    	}else{
    		$tmp_struct = 0;
    	}
    	return array(
    			'form'=>$form->createView(),
    			'nbTaux'=>$this->getNombreTaux(),
    			'tabByInstance'=>$tabByInstance,
    			'tabByStructure'=>$tabByStructure,
    			'tabCroise'=>$tabCroise,
    			'statut'=>$this->getStatus(),
    			'role'=>$role,
    			'instances'=>$instances,
    			'graphe'=>$graphe,
    			'structures'=>$structures,
    			'tmp_inst' => $tmp_inst,
    			'tmp_struct' => $tmp_struct
    	);
    }
    /**
     * 
     * @param unknown $type
     */
    public function statistiqueByType($type, $role, $arrType,$criteria){
    	$tableau=array();
    	$bu=$this->getUser()->getStructure()->getBuPrincipal();
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
    	if($type=='instance') {
    		$rq = $rep->getStatsByInstance($role, $criteria);
    		$reqActions = $rep->getStatsByInstance2($role, $criteria);
    		$req =$this->container->get('orange.main.dataStats')-> combineTacheAndAction($rq->getQuery()->getArrayResult());
    		$map= $this->container->get('orange.main.dataStats')->transformRequeteToSimple($req, $arrType);
    		$data = $this->container->get('orange.main.calcul')->stats($bu, $map);
    		$tableau = $this->container->get('orange.main.dataStats')->mappingDataStats($data, 'instance',$arrType);
    		$this->get('session')->set('donnees_reporting_actions_instance',array('query'=>$reqActions->getDQL() , 'param'=>$reqActions->getParameters()));
    		$this->get('session')->set('donnees_reporting_instance',array('data'=>$tableau, 'req'=>$rq->getDQL() , 'param'=>$rq->getParameters()));
    		$this->get('session')->set('reporting_instance',array('req' => $rq->getDQL(), 'param' => $rq->getParameters(), 'tp' => 2, 'arrType' => serialize($arrType)));
    		$this->get('session')->set('type',array('valeur' => 2));
    		
    	} else {
    		$rq=$rep->getStatsByStructure($role, $criteria);
    		$reqActions=$rep->getStatsByStructure2($role, $criteria);
    		$req =$this->container->get('orange.main.dataStats')-> combineTacheAndAction($rq->getQuery()->getArrayResult());
    		$mapM= $this->container->get('orange.main.dataStats')->transformRequeteToSimple($req, $arrType);
    		$data = $this->container->get('orange.main.calcul')->stats($bu, $mapM);
    		$tableau = $this->container->get('orange.main.dataStats')->mappingDataStats($data, 'structure',$arrType);
    		$this->get('session')->set('donnees_reporting_actions_structure',array('query'=>$reqActions->getDQL() , 'param'=>$reqActions->getParameters()));
    		$this->get('session')->set('donnees_reporting_structure',array('data'=>$tableau, 'req'=>$rq->getDQL(), 'param'=>$rq->getParameters() ));
    		$this->get('session')->set('reporting_structure',array('req' => $rq->getDQL(), 'param' => $rq->getParameters(), 'tp' => 1, 'arrType' => serialize($arrType)));
    		$this->get('session')->set('type',array('valeur' => 1));
    	}
    	
    	return $tableau;
    }
    
    
    /**
     * @Route("/{role}/statistique_generale_evo", name="statistique_generale_evo")
     * @Route("/vue_evolutive", name="vue_evolutive")
     * @Template("OrangeMainBundle:Statistique:vue_evolutive.html.twig")
     *
     */
    public function statsGeneraleEvoAction(Request $request,$role=4){
    	$tabRoles=array(Utilisateur::ROLE_ADMIN, Utilisateur::ROLE_ANIMATEUR, Utilisateur::ROLE_MANAGER,
    			Utilisateur::ROLE_RAPPORTEUR, Utilisateur::ROLE_PORTEUR, Utilisateur::ROLE_CONTRIBUTEUR );
    
    	$init=array();
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$repStruct=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure');
    	$repInst=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance');
    	$bu = $this->getUser()->getStructure()->getBuPrincipal();
    	$stats=array();
    	$graphe=array();
    	$structures=null;
    	
    	if ($tabRoles[$role]==Utilisateur::ROLE_ADMIN){
    		$structures=$repStruct->getStructureAndStructureDirecteByStructure($this->getUser()->getStructure()->getRoot())->getQuery()->getArrayResult();
    	}elseif ($tabRoles[$role]==Utilisateur::ROLE_ANIMATEUR){
    		$instances=$repInst->animateurQueryBuilder($init)->addSelect('i3.libelle')->getQuery()->getArrayResult();
    		
    	}elseif ($tabRoles[$role]==Utilisateur::ROLE_MANAGER){
			$structures=$repStruct->getStructureAndStructureDirecteByStructure($this->getUser()->getStructure()->getId())->getQuery()->getArrayResult();
    	}elseif($tabRoles[$role]==Utilisateur::ROLE_RAPPORTEUR){
    		$structures=$repStruct->rapporteurQueryBuilder($init)->addSelect('s8.libelle')->getQuery()->getArrayResult();
    	}
    	$form = $this->createForm($this->get('orange.main.statistique_criteria'), null, array('attr'=>array( 'structures'=> $repStruct->getStructureByRole($tabRoles[$role]), 'instances'=>$repInst->getInstanceByRole($tabRoles[$role]) )));
    	$form->handleRequest($request);
    	
    	if ($tabRoles[$role]==Utilisateur::ROLE_CONTRIBUTEUR){
    		$req=$rep->getStatsUserBySemaine($this->getUser(), 2, $form->getData());
    		$data = $this->container->get('orange.main.calcul')->stats($bu, $req);
    		$stats = $this->container->get('orange.main.dataStats')->mappingDataStatsEvo($data, 'semaine');
    	}
    	
    	if(($request->getMethod()=='POST') && $form->getData()){
    		$this->get('session')->set('statistique_criteria', $request->request->get($form->getName()));
    		if(count($form->getData()->instances)>0){
    			$insts=$form->getData()->instances;
	    		$instances=array();
	    		foreach($insts as $k=>$val)
	    			$instances[]=array('id'=>$val->getId(), 'libelle'=>$val->getLibelle());
    		}
    		if($form->getData()->getStructure()!=null){
    				$str=$form->getData()->getStructure()->getId();
    				$structures=$repStruct->getStructureAndStructureDirecteByStructure($str)->getQuery()->getArrayResult();
    	   }
    	}
    	if($tabRoles[$role]==Utilisateur::ROLE_ADMIN || $tabRoles[$role]== Utilisateur::ROLE_RAPPORTEUR || $tabRoles[$role]== Utilisateur::ROLE_MANAGER){
    		$stats = $this->createTableauEvoByType($structures, "structure", $form->getData());
	    	$graphe=$this->createManyGrapheEvo($stats, $structures,'structure');
    	}
    	if($tabRoles[$role]==Utilisateur::ROLE_ANIMATEUR){
    		$stats = $this->createTableauEvoByType($instances, "instance",$form->getData());
    		$graphe=$this->createManyGrapheEvo($stats, $instances,'instance');
    	}
    	
    	if($tabRoles[$role]==Utilisateur::ROLE_PORTEUR){
    		$req=$rep->getStatsUserBySemaine($this->getUser(), 1, $form->getData());
    		$data = $this->container->get('orange.main.calcul')->stats($bu, $req);
    		$stats = $this->container->get('orange.main.dataStats')->mappingDataStatsEvo($data, 'semaine');
    		$graphe=$this->createGrapheEvo($stats);
    	}
    	return array(
    			'form'=>$form->createView(),
    			'role'=>$role,
    			'stats'=>$stats,
    			'graphe'=>$graphe,
    			'statut'=>$this->getStatus(),
    			'nbTaux'=>$this->getNombreTaux(),
    			'semaines'=>$this->getSemaines()
    	);
    }
    
    public function mapIds($data){
    	$array = array();
    	foreach($data as $value){
    		array_push($array, $value['id']);
    	}
    	return $array;
    }
    
    /**
     * @Route("/reporting_instance", name="reporting_instance")
     * @Template()
     *
     */
    public function reportingInstanceAction(Request $request){
    			
    			$em = $this->getDoctrine()->getEntityManager();
    			$queryBuilder =$this->get('session')->get('donnees_reporting_actions_instance' );
    			$query = $em->createQuery($queryBuilder['query']);
    			$query->setParameters($queryBuilder['param']);
    			$idActions = $this->mapIds($query->execute());
    			$actions = $em->getRepository('OrangeMainBundle:Action')->filterExportReporting($idActions);
    			$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
    			$statuts = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
//     			var_dump($query->execute());exit;
    			
    	        $data=$this->get('session')->get('donnees_reporting_instance' );
    			$this->get('session')->set('reporting_export',array('data' => $data['data'], 'statut' => $this->getStatus(), 'tmp' => 0) );
    			$objWriter = $this->get('orange.main.reporting')->reportingInstanceAction($data['data'], $this->getStatus(), $actions, $statuts->getQuery()->execute());
    			$filename = "test.xlsx";
//     			$filename = sprintf("Extraction des statistiques par instance du %s.xlsx", date('d-m-Y à H:i:s'));
    			$filename = sprintf("Extraction des statistiques par instance-du-%s.xlsx", date('d-m-Y'));
    			$objWriter->save($this->web_dir."/upload/reporting/$filename");
    			return $this->redirect($this->getUploadDir().$filename);
    			
    }
    
    /**
     * @Route("/reporting_structure", name="reporting_structure")
     * @Template()
     *
     */
    public function reportingStructureAction(Request $request){
    	$em = $this->getDoctrine()->getEntityManager();
    	$queryBuilder =$this->get('session')->get('donnees_reporting_actions_structure' );
    	$query = $em->createQuery($queryBuilder['query']);
    	$query->setParameters($queryBuilder['param']);
    	$statuts = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
    	$idActions = $this->mapIds($query->execute());
    	$actions = $em->getRepository('OrangeMainBundle:Action')->filterExportReporting($idActions);
    	 $data=$this->get('session')->get('donnees_reporting_structure' );
    			$this->get('session')->set('reporting_export',array('data' => $data['data'], 'statut' => $this->getStatus(), 'tmp' => 0) );
    			$objWriter = $this->get('orange.main.reporting')->reportingStructureAction($data['data'], $this->getStatus(),$actions, $statuts->getQuery()->execute());
//     			$filename = sprintf("Extraction des statistiques par structure du %s.xlsx", date('d-m-Y à H:i:s'));
    			$filename = sprintf("Extraction_des_statistiques_par_structure_du_%s.xlsx", date('d-m-Y'));
    			$objWriter->save($this->web_dir."/upload/reporting/$filename");
    			return $this->redirect($this->getUploadDir().$filename);
    	
    }
    
    /**
     * @Route("/{type}/nouveau_reporting", name="nouveau_reporting")
     * @Method("GET")
     * @Template()
     */
    public function newReportingAction($type)
    {
		if($type == 2){
			$req = $this->get('session')->get('reporting_instance', array());
			$query = $this->get('session')->get('donnees_reporting_actions_instance', array());
			$tp = $req['tp'];
		}else{
			$req = $this->get('session')->get('reporting_structure', array());
			$query = $this->get('session')->get('donnees_reporting_actions_structure', array());
			$tp = $req['tp'];
		}
		$param  = array();
		$parameters = array();
		foreach($query['param'] as $value) {
			if(is_numeric($value->getValue()) || is_array($value->getValue()) ){
				$param[$value->getName()] = $value->getValue();
			}
			else
				$param[$value->getName()] = $value->getValue()->getId();
		}
		
    	foreach($req['param'] as $value) {
    		if(is_numeric($value->getValue()) || is_array($value->getValue()) ){
    			$parameters[$value->getName()] = $value->getValue();
    		}
    		else
    			$parameters[$value->getName()] = $value->getValue()->getId();
    	}
    	$entity = new Reporting();
		$entity->setArrayType($req['arrType']);
    	$entity->setParameter(serialize($parameters));
    	$entity->setRequete($req['req']);
		$entity->setTypeReporting($tp);
		$entity->setParam(serialize($param));
		$entity->setQuery($query['query']);
		
    	$form   = $this->createCreateForm($entity,'Reporting');
    	return array(
    			'entity' => $entity,
    			'tp' => $tp,
    			'form'   => $form->createView(),
    	);
    }
    
    /**
     * Creates a new Action entity.
     *
     * @Route("/creer_reporting_stat", name="creer_reporting_stat")
     * @Method({"POST","GET"})
     * @Template("OrangeMainBundle:Statistique:newReporting.html.twig")
     */
    public function createReportingAction(Request $request)
    {
    	$entity = new Reporting();
    	$form = $this->createCreateForm($entity,'Reporting');
    	$form->handleRequest($request);
    	if ($form->isValid()) {
    		$em = $this->getDoctrine()->getManager();
    		$entity->setUtilisateur($this->getUser());
    		$this->container->get('orange.main.envoi')->generateEnvoi($entity);
    		$em->persist($entity);
    		$em->flush();
    		// envoie de mail pour notifier de la creation du reporting
    		$to = $this->getUser()->getEmail();
    		$result = $this->container->get('orange.main.mailer')->sendNotifReport($to, $entity, $this->getUser());
    		$this->get('session')->getFlashBag()->add('success', array (
    				'title' => 'Notification',
    				'body' => 'le reporting a étè créée avec succes'
    		));
 			return $this->redirect($this->generateUrl('les_reportings'));
    		
    	}
    	return $this->render('OrangeMainBundle:Statistique:newReporting.html.twig',
    			array(
    					'entity' => $entity,
    					'form'   => $form->createView(),
    			), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
	
    public function getSemaines(){
        $semaines=array();
        for($s=1;$s<=date("W");$s++)
            $semaines[$s-1]=$s;
        
        return $semaines;
    }
	
    /*
     * reperetoire de sauvegarde des reporting
     */
    private function getUploadDir() {
    	return $this->getRequest()->getBaseUrl().($this->get('kernel')
    			->getEnvironment()=='prod' ? '' : '/..')."/upload/reporting/";
    }
  
    
    /* autres methodes */
    /**
     * Pour recuperer le nombre de ligne dans les tableaux  
     */
    public function getNombreTaux(){
    	return count($this->getDoctrine()->getRepository('OrangeMainBundle:Formule')->findBy(array('visibilite'=>true, 'bu'=>$this->getUser()->getStructure()->getBuPrincipal())));
    }
    
    public function createManyGrapheStat($stats,$lib,$lib1){
    	$graphe=array();
    	foreach ($stats[$lib] as $key => $values){
    		$graphe[$key]=array();
    		foreach ($values['taux']  as $cle1 => $taux){
    			$graphe[$key][$cle1]=array();
    		}
    	}
    	foreach ($stats[$lib] as $key=>$values){
    		foreach($values[$lib1] as  $cle=>$val){
    			foreach ($val['data'] as $cle1=>$dt){
    				if(isset($graphe[$key][$cle1]))
    					$graphe[$key][$cle1][]=$dt;
    			}
    		}
    		 
    	}
    	return  $graphe;
    }
    public function createGraphe($stats,$graphe){
    	foreach ($stats as $key=>$values){
    		$i=0;
    		foreach ($values['data'] as $cle=>$val){
    			if(isset($graphe[$cle]))
    				$graphe[$cle][]=$val;
    		}
    	}
    	return $graphe;
    }
    public function createManyGrapheEvo($stats,$params,$lib){
    	$graphe=array();
    	foreach ($params as $par){
    		$graphe[$par['libelle']]=array();
    		foreach($stats[$lib][$par['libelle']]['taux'] as $key => $values){
    			$graphe[$par['libelle']][$key]=array();
    		}
    	}
    	foreach ($stats[$lib] as $key=>$values){
    		foreach($values['semaine'] as  $cle=>$val){
    			foreach ($val['data'] as $c=>$v)
    				if(isset($graphe[$key][$c]))
    					$graphe[$key][$c][]=$v;
    		}
    	}
    	return $graphe;
    }
    
    public function createGrapheEvo($stats){
    	$graphe=array();
    	foreach($stats['taux'] as $key=>$value){
    		$graphe[$key]=array();
    	}
    	foreach ($stats['semaine'] as $key=>$values){
    		$i=0;
    		foreach ($values['data'] as $cle=>$val){
    			if(isset($graphe[$cle]))
    				$graphe[$cle][]=$val;
    		}
    	}
    	return  $graphe;
    }
    
    public function createArrSemaine(){
    	$semaines=array(); $index=0;
    	for($i=1;$i<=Date('W');$i++){
    		$semaines[$index]['id']=$i;
    		$semaines[$index]['libelle']=$i;
    		$index++;
    	}
    	return $semaines;
    }
    
    public function getStatus(){
    	$formule=$this->getDoctrine()->getRepository('OrangeMainBundle:Formule')->getTauxStats();
    	$statuts=$this->statuts;
    	if(count($formule)>0)
	    	foreach ($formule as $key=>$form)
	    		$statuts[$form['libelle']]=$form['libelle'];
    	return $statuts;
    		
    }
 /**
     * 
     * @param array $params
     * @param string $type 
     * le type est soit instance ou structure
     */
    public function createTableauEvoByType(&$params, $type,$criteria){
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$bu=$this->getUser()->getStructure()->getBuPrincipal();
    	$semaines=$this->createArrSemaine();
    	$stats=array();
    	switch ($type){
    		case 'instance':
    			$req=$rep->getStatistiqueEvolutiveByInstance($criteria)->getQuery()->getArrayResult();
    			$data = $this->container->get('orange.main.calcul')->stats($bu, $req);
    			$stats = $this->container->get('orange.main.dataStats')->mappingDataStatsCroise($data, 'instance','semaine',$params,$semaines);
    		break;
    		case 'structure':
    			$req=$rep->getStatistiqueEvolutiveByStructure($criteria);
    			$data = $this->container->get('orange.main.calcul')->stats($bu, $req);
    			$stats = $this->container->get('orange.main.dataStats')->mappingDataStatsCroise($data, $type,'semaine',$params,$semaines);
    		break;
    	}
    	return $stats;
    }

}
