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
use Orange\QuickMakingBundle\Annotation\QMLogger;
class StatistiqueSignController extends Controller
{

    /**
     * Lists all Statistique entities.
     *
     * @Route("/", name="statistiqueSign")
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
     *  @Method("GET")
     * @Template()
     * 
     */
    public function tableauStatistiqueUtilisateurAction(){
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$instances=array();
    	$instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->filter()->getQuery()->getArrayResult();
    	$tabTest=$rep->getStatistiqueUtilisateur();
    	$tableau=array();	
    	
    	$i=0;		$j=0;		$k=0;
    	foreach($instances as $inst){
    		$aide=0;$aide1=0;$aide2=0;
    		foreach($tabTest as $val){
    			$lib=($val['typ']==1)? 'Portee' : 'en contribution';
    			if($inst['id']==$val['inst']){
    				if($val['typ']==1){
    					$this->customizeTableauStats($val[0], $lib, $tableau, $i, false);
    					$aide=1;
    					$i++;
    				} else {
    					$this->customizeTableauStats($val[0], $lib, $tableau, $j, false);
    					$aide1=1;
    					$j++;
    				}
    			}
    		}
    		if($aide==0){
    			$this->customizeTableauStats($val[0], 'Portee', $tableau, $i, true);
    			$i++;
    		}
    		if($aide1==0){
    			$this->customizeTableauStats($val[0], 'en contribution', $tableau, $j, true);
    			$j++;
    		}
    		if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
    			$tabTest1=$rep->getStatistiqueCollaborateur();
    			foreach($tabTest1 as $v){
    				if($inst['id']==$v['inst']){
    					$this->customizeTableauStats($v[0], 'N-1', $tableau, $k, false);
    					$aide2=1;
    					$k++;
    				}
    			}
    			if($aide2==0){
    				$this->customizeTableauStats($v[0], 'N-1', $tableau, $k, true);
    				$k++;
    			}
    		}
    	}
    	return array(
    			'instances'=>$instances,
    			'tableauStatique'=>$tableau
    	);
    }
   
    /**
     *  @Method("GET")
     * @Template()
     * 
     */
    public function tableauStatistiqueEvoUtilisateurAction(){
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$tabTest=$rep->getStatisEvolutiveUtilisateur();
    	$semaines=array();
    	$tableau=array();
    	$i=0;		$j=0;		$k=0;
    	for($s=1;$s<date("W");$s++){
    		$semaines[$s-1]=$s;
    		$aide=0;
    		$aide1=0;
    		$aide2=0;
    		foreach($tabTest as $val){
    			$lib=($val['typ']==1)? 'Portee' : 'en contribution';
    			if($s==$val['semaine']){
    				if($val['typ']==1){
    					$this->customizeTableauStats($val, $lib, $tableau, $i, false);
    					$aide=1;
    					$i++;
    				} else {
    					$this->customizeTableauStats($val, $lib, $tableau, $j, false);
    					$aide1=1;
    					$j++;
    				}
    			}
    		}
    		if($aide==0){
    			$this->customizeTableauStats($val, 'Portee', $tableau, $i, true);
    			$i++;
    		}
    		if($aide1==0){
    			$this->customizeTableauStats($val, 'en contribution', $tableau, $j, true);
    			$j++;
    		}
    		if($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER)){
    			$tabTest1=$rep->getStatistiqueCollaborateur();
    			foreach($tabTest1 as $v){
    				if($inst['id']==$v['inst']){
    					$this->customizeTableauStats($v, 'N-1', $tableau, $k, false);
    					$aide2=1;
    					$k++;
    				}
    			}
    			if($aide2==0){
    				$this->customizeTableauStats($v, 'N-1', $tableau, $k, true);
    				$k++;
    			}
    		}
    	}
    	
    	//var_dump($tableau); exit;
    	return array(
    			'semaines'=>$semaines,
    			'tableauStatique'=>$tableau
    	);
    }
    
    /**
     *  @Method("GET")
     * @Template()
     */
    public function tableauStatistiqueAdminAction(){
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
		$structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionsByBu()->getQuery()->getArrayResult();
		$test=$rep->getStatistiqueAdmin($structures);
		$tableau=array();
		$i=0;	
		$lib='';
		foreach($structures as $struct){
			$aide=0;
			foreach($test as $val){
				if($struct['libelle']==$val['libelle']){
						$this->customizeTableauStats($val, $lib, $tableau, $i, false);
						$tableau[$lib]['Nombre d utilisateurs'][$i]=$val['nbUtilisateur'];
						$tableau[$lib]['Nombre moyens d actions par utilisateur'][$i]=($val['nbUtilisateur']==0)?0:(int)($val['total']/$val['nbUtilisateur']);
						$tableau[$lib]['Nombre d utilisateurs actifs'][$i]=$val['nbUtilisateurActif'];
						$tableau[$lib]['Nombre moyens d’actions par utilisateur actif'][$i]=($val['nbUtilisateurActif']==0)?0:(int)($val['total']/$val['nbUtilisateurActif']);
						$aide=1;
						$i++;
				}
			}
			if($aide==0){
				$this->customizeTableauStats($val, $lib, $tableau, $i, true);
				$tableau[$lib]['Nombre d utilisateurs'][$i]=0;
				$tableau[$lib]['Nombre moyens d actions par utilisateur'][$i]=0;
				$tableau[$lib]['Nombre d utilisateurs actifs'][$i]=0;
				$tableau[$lib]['Nombre moyens d’actions par utilisateur actif'][$i]=0;
				$i++;
			}
			
		}
		return array(
				'structures'=>$structures,
				'tableauStatique'=>$tableau
		);
    }
    
    /**
     *  @Method("GET")
     * @Template()
     */
    public function tableauStatistiqueEvoAdminAction(){
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionsByBu()->getQuery()->getArrayResult();
    	$test=$rep->getStatistiqueEvolutiveAdmin($structures);
    	$tableau=array();
    	$semaines=array();
    	$i=0;
    	$lib='';
    	for($s=1;$s<date("W");$s++){
    		$semaines[$s-1]=$s;
    		$aide=0;
    		foreach($test as $val){
    			if($s==$val['semaine']){
    				$this->customizeTableauStats($val, $lib, $tableau, $i, false);
    				$tableau[$lib]['Nombre d utilisateurs'][$i]=$val['nbUtilisateur'];
    				$tableau[$lib]['Nombre moyens d actions par utilisateur'][$i]=($val['nbUtilisateur']==0)?0:(int)($val['total']/$val['nbUtilisateur']);
    				$tableau[$lib]['Nombre d utilisateurs actifs'][$i]=$val['nbUtilisateurActif'];
    				$tableau[$lib]['Nombre moyens d’actions par utilisateur actif'][$i]=($val['nbUtilisateurActif']==0)?0:(int)($val['total']/$val['nbUtilisateurActif']);
    				$aide=1;
    				$i++;
    			}
    		}
    		if($aide==0){
    			$this->customizeTableauStats($val, $lib, $tableau, $i, true);
    			$tableau[$lib]['Nombre d utilisateurs'][$i]=0;
    			$tableau[$lib]['Nombre moyens d actions par utilisateur'][$i]=0;
    			$tableau[$lib]['Nombre d utilisateurs actifs'][$i]=0;
    			$tableau[$lib]['Nombre moyens d’actions par utilisateur actif'][$i]=0;
    			$i++;
    		}
    	
    	}
    	return array(
    			'semaines'=>$semaines,
    			'tableau'=>$tableau
    	);
    }
    
    /**
     *
     * @Route("/statsEvRapporteur/{structure_id}", name="statsEvRapporteur")
     * @Template()
     */
    public function testEvolutiveRapporteurAction($structure_id){
    	$rep=$this->getDoctrine()->getRepository('OrangeMainBundle:Statistique');
    	$structures=$this->getDoctrine()->getRepository('OrangeMainBundle:Structure')->getDirectionAndDepartmentByStructure($structure_id)->getQuery()->getArrayResult();
    	$instances=$this->getDoctrine()->getRepository('OrangeMainBundle:Instance')->getInstanceRapporteur($structures);
    	$stats=$rep->getStatistiqueEvolutiveRapporteur($structures);
    	
    	
    
    	$tableau=array();
    	$semaines=array();
    	foreach($structures as $str){
    		$i=0;
    		for($s=1;$s<date("W");$s++){
    			$aide=0; $semaines[$s-1]=$s;
    			foreach ($stats as $sta){
    
    				if($s==$sta['semaine'] && $str['id']==$sta['struct']){
    					$this->customizeTableauStats($sta, $str['libelle'], $tableau, $i, false);
    					$aide=1;
    					$i++;
    				}
    			}
    			if($aide==0){
    				$this->customizeTableauStats($sta, $str['libelle'], $tableau, $i, true);
    				$i++;
    			}
    		}
    	}
    	var_dump($stats);exit;
    	return array(
    			'semaines'=>$semaines,
    			'tableau'=>$tableau
    	);
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
    		$tab[$lib]['Taux de realisation globale'][$index]=($val['total']==0)?0:(number_format((($val['nbSoldee']/$val['total'])*100),2)).'%';
    		$tab[$lib]['Taux de realisation dans les delais'][$index]=($val['total']==0)?0:(number_format((($val['nbSoldeeDansLesDelais']/$val['total'])*100),2)).'%';
    	} else{
    		$tab[$lib]['Abandonnée'][$index]=0;
    		$tab[$lib]['Demande Abandon'][$index]=0;
    		$tab[$lib]['Echue non Soldee'][$index]=0;
    		$tab[$lib]['Non Echue'][$index]=0;
    		$tab[$lib]['Faite'][$index]=0;
    		$tab[$lib]['Soldee'][$index]=0;
    		$tab[$lib]['Soldee dans les delais'][$index]=0;
    		$tab[$lib]['Total'][$index]=0;
    		$tab[$lib]['Taux de realisation globale'][$index]=0;
    		$tab[$lib]['Taux de realisation dans les delais'][$index]=0;
    	}
    }
    
}
