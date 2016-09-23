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

class StatistiqueController extends BaseController
{
    
    protected $web_dir = WEB_DIRECTORY;

     protected  $statuts= array(
                        'nbAbandon' => 'Abandon',
                        'nbDemandeAbandon' => "Demande d'abandon",
                        'nbFaiteDelai' => "Faite dans les délais",
                        'nbFaiteHorsDelai' => "Faite hors délai",
                        'nbNonEchue' => "Non échue",
                        'nbSoldeeHorsDelais' => 'Soldée hors délais',
                        'nbSoldeeDansLesDelais' => "Soldée dans les délais",
                        'total' => "Total",
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
        $instancesP=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->porteurQueryBuilder($init)->addSelect('i7.libelle')->distinct()->getQuery()->getArrayResult();
        $instancesC=$this->getDoctrine()->getRepository('OrangeMainBundle:instance')->getInstancesEnConributions($this->getUser()->getId());
        $instancesM=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstanceByStructure($this->getUser()->getStructure()->getId())->getQuery()->getArrayResult();
        $statsP=$rep->getStatPorteeByInstance($this->getUser());
        $statsC=$rep->getStatsContribuesByInstance($criteria);
        $tableauP=$this->createTabStatique($instancesP, $init,$statsP,'libelle',0);
        $tableauC=$this->createTabStatique($instancesC, $init ,$statsC,'libelle',0);
        $tableauM=array();
        if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
            $statsM=$rep->getStatsByOneStructureInstance($this->getUser()->getStructure(),$criteria);
            $tableauM=$this->createTabStatique($instancesM, $init,$statsM,'libelle',0);
            $statsTotalManager=$em->getRepository("OrangeMainBundle:Action")->getTotalStatsByOneStructureInstance($this->getUser()->getStructure(),$criteria);
            $tableauM=$this->createTabResults($instancesM, $statsTotalManager, $tableauM);
            }
    
        $statTotalByInstance=$rep->getTotalPorteeByInstance($this->getUser());
        $tableauP=$this->createTabResults($instancesP, $statTotalByInstance, $tableauP);
        $statTotalContribByInstance=$rep->getTotalContribueByInstance($this->getUser());
        $tableauC=$this->createTabResults($instancesC, $statTotalContribByInstance, $tableauC);
        
