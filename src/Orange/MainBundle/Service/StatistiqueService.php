<?php

namespace Orange\MainBundle\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Gedmo\DoctrineExtensions;
use Doctrine\ORM\Query\Expr;
use DateTime;

class StatistiqueService 
{
	
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}	

	/**
	 * 
	 */
	public function showStatistique(&$stats, &$params ){
		$tableau=array();
		foreach ($params as $key=> $par){
			$i=0;
			$aide=0;
			foreach ($stats as $key =>$stat){
				if($stat['libelle']==$par['libelle']){
					$this->customizeTableauStats($stat, $par['libelle'], $tableau, $i, false);
					$i++;
				}
			}
			if($aide==0){
				$this->customizeTableauStats($stat, $par['libelle'], $tableau, $i, false);
				$i++;
			}
		}
		return $tableau;
	}
	public function computeKpi($data, $kpi) {
		$num = 0;
		$denom = 0;
		foreach ($kpi['num'] as $key => $val){
			$num+=$data[$val];
		}
		foreach ($kpi['denom'] as $key => $val){
			$denom+=$data[$val];
		}
		return $denom ? $num / $denom : null;
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
	
	
}

