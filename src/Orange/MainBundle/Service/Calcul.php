<?php
namespace Orange\MainBundle\Service;

class Calcul 
{
	
		/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	public function __construct($em, $container)
	{
		$this->em = $em;
		$this->container = $container;
	}	
	
	public function cumul($stats)
	{
		$nbAbandon = 0;
		$nbDemandeAbandon = 0;
		$nbFaiteDelai = 0;
		$nbFaiteHorsDelai = 0;
		$nbNonEchue = 0;
		$nbEchueNonSoldee = 0;
		$nbNonEchueNonSoldee = 0;
		$nbSoldeeHorsDelais = 0;
		$nbSoldeeDansLesDelais = 0;
		$total = 0;
		for ($i=0; $i<sizeof($stats);$i++ ){
			$nbAbandon += intval($stats[$i]['nbAbandon']);
			$nbDemandeAbandon += intval($stats[$i]['nbDemandeAbandon']);
			$nbFaiteDelai += intval($stats[$i]['nbFaiteDelai']);
			$nbFaiteHorsDelai += intval($stats[$i]['nbFaiteHorsDelai']);
			$nbNonEchue += intval($stats[$i]['nbNonEchue']);
			$nbEchueNonSoldee += intval($stats[$i]['nbEchueNonSoldee']);
			$nbNonEchueNonSoldee += intval($stats[$i]['nbNonEchueNonSoldee']);
			$nbSoldeeHorsDelais += intval($stats[$i]['nbSoldeeHorsDelais']);
			$nbSoldeeDansLesDelais += intval($stats[$i]['nbSoldeeDansLesDelais']);
			$total += intval($stats[$i]['total']);
				
			$stats[$i]['nbAbandon'] =$nbAbandon;
			$stats[$i]['nbDemandeAbandon'] = $nbDemandeAbandon;
			$stats[$i]['nbEchueNonSoldee'] = $nbEchueNonSoldee;
			$stats[$i]['nbFaiteHorsDelai'] = $nbFaiteDelai;
			$stats[$i]['nbNonEchue'] = $nbFaiteHorsDelai;
			$stats[$i]['nbEchueNonSoldee'] = $nbNonEchue;
			$stats[$i]['nbNonEchueNonSoldee'] = $nbNonEchueNonSoldee;
			$stats[$i]['nbSoldeeHorsDelais'] = $nbSoldeeDansLesDelais;
			$stats[$i]['nbSoldeeDansLesDelais'] = $nbSoldeeHorsDelais;
			$stats[$i]['total'] =$total;
		}
		return $stats;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Bu $bu
	 * @param array $stats
	 * @return number
	 */
	public function stats($bu, $stats)
	{
		$formule = $this->em->getRepository('OrangeMainBundle:Formule')->getByBu($bu);
		$taux=array();
		if(count($formule)>0) {
			foreach ($formule as $for) {
				$taux[$for['id']] = array('libelle' => $for['libelle']);
				$taux[$for['id']]['num']=$this->changeTableau(explode('+',$for['numerateur']));
				$taux[$for['id']]['denom']=$this->changeTableau(explode('+',$for['denominateur']));
			}
			if(count($stats)>0)
				foreach($stats as $key => $data) {
					$stats[$key]['taux'] = array();
					foreach($taux as $kpi) {
						$stats[$key]['taux'][$kpi['libelle']] = round($this->computeKpi($data, $kpi), 1);
					}
				}
		}
		return $stats;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Utilisateur $user
	 * @param array $stats
	 * @return number
	 */
	public function reporting($user, $stats)
	{
		$bu = $user->getStructure()->getBuPrincipal();
		$formule = $this->em->getRepository('OrangeMainBundle:Formule')->getByBu($bu);
		$taux=array();
		foreach ($formule as $for){
			$taux[$for['id']] = array('libelle' => $for['libelle']);
			$taux[$for['id']]['num']=$this->changeTableau(explode('+',$for['numerateur']));
			$taux[$for['id']]['denom']=$this->changeTableau(explode('+',$for['denominateur']));
		}
		foreach($stats as $key => $data) {
			$stats[$key]['taux'] = array();
			foreach($taux as $kpi) {
				$stats[$key]['taux'][$kpi['libelle']] = $this->computeKpi($data, $kpi);
			}
			$stats[$key]['taux']["Taux de respect du délai"] = $this->computeRespectDelai($data);
		}
		return $stats;
	}
	
	public function computeKpi($data, $kpi) {
		$num = 0;
		$denom = 0;
		$test = array();
		foreach ($kpi['num'] as $val){
			$num += $data[$val];
		}
		foreach ($kpi['denom'] as $val){
			$denom += $data[$val];
			array_push($test, $data[$val]);
		}
		return $denom ? round(($num / $denom)*100) : 0;
	}
	
	public function computeRespectDelai($data) {
		return 10;
	}
	
	public function changeTableau($tab) {
		$emRef = $this->em->getRepository('OrangeMainBundle:Reference');
		foreach ($tab as $k => $val){
			$tab[$k] = $emRef->getBySymbole($val)->getReferenceStatistique();
		}
		return $tab;
	}
}