        return array(
                'instancesP'=>$instancesP,
                'instancesC'=>$instancesC,
                'instancesM'=>$instancesM,
                'statsP'=>$tableauP,
                'statsC'=>$tableauC,
                'statsM'=>$tableauM
                
        );
    }
   
    /**
     * @Route("/tableauStatUtilisateurEv", name="tableauStatUtilisateurEv")
     *  @Method("GET")
     * @Template()
     * 
     */
    public function tableauStatistiqueEvoUtilisateurAction(){
        $init=array();
        $instancesP=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->porteurQueryBuilder($init)->addSelect('i7.libelle')->distinct()   ->getQuery()->getArrayResult();
        $instancesC=$this->getDoctrine()->getRepository('OrangeMainBundle:instance')->getInstancesEnConributions($this->getUser()->getId());
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
        $stats=$rep->getStatisEvolutiveUtilisateur();
        $semaines=$this->getSemaines();     $tableauP=array();      $tableauC=array();      $tableauM=array();
        $tableauP=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableauP,$init, 0,false);
        $tableauC=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableauC, $init,0,false);
        $tableauM=array();
        if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
                $stats1=$rep->getStatistiqueEvolutiveByOneStructure($this->getUser()->getStructure());
                $tableauM=$this->customizeTableauStatsEvolutive($semaines, $stats1, $tableauM, $init,0,false);
        }
        return array(
                'instancesP'=>$instancesP,
                'instancesC'=>$instancesC,
                'semaines'=>$semaines,
                'tableauP'=>$tableauP,
                'tableauM'=>$tableauM,
                'tableauC'=>$tableauC               
        );
    }
    
    /**
     * @Route("/tableauStatistiqueAdmin", name="tableauStatistiqueAdmin")
     * @Method("GET")
     * @Template()
     */
    public function tableauStatistiqueAdminAction(){
        $init=array();
        $criteria=null;
        $bu=$this->getUser()->getStructure()->getBuPrincipal();
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstancesAdmin();
        $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionsByBu()->getQuery()->getArrayResult();
        $statsStruct=$rep->getStatsByStructure($criteria);
        $statsInst=$rep->getStatsByInstance($criteria);
        $tableau1=$this->createTabStatique($instances, $init, $statsInst,'libelle',0);
        $tableau=$this->createTabStatique($structures,$init, $statsStruct,'libelle',0);
        $em=$this->getDoctrine()->getEntityManager();
        
        $statTotalByStructure=$rep->getTotalByStructure($criteria);
        $tableau=$this->createTabResults($structures, $statTotalByStructure, $tableau);
        
        $statTotalByInstance=$rep->getTotalByInstance($criteria);
        $tableau1=$this->createTabResults($structures, $statTotalByInstance, $tableau1);
        
        $users=$this->getDoctrine()->getRepository('OrangeMainBundle:Utilisateur')->getUtilisateurByStructure()->getQuery()->getArrayResult();
        $usersActif=$this->getDoctrine()->getRepository('OrangeMainBundle:Utilisateur')->getUtilisateurActifByStructure()->getQuery()->getArrayResult();
        $tableau=$this->tabResultsAdmin($structures, $users, $usersActif, $tableau);
        return array(
                'structures'=>$structures,
                'stats'=>$tableau,
                'stats1'=>$tableau1,
                'instances'=>$instances,
        );
    }
    
    /**
     * @Route("/tableauStatistiqueEvoAdmin", name="tableauStatistiqueEvoAdmin")
     *  @Method("GET")
     * @Template()
     */
    public function tableauStatistiqueEvoAdminAction(){
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
        $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionsByBu()->getQuery()->getArrayResult();
        $stats=$rep->getStatistiqueEvolutiveByStructure();
        $tableau=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableau, $structures,1,true);
        return array(
                'semaines'=>$this->getSemaines(),
                'tableau'=>$tableau
        );
    }
    
    /**
     *
     * @Route("/tableauStatistiqueRapporteur/{id}", name="tableauStatistiqueRapporteur")
     * @Template()
     */
    public function tableauStatistiqueRapporteurAction($id, Request $request){
        $init=array();
        $form = $this->createForm(new StatistiqueActionCriteria());
        $set = $this->get('session')->set('statistique_action_criteria', new Request());
        $criteria=null;
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
        $em = $this->getDoctrine()->getManager();
        
        if($request->getMethod()=='POST'){
            $this->modifyRequestForForm($request, $this->get('session')->get('statistique_action_criteria'), $form);
            $criteria = $form->getData();
        }
    
        $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionAndDepartmentByStructure($id)->getQuery()->getArrayResult();
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstanceRapporteur();
        
        $statsStruct=$rep->getStatsByStructure($criteria);
        $tableauS=$this->createTabStatique($structures, $init ,$statsStruct,'libelle',1);
        
        $statsInst=$rep->getStatsByInstance($criteria);
        $tableauI=$this->createTabStatique($instances,$init,$statsInst,'libelle',0);
        
        $i=0;
        $results['Total']=array();
        $results['Taux de réalisation globale']=array();
        $results['Taux de réalisation dans les delais']=array();
        foreach ($structures as $struct){
            $total=$results['Total'][$i] = count($em->getRepository("OrangeMainBundle:Action")->getActionByStructure($struct['id']));
            $results['Taux de réalisation globale'][$i]=($total==0)?0:(number_format((($tableauS['Soldee'][$i]/$total)*100),1));
            $results['Taux de réalisation dans les delais'][$i]=($total==0)?0:(number_format((($tableauS['Soldee dans les delai'][$i]/$total)*100),1));
            $i++;
        }
        $i=0;
        $results1['Total']=array();
        $results1['Taux de réalisation globale']=array();
        $results1['Taux de réalisation dans les delais']=array();
        foreach($instances as $inst){
            $total1=$results1['Total'][$i] = count($em->getRepository("OrangeMainBundle:Action")->findBy(array('instance' => $inst)));
            $results1['Taux de réalisation globale'][$i]=($total1==0)?0:(number_format((($tableauI['Soldee'][$i]/$total1)*100),1));
            $results1['Taux de réalisation dans les delais'][$i]=($total1==0)?0:(number_format((($tableauI['Soldee dans les delai'][$i]/$total1)*100),1));
            $i++;
        }
        
        return array(
                'structures'=>$structures,
                'instances'=>$instances,
                'tableauS'=>$tableauS,
                'tableauI'=>$tableauI,
                'results'=>$results,
                'results1'=>$results1
        );
    }
    
    /**
     *
     * @Route("/tableauStatistiqueEvoRapporteur/{structure_id}", name="tableauStatistiqueEvoRapporteur")
     * @Template()
     */
    public function tableauStatistiqueEvoRapporteurAction($structure_id){
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
        $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getStructureAndStructureDirecteByStructure($structure_id)->getQuery()->getArrayResult();
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstanceByStructure($structure_id);
        $stats=$rep->getStatistiqueEvolutiveByStructure();
        $tableau=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableau, $structures,1,false);
        return array(
                'semaines'=>$this->getSemaines(),
                'tableau'=>$tableau
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
        $statutCloture      = $em->getRepository("OrangeMainBundle:Statut")->findOneByCode(Statut::SIGNALISATION_CLOTURE);
        $statutEfficace     = $em->getRepository("OrangeMainBundle:Statut")->findOneByCode(Statut::SIGNALISATION_TRAITE_EFFICACEMENT);
        $statutEnCours      = $em->getRepository("OrangeMainBundle:Statut")->findOneByCode(Statut::SIGNALISATION_VALIDER);
        $statutNonEfficace  = $em->getRepository("OrangeMainBundle:Statut")->findOneByCode(Statut::SIGNALISATION_TRAITE_NON_EFFICACEMENT);
    
        $total          = intval($em->getRepository("OrangeMainBundle:Signalisation")->totalSignalisation()['total']);
        $cloture        = count($em->getRepository("OrangeMainBundle:SignalisationStatut")->findBy(array('statut' => $statutCloture->getId())));
        $efficace       = count($em->getRepository("OrangeMainBundle:SignalisationStatut")->findBy(array('statut' => $statutEfficace->getId())));
        $enCours        = count($em->getRepository("OrangeMainBundle:SignalisationStatut")->findBy(array('statut' => $statutEnCours->getId())));
        $nonEfficace    = count($em->getRepository("OrangeMainBundle:SignalisationStatut")->findBy(array('statut' => $statutNonEfficace->getId())));
    
        $statistiqueTableauBord["total"] = $total;      // Total des signalisations prises en charge, les autres ne sont pas consid��r�es dans le syst�me
        $statistiqueTableauBord["cloture"] = $cloture;
        $statistiqueTableauBord["efficace"] = $efficace;
        $statistiqueTableauBord["en_cours"] = $enCours;
        $statistiqueTableauBord["non_efficace"] = $nonEfficace;
    
        $statistiqueTableauBord["% cloture"] = ($total==0)?"0%":number_format((($cloture/$total)*100),2)."%";
        $statistiqueTableauBord["% efficace"] = ($total==0)?"0%":number_format((($efficace/$total)*100),2)."%";
        $statistiqueTableauBord["% en_cours"] = ($total==0)?"0%":number_format((($enCours/$total)*100),2)."%";
        $statistiqueTableauBord["% non_efficace"] = ($total==0)?"0%":number_format((($nonEfficace/$total)*100),2)."%";
        return array('statistiqueTableauBord' => $statistiqueTableauBord);
    }
    
    /**
     *
     * @Route("/tableauStatistiqueAnimateur", name="tableauStatistiqueAnimateur")
     * @Template()
     */
    public function tableauStatistiqueAnimateurAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->filter()->getQuery()->getArrayResult();
        $form = $this->createForm(new StatistiqueActionCriteria());
        $set = $this->get('session')->set('statistique_action_criteria', new Request());
        
        $criteria=null;
        
        if($request->getMethod()=='POST'){  
            $this->modifyRequestForForm($request, $this->get('session')->get('statistique_action_criteria'), $form);
            $criteria = $form->getData();
            //return $this->redirect($this->generateUrl('statsGeneraleAnimateur'));
       }
       
        $init=array();
        $stats = $em->getRepository('OrangeMainBundle:Action')->getStatsByInstance($criteria);
        $tableau=$this->createTabStatique($instances, $init,$stats,'libelle',0);
        
        $statTotalByInstance=$em->getRepository('OrangeMainBundle:Action')->getTotalByInstance($criteria);
        $tableau=$this->createTabResults($instances, $statTotalByInstance, $tableau);
        
        return array('form'   => $form->createView(),
                     'instances'=>$instances,
                     'tableau' => $tableau,
        );
        
    }
    
    
    /**
     *
     * @Route("/tableauStatistiqueEvoAnimateur", name="tableauStatistiqueEvoAnimateur")
     * @Template()
     */
    public function tableauStatistiqueEvoAnimateurAction(){
        $criteria=null;
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->filter()->getQuery()->getArrayResult();
        $stats=$rep->getStatistiqueEvolutiveByInstance($criteria);
        $tableau=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableau, $instances, 1,false);
        $semaines=array();
        $tableau=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableau, $instances, 1,false);
        $graphe=array();
        foreach($instances as $inst){
            $graphe[$inst['libelle']]['Total']=&$tableau[$inst['libelle']]['Total'];
            $graphe[$inst['libelle']]['Taux de réalisation globale']=&$tableau[$inst['libelle']]['Taux de réalisation globale'];
            $graphe[$inst['libelle']]['Taux de réalisation dans les delais']=&$tableau[$inst['libelle']]['Taux de réalisation dans les delais'];
        }
        return array(
                'semaines'=>$this->getSemaines(),
                'tableau'=>$tableau,
                'graphe'=>$graphe
        );
    }
    
    /**
     * @Route("/statsGeneraleUtilisateur", name="statsGeneraleUtilisateur")
     * @Template()
     *
     */
    public function statsGeneraleUtilisateurAction(){
        $init=array();
        $criteria=null;
        $em=$this->getDoctrine()->getManager();
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
        $instancesP=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->porteurQueryBuilder($init)->addSelect('i7.libelle')->distinct()->getQuery()->getArrayResult();
        $instancesC=$this->getDoctrine()->getRepository('OrangeMainBundle:instance')->getInstancesEnConributions($this->getUser()->getId());
        $instancesM=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstanceByStructure($this->getUser()->getStructure()->getId())->getQuery()->getArrayResult();
        $statsP=$rep->getStatPorteeByInstance($this->getUser());
        $statsC=$rep->getStatsContribuesByInstance($criteria);
        $tableauP=$this->createTabStatique($instancesP, $init,$statsP,'libelle',0);
        $tableauC=$this->createTabStatique($instancesC, $init ,$statsC,'libelle',0);
        $tableauM=array();
        if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
            $statsM=$rep->getStatsByOneStructureInstance($this->getUser()->getStructure(),$criteria);
            $tableauM=$this->createTabStatique($instancesM, $init,$statsM,'libelle',0);
            $statsTotalManager=$em->getRepository("OrangeMainBundle:Action")->getTotalStatsByOneStructureInstance($this->getUser()->getStructure(),$criteria);
            $tableauM=$this->createTabResults($instancesM, $statsTotalManager, $tableauM);
        }
        $statTotalByInstance=$rep->getTotalPorteeByInstance($this->getUser());
        $tableauP=$this->createTabResults($instancesP, $statTotalByInstance, $tableauP);
        $statTotalContribByInstance=$rep->getTotalContribueByInstance($this->getUser());
        $tableauC=$this->createTabResults($instancesP, $statTotalContribByInstance, $tableauC);
        
        
        $grapheP=$this->createGraphe( $tableauP);
        $grapheC=$this->createGraphe($tableauC);
        $grapheM=$this->createGraphe($tableauM);
        $stats=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique')->getStatisEvolutiveUtilisateur();
        $instancesP=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')
        ->porteurQueryBuilder($init)->addSelect('i7.libelle')->distinct()
        ->getQuery()->getArrayResult();
         
        $instancesC=$this->getDoctrine()->getRepository('OrangeMainBundle:instance')
        ->getInstancesEnConributions($this->getUser()->getId());
        $tableauEvP=array();
        $tableauEvC=array();
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
        $semaines=$this->getSemaines();
        $stats=$rep->getStatisEvolutiveUtilisateur();
        $tableauEvP=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableauEvP,$init, 0,false);
        $tableauEvC=$this->customizeTableauStatsEvolutive($semaines, $stats, $tableauEvC ,$init,0,false);
        
        $tableauEvM=array();
        if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
            $stats1=$rep->getStatistiqueEvolutiveByOneStructure($this->getUser()->getStructure());
            $tableauEvM=$this->customizeTableauStatsEvolutive($semaines, $stats1, $tableauEvM, $init,0,false);
        }
        $grapheEvP['Taux de réalisation globale']=&$tableauEvP['']['Taux de réalisation globale'];
        $grapheEvP['Taux de réalisation dans les delais']=&$tableauEvP['']['Taux de réalisation dans les delais'];
        
        $grapheEvC['Taux de réalisation globale']=&$tableauEvC['']['Taux de réalisation globale'];
        $grapheEvC['Taux de réalisation dans les delais']=&$tableauEvC['']['Taux de réalisation dans les delais'];
        
        $grapheEvM['Taux de réalisation globale']=&$tableauEvM['']['Taux de réalisation globale'];
        $grapheEvM['Taux de réalisation dans les delais']=&$tableauEvM['']['Taux de réalisation dans les delais'];
        return array(
                'instancesP'=>$instancesP,
                'instancesC'=>$instancesC,
                'instancesM'=>$instancesM,
                'tableauEvP'=>$tableauEvP,
                'tableauEvC'=>$tableauEvC,
                'tableauEvM'=>$tableauEvM,
                'grapheEvP'=>$grapheEvP,
                'grapheEvC'=>$grapheEvC,
                'grapheEvM'=>$grapheEvM,
                'statsP'=>$tableauP,
                'statsC'=>$tableauC,
                'statsM'=>$tableauM,
                'grapheP'=>$grapheP,
                'grapheC'=>$grapheC,
                'grapheM'=>$grapheM,
                'semaines'=>$this->getSemaines()
                
        );
    
    }
    
    /**
     * @Route("/statsGeneraleAnimateur", name="statsGeneraleAnimateur")
     * @Template()
     * 
     */
    public function statsGeneraleAnimateurAction(Request $request){
        $init=array();
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->filter()->getQuery()->getArrayResult();
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
        $form = $this->createForm(new StatistiqueActionCriteria());
        $set = $this->get('session')->set('statistique_action_criteria', new Request());
        $em = $this->getDoctrine()->getManager();
        $criteria=null;
        if($request->getMethod()=='POST'){
            $this->modifyRequestForForm($request, $this->get('session')->get('statistique_action_criteria'), $form);
            $criteria = $form->getData();
        }
        $stats = $em->getRepository('OrangeMainBundle:Action')->getStatsByInstance($criteria);
      //  $stats1=$rep->getStatistiqueEvolutiveByInstance($criteria)->getQuery()->getArrayResult(); 
        $tableauStatique=$this->createTabStatique($instances, $init,$stats,'libelle',0);
        $statTotalByInstance=$em->getRepository('OrangeMainBundle:Action')->getTotalByInstance($criteria);
        $tableauStatique=$this->createTabResults($instances, $statTotalByInstance, $tableauStatique);
        $tableauEvolutive=array();
        $semaines=array();
       // $tableauEvolutive=$this->customizeTableauStatsEvolutive($semaines, $stats1, $tableauEvolutive, $instances, 1, false);

        $stats1=$rep->getStatistiqueEvolutiveByInstance($criteria)->getQuery()->getArrayResult();
        $tableauEvolutive=$this->customizeTableauStatsEvolutive($semaines, $stats1, $tableauEvolutive, $instances, 1,false);
        
        foreach($instances as $inst){
            $graphe[$inst['libelle']]['Taux de réalisation globale']=&$tableauEvolutive[$inst['libelle']]['Taux de réalisation globale'];
            $graphe[$inst['libelle']]['Taux de réalisation dans les delais']=&$tableauEvolutive[$inst['libelle']]['Taux de réalisation dans les delais'];
        }
        $results['Taux de réalisation globale']=&$tableauStatique['Taux de réalisation globale'];
        $results['Taux de réalisation dans les delais']=&$tableauStatique['Taux de réalisation dans les delais'];
        
        return array('form'   => $form->createView(),
                     'instances'=>$instances,
                     'tableauStatique' => $tableauStatique,
                     'tableau' => $tableauEvolutive,
                     'semaines'=>$semaines,
                     'results'=>$results,
                     'graphe'=>$graphe
        );
        
    }
    /**
     * @Route("/statsGeneraleAdmin", name="statsGeneraleAdmin")
     * @Template()
     *
     */
    public function statsGeneraleAdminAction(Request $request){
        $init=array();
        $structure_choisie=0;
        $form1 = $this->createForm(new StatistiqueActionCriteria());
        $set = $this->get('session')->set('statistique_action_criteria', new Request());
        $criteria=null;
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstancesAdmin();
        $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionsByBu()->getQuery()->getArrayResult();
        $allStructures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->findAll();
        $em = $this->getDoctrine()->getManager();
        if($request->getMethod()=='POST'){
            $this->get('session')->set('statistique_action_criteria', $request->request->get($form1->getName()));
        }
        if(isset($_POST['struct']) && $_POST['struct']!=-1){
                $structure_choisie=$_POST['struct'];
                $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getStructureAndStructureDirecteByStructure($structure_choisie)->getQuery()->getArrayResult();
        }
        $this->modifyRequestForForm($request, $this->get('session')->get('statistique_action_criteria'), $form1);
        $criteria = $form1->getData();
        $statsStruct=$rep->getStatsByStructureInstance($criteria);
        $statsInst=$rep->getStatsByInstance($criteria);
        $tableau1=$this->createTabStatique($instances, $init,$statsInst,'libelle',0);
        $tableau=$this->createTabStatique($instances,$structures, $statsStruct,'libelle',1);
        $statsTotal=$rep->getTotalByStructureInstance($criteria);
        $results=array();
        $grapheStatique=array();    
        foreach ($structures as $key=>$struct){
            $i=0;
            foreach ($instances as $k=>$inst){
                $aide=0;
                foreach ($statsTotal as $cle=>$s){
                    if($s['libelle']==$struct['libelle'] && $s['inst']==$inst['libelle']){
                        $total=$tableau[$struct['libelle']]['Total'][$i] = $s['total'];
                        $tableau[$struct['libelle']]['Taux de réalisation globale'][$i]=intval(($total==0)?0:(number_format((($tableau[$struct['libelle']]['Soldée'][$i]/$total)*100),1)));
                        $tableau[$struct['libelle']]['Taux de réalisation dans les delais'][$i]=intval(($total==0)?0:(number_format((($tableau[$struct['libelle']]['Soldée dans les délais'][$i]/$total)*100),1)));
                        $aide=1;
                        $i++; 
                    }
                }
                if($aide==0){
                    $tableau[$struct['libelle']]['Total'][$i]=0;
                    $tableau[$struct['libelle']]['Taux de réalisation globale'][$i]=0;
                    $tableau[$struct['libelle']]['Taux de réalisation dans les delais'][$i]=0;
                    $i++; 
                }
            }
            $grapheStatique[$struct['libelle']]['Taux de réalisation globale']=&$tableau[$struct['libelle']]['Taux de réalisation globale'];
            $grapheStatique[$struct['libelle']]['Taux de réalisation dans les delais']=&$tableau[$struct['libelle']]['Taux de réalisation dans les delais'];
        }
        
        $results1=array();
        $results=array();
        $graphe=array();
        $stats=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique')->getStatistiqueEvolutiveByStructure();
        
        $i=0;
        $results1['Total']=array();
        $results1['Taux de réalisation globale']=array();
        $results1['Taux de réalisation dans les delais']=array();
        foreach($instances as $inst){
            $total1=$results1['Total'][$i] = count($em->getRepository("OrangeMainBundle:Action")->findBy(array('instance' => $inst)));
            $results1['Taux de réalisation globale'][$i]=($total1==0)?0:(number_format((($tableau1['Soldée'][$i]/$total1)*100),1));
            $results1['Taux de réalisation dans les delais'][$i]=($total1==0)?0:(number_format((($tableau1['Soldée dans les délais'][$i]/$total1)*100),1));
            $i++;
        }
        $graphe=array();
        $semaines=array();
        $tableauEv=array();
        $i=0;
        $lib='';
        foreach($structures as $str){
            $i=0;
            for($s=1;$s<date("W");$s++){
                $aide=0; $semaines[$s-1]=$s;
                foreach ($stats as $sta){
                    if($s==$sta['semaine'] && $str['id']==$sta['id']){
                        $this->customizeTableauStats($sta, $str['libelle'], $tableauEv, $i, false);
                        $totale=$sta['total'];
                        $graphe[$str['libelle']]['Taux de réalisation globale'][$i]=($totale==0)?0:(number_format((($sta['nbSoldee']/$totale)*100),2));
                        $graphe[$str['libelle']]['Taux de réalisation dans les delais'][$i]=($totale==0)?0:(number_format((($sta['nbSoldeeDansLesDelais']/$totale)*100),2));
                        $aide=1;
                        $i++;
                    }
                }
        
                if($aide==0){
                    $this->customizeTableauStats($sta, $str['libelle'], $tableauEv, $i, true);
                    $graphe[$str['libelle']]['Taux de réalisation globale'][$i]=0;
                    $graphe[$str['libelle']]['Taux de réalisation dans les delais'][$i]=0;
                    $i++;
                }
            }
        
        }
         
        return array(
                'structures'=>$structures,
                'stats'=>$tableau,
                'stats1'=>$tableau1,
                'tableauEv'=>$tableauEv,
                'instances'=>$instances,
                'form1'=>$form1->createView(),
                'results'=>$results,
                'results1'=>$results1,
                'graphe'=>$graphe,
                'grapheStatique'=>$grapheStatique,
                'semaines'=>$this->getSemaines(),
                'allStructures'=>$allStructures,
                'structure_choisie'=>$structure_choisie
        );
    }
    
    
    /**
    * @Route("/statsGeneraleRapporteur/{id}", name="statsGeneraleRapporteur")
    * @Template()
    */
    public function  statsGeneraleRapporteurAction($id,Request $request){
        $init=array();$criteria=null;
        $form = $this->createForm(new StatistiqueActionCriteria());
        $set = $this->get('session')->set('statistique_action_criteria', new Request());
        $rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Action');
        $em = $this->getDoctrine()->getManager();
        
        if($request->getMethod()=='POST'){
            $this->get('session')->set('statistique_action_criteria', $request->request->get($form->getName()));
        }
        $this->modifyRequestForForm($request, $this->get('session')->get('statistique_action_criteria'), $form);
        $criteria = $form->getData();
        $structuresRapp=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getSousStructureAndStructureRapporteur()->getQuery()->getArrayResult();
        $structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getStructureAndStructureDirecteByStructure($id)->getQuery()->getArrayResult();
        $instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstanceByStructure($id)->getQuery()->getArrayResult();
        
        $statsStruct=$rep->getStatsByStructureInstance($criteria);
        $tableau=$this->createTabStatique($instances,$structures, $statsStruct,'libelle',1);
    
        $statsTotal=$em->getRepository("OrangeMainBundle:Action")->getTotalByStructureInstance($criteria);
        $grapheStatique=array();
        foreach ($structures as $key=>$struct){
            $i=0;
            foreach ($instances as $k=>$inst){
                $aide=0;
                foreach ($statsTotal as $cle=>$s){
                    if($s['libelle']==$struct['libelle'] && $s['inst']==$inst['libelle']){
                        $total=$tableau[$struct['libelle']]['Total'][$i] = $s['total'];
                        $tableau[$struct['libelle']]['Taux de réalisation globale'][$i]=intval(($total==0)?0:(number_format((($tableau[$struct['libelle']]['Soldee'][$i]/$total)*100),1)));
                        $tableau[$struct['libelle']]['Taux de réalisation dans les delais'][$i]=intval(($total==0)?0:(number_format((($tableau[$struct['libelle']]['Soldee dans les delai'][$i]/$total)*100),1)));
                        $aide=1;
                        $i++;
                    }
                }
                if($aide==0){
                    $tableau[$struct['libelle']]['Total'][$i]=0;
                    $tableau[$struct['libelle']]['Taux de réalisation globale'][$i]=0;
                    $tableau[$struct['libelle']]['Taux de réalisation dans les delais'][$i]=0;
                    $i++;
                }
            }
            $grapheStatique[$struct['libelle']]['Taux de réalisation globale']=&$tableau[$struct['libelle']]['Taux de réalisation globale'];
            $grapheStatique[$struct['libelle']]['Taux de réalisation dans les delais']=&$tableau[$struct['libelle']]['Taux de réalisation dans les delais'];
        }
        

        $stats=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique')->getStatistiqueEvolutiveByStructure();
        $graphe=array();
        $semaines=array();
        $i=0;
        $lib='';
        
        foreach($structures as $str){
            $i=0;
            for($s=1;$s<date("W");$s++){
                $aide=0; $semaines[$s-1]=$s;
                foreach ($stats as $sta){
                    if($s==$sta['semaine'] && $str['id']==$sta['id']){
                        $totale=$sta['total'];
                        $graphe[$str['libelle']]['Taux de réalisation globale'][$i]=($totale==0)?0:(number_format((($sta['nbSoldee']/$totale)*100),2));
                        $graphe[$str['libelle']]['Taux de réalisation dans les delais'][$i]=($totale==0)?0:(number_format((($sta['nbSoldeeDansLesDelais']/$totale)*100),2));
                        $aide=1;
                        $i++;
                    }
                }
                 
                if($aide==0){
                    $graphe[$str['libelle']]['Taux de réalisation globale'][$i]=0;
                    $graphe[$str['libelle']]['Taux de réalisation dans les delais'][$i]=0;
                    $i++;
                }
            }
             
        }
        
        
        //var_dump($tableau1);exit;
        return array(
                'structures'=>$structures,
                'structuresRapp'=>$structuresRapp,
                'instances'=>$instances,
                'tableau'=>$tableau,
                'grapheStatique'=>$grapheStatique,
                'form'=>$form->createView(),
                'graphe'=>$graphe,
                'semaines'=>$semaines,
                'id'=>$id
        );
         
    }
    
    public function createTabStatique(&$params, &$params1 ,&$stats,$lib,$type){
        $status=array(
                'Abandonnée'=>Statut::ACTION_ABANDONE,
                'Demande Abandon'=>Statut::ACTION_DEMANDE_ABANDON,
                'Echue non Soldée'=>Statut::ACTION_ECHUE_NON_SOLDEE,
                'Non échue'=>Statut:: ACTION_TRAITEMENT,
                'Faite'=>Statut::ACTION_DEMANDE_SOLDE,
                'Soldée'=>Statut::ACTION_EFFICACE,
                'Soldée dans les délais'=>Statut::ACTION_SOLDEE_DELAI
        );
        $tableau=array();
        if($type==0){
                foreach ($status as $key=>$state){
                    $i=0;
                    foreach ($params as $par){
                        $aide=0;
                        foreach ($stats as $st){
                            if($st['libelle']==$par[$lib] && $st['etatCourant']==$state){
                                $tableau[$key][$i]=$st['total'];
                                $aide=1;
                                $i++;
                            }
                        }
                        if($aide==0){
                            $tableau[$key][$i]=0;
                            $i++;
                        }
                    }
                }
        }else{
            foreach ($status as $key=>$state){
                $j=0;
                foreach ($params1 as $cle => $par1){
                    $i=0;
                    foreach ($params as $par){
                        $aide=0;
                        foreach ($stats as $st){
                            if($st['libelle']==$par1['libelle'] && $st['inst']==$par[$lib] && $st['etatCourant']==$state){
                            
                                $tableau[$par1['libelle']][$key][$i]=$st['total'];
                                $aide=1;
                                $i++;
                            }
                        }
                        if($aide==0){
                            $tableau[$par1['libelle']][$key][$i]=0;
                            $i++;
                        }
                    }
                    $j++;
                }
        }
        }
        return $tableau;
    }
    
    
    public function customizeTableauStats(&$val,$lib, &$tab, $index,$isNull){
        if( $isNull==false){
            $tab[$lib]['Abandonnée'][$index]=$val['nbAbandon'];
            $tab[$lib]['Demande Abandon'][$index]=$val['nbDemandeAbandon'];
            $tab[$lib]['Echue non Soldee'][$index]=$val['nbEchueNonSoldee'];
            $tab[$lib]['Non Echue'][$index]=$val['nbAbandon'];
            $tab[$lib]['Faite'][$index]=$val['nbFaite'];
            $tab[$lib]['Soldee'][$index]=$val['nbSoldee'];
            $tab[$lib]['Soldee dans les delais'][$index]=$val['nbSoldeeDansLesDelais'];
            $tab[$lib]['Total'][$index]=$val['total'];
            $tab[$lib]['Taux de réalisation globale'][$index]=($val['total']==0)?0:(number_format((($val['nbSoldee']/$val['total'])*100),2));
            $tab[$lib]['Taux de réalisation dans les delais'][$index]=($val['total']==0)?0:(number_format((($val['nbSoldeeDansLesDelais']/$val['total'])*100),2));
        } else{
            $tab[$lib]['Abandonnée'][$index]=0;
            $tab[$lib]['Demande Abandon'][$index]=0;
            $tab[$lib]['Echue non Soldee'][$index]=0;
            $tab[$lib]['Non Echue'][$index]=0;
            $tab[$lib]['Faite'][$index]=0;
            $tab[$lib]['Soldee'][$index]=0;
            $tab[$lib]['Soldee dans les delais'][$index]=0;
            $tab[$lib]['Total'][$index]=0;
            $tab[$lib]['Taux de réalisation globale'][$index]=0;
            $tab[$lib]['Taux de réalisation dans les delais'][$index]=0;
        }
    }
   
    public function customizeTableauStatsEvolutive(&$semaines,&$stats,&$tableau,&$params,$type,$test){
        $i=0;
        if($type==0){
            for($s=1;$s<date("W");$s++){
                $semaines[$s-1]=$s;
                $aide=0;
                foreach($stats as $val){
                    if($s==$val['semaine']){
                            $this->customizeTableauStats($val, '', $tableau, $i, false);
                            $aide=1;
                            $i++;
                    }
                }
                if($aide==0){
                    $this->customizeTableauStats($val, '', $tableau, $i, true);
                    $i++;
                }
            
            }
        }else{
            foreach($params as $par){
                $i=0;
                for($s=1;$s<date("W");$s++){
                    $aide=0; $semaines[$s-1]=$s;
                    foreach ($stats as $sta){
                        if($s==$sta['semaine'] && $par['id']==$sta['id']){
                            $this->customizeTableauStats($sta, $par['libelle'], $tableau, $i, false);
                            if($this->getUser()->hasRole(Utilisateur::ROLE_ADMIN) && $test==true){
                                    $tableau[$par['libelle']]['Nombre d \'utilisateurs'][$i]=$sta['nbUtilisateur'];
                                    $tableau[$par['libelle']]['Nombre moyens d \'actions par utilisateur'][$i]=($sta['nbUtilisateur']==0)?0:(int)($sta['total']/$sta['nbUtilisateur']);
                                    $tableau[$par['libelle']]['Nombre d \'utilisateurs actifs'][$i]=$sta['nbUtilisateurActif'];
                                    $tableau[$par['libelle']]['Nombre moyens d�actions par utilisateur actif'][$i]=($sta['nbUtilisateurActif']==0)?0:(int)($sta['total']/$sta['nbUtilisateurActif']);
                            }
                            $aide=1;
                            $i++;
                        }
                    }
            
                    if($aide==0){
                        $this->customizeTableauStats($sta, $par['libelle'], $tableau, $i, true);
                        if($this->getUser()->hasRole(Utilisateur::ROLE_ADMIN) && $test==true){
                            $tableau[$par['libelle']]['Nombre d \'utilisateurs'][$i]=0;
                            $tableau[$par['libelle']]['Nombre moyens d \'actions par utilisateur'][$i]=0;
                            $tableau[$par['libelle']]['Nombre d \'utilisateurs actifs'][$i]=0;
                            $tableau[$par['libelle']]['Nombre moyens d�actions par utilisateur actif'][$i]=0;
                        }
                        $i++;
                    }
                }
            
            }
            
        }
        return $tableau;
        
    }
    public function getSemaines(){
        $semaines=array();
        for($s=1;$s<date("W");$s++)
            $semaines[$s-1]=$s;
        
        return $semaines;
    }
    public function createTabResults(&$params,&$stats,&$tableau){
        $i=0;
        if($tableau){
        foreach ($params as $par){
            $aide=0;
            foreach ($stats as $key=> $stat){
                if($stat['libelle']==$par['libelle']){
                    $total=$tableau['Total'][$i]=$stat['total'];
                    $tableau['Taux de réalisation globale'][$i]=($total==0)?0:(number_format((($tableau['Soldée'][$i]/$total)*100),1));
                    $tableau['Taux de réalisation dans les délais'][$i]=($total==0)?0:(number_format((($tableau['Soldée dans les délais'][$i]/$total)*100),1));
                    $i++;
                    $aide=1;
                }
            }
            if($aide==0){
                $tableau['Total'][$i]=0;
                $tableau['Taux de réalisation globale'][$i]=0;
                $tableau['Taux de réalisation dans les délais'][$i]=0;
                $i++;
            }
        }
        }
        return $tableau;
    }
    public function tabResultsAdmin(&$params,&$user,&$userActif,&$tableau){
        $i=0; $j=0;
        foreach ($params as $par){
            $aide=0;
            foreach ($user as $key=> $stat){
                if($stat['libelle']==$par['libelle']){
                    $tot=$tableau['Nombre d \'utilisateurs'][$i]=$stat['usr'];
                    $tableau['Nombre d \'action moyen par utilisateur'][$i]=($tot==0)?0:(int)($tableau['Total'][$i]/$tot);
                    $i++;
                    $aide=1;
                }
            }
            if($aide==0){
                $tableau['Nombre d \'utilisateurs'][$i]=0;
                $tableau['Nombre d \'action moyen par utilisateur'][$i]=0;
                $i++;
            }
        }
        $i=0;
        foreach ($params as $par){
        $aide=0;
        foreach ($userActif as $key=> $stat){
            if($stat['libelle']==$par['libelle']){
                $tot=$tableau['Nombre d \'utilisateurs actifs'][$i]=$stat['usr'];
                $tableau['Nombre d \'action moyen par utilisateur actif'][$i]=($tot==0)?0:(int)($tableau['Total'][$i]/$tot);
                $i++;
                $aide=1;
            }
        }
        if($aide==0){
            $tableau['Nombre d \'utilisateurs actifs'][$i]=0;
            $tableau['Nombre d \'action moyen par utilisateur actif'][$i]=0;
            $i++;
        }
        }
        return $tableau;
    }
    public function createGraphe(&$tableau){
            $graphe['Taux de réalisation globale']=&$tableau['Taux de réalisation globale'];
            $graphe['Taux de réalisation dans les délais']=&$tableau['Taux de réalisation dans les delais'];
            return $graphe;
            
    }
    
     /**
     * @Route("/reporting_instance", name="reporting_instance")
     * @Template()
     *
     */
    public function reportingInstanceAction(Request $request){
    	$form = $this->createForm($this->get('orange.main.statistique_criteria'));
    	$form->handleRequest($request);
    	if($request->getMethod()=='POST' && $form->getData()){
    			$req = $this->getDoctrine()->getRepository('OrangeMainBundle:Statistique')->getStatsByInstance($this->getUser(), $form->getData());
    			$data = $this->container->get('orange.main.calcul')->reporting($this->getUser(), $req->getQuery()->getArrayResult());
    			$stats = $this->container->get('orange.main.data')->mapDataReportingInstance($data);
    			$statuts = array(
    					'nbAbandon' => 'Abandon',
    					'nbDemandeAbandon' => "Demande d'abandon",
    					'nbFaiteDelai' => "Faite dans les délais",
    					'nbFaiteHorsDelai' => "Faite hors délai",
    					'nbNonEchue' => "Non échue",
    					'nbEchueNonSoldee' => "Echue non soldée",
    					'nbSoldeeHorsDelais' => 'Soldée hors délais',
    					'nbSoldeeDansLesDelais' => "Soldée dans les délais",
    					'total' => "Total",
    			);
    			foreach ($stats['taux'] as $key => $taux){
    				$statuts[$key] = $key;
    			}
    			$this->get('session')->set('reporting_export',array('data' => $stats, 'statut' => $statuts, 'tmp' => 0) );
    			$this->get('session')->set('reporting',array('req' => $req->getDQL(), 'param' => $req->getParameters(), 'tp' => 2));
    	}
    	return array('form' => $form->createView(),
    				 'statut' => isset($statuts) ? $statuts : null,
    				 'data' => isset($stats) ? $stats : null
    	);
    }
    
  
    /**
     * @Route("/reporting_structure", name="reporting_structure")
     * @Template()
     *
     */
    public function reportingStructureAction(Request $request){
    	$var = $this->container->getParameter('ids');
    	$form = $this->createForm($this->get('orange.main.statistique_criteria'));
    	$form->handleRequest($request);
    	if($request->getMethod()=='POST' && $form->getData()){
    			$idStructs = $this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getIdStructure($var['structure']['drps']);
    			$req = $this->getDoctrine()->getRepository('OrangeMainBundle:Statistique')->getStatsByStructure($idStructs, $this->getUser(), $form->getData());
    			$data = $this->container->get('orange.main.calcul')->reporting($this->getUser(), $req->getQuery()->getArrayResult());
    			$stats = $this->container->get('orange.main.data')->mapDataReportingStructure($data);
    			$statuts = array(
    					'nbAbandon' => 'Abandon',
    					'nbDemandeAbandon' => "Demande d'abandon",
    					'nbFaiteDelai' => "Faite dans les délais",
    					'nbFaiteHorsDelai' => "Faite hors délai",
    					'nbNonEchue' => "Non échue",
    					'nbEchueNonSoldee' => "Echue non soldée",
    					'nbSoldeeHorsDelais' => 'Soldée hors délais',
    					'nbSoldeeDansLesDelais' => "Soldée dans les délais",
    					'total' => "Total",
    			);
    			foreach ($stats['taux'] as $key => $taux){
    				$statuts[$key] = $key;
    			}
    			$this->get('session')->set('reporting_export',array('data' => $stats, 'statut' => $statuts, 'tmp' => 1) );
    			$this->get('session')->set('reporting',array('req' => $req->getDQL(), 'param' => $req->getParameters(), 'tp' => 1));
    	}
    	return array('form' => $form->createView(),
    				'statut' => isset($statuts) ? $statuts : null,
    				'data' => isset($stats) ? $stats : null
    	);
    	
    }
    /**
     * @Route("/export_reporting", name="export_reporting")
     * @Template()
     */
    public function exportReportingAction() {
        $data= $this->get('session')->get('reporting_export', array());
        if($data['tmp'] == 0){
            $objWriter = $this->get('orange.main.reporting')->reportingInstanceAction($data['data'], $data['statut']);
        }else
            $objWriter = $this->get('orange.main.reporting')->reportingStructureAction($data['data'], $data['statut']);
        $filename = "test.xlsx";
        $objWriter->save($this->web_dir."/upload/reporting/$filename");
        return $this->redirect($this->getUploadDir().$filename);
    }
    
    /**
     * @Route("/nouveau_reporting", name="nouveau_reporting")
     * @Method("GET")
     * @Template()
     */
    public function newReportingAction()
    {
        $parameters = array();
        $req = $this->get('session')->get('reporting', array());
        $tp = $req['tp'];
        foreach($req['param'] as $value) {
            if(is_numeric($value->getValue()) || is_array($value->getValue()) ){
                $parameters[$value->getName()] = $value->getValue();
            }
            else
                $parameters[$value->getName()] = $value->getValue()->getId();
        }
        $entity = new Reporting();
        $entity->setRequete($req['req']);
        $entity->setParameter(serialize($parameters));
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
        $req = $this->get('session')->get('reporting', array());
        $tp = $req['tp'];
        $entity = new Reporting();
        $form = $this->createCreateForm($entity,'Reporting');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setUtilisateur($this->getUser());
            $entity->setTypeReporting($tp);
            $this->container->get('orange.main.envoi')->generateEnvoi($entity);
            $em->persist($entity);
            $em->flush();
            // envoie de mail pour notifier de la creation du reporting
            $to = $this->getUser()->getEmail();
            $result = $this->container->get('orange.main.mailer')->sendNotifReport($to, $entity, $this->getUser());
            
            return new JsonResponse(array('url' => $this->generateUrl('les_reportings')));
            
        }
        return $this->render('OrangeMainBundle:Statistique:newReporting.html.twig',
                array(
                        'entity' => $entity,
                        'form'   => $form->createView(),
                ), new \Symfony\Component\HttpFoundation\Response(null,303));
    }
    
    /**
     * @Route("/filtre_reporting", name="filtre_reporting")
     * @Template()
     *
     */
    public function filtreReportingAction(){
        $form = $this->createForm(new StatistiqueCriteria());
        var_dump($data);exit;
        $form = $this->createForm(new StatistiqueCriteria());
        return array('form' => $form->createView());
    }
    
      
    /*
     * reperetoire de sauvegarde des reporting
     */
    private function getUploadDir() {
        return $this->getRequest()->getBaseUrl().($this->get('kernel')
                ->getEnvironment()=='prod' ? '' : '/..')."/upload/reporting/";
    }
}
