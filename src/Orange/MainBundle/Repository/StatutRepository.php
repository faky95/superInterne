<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\CustomInterface\RepositoryInterface;
use Orange\MainBundle\Entity\Action;

class StatutRepository extends BaseRepository {
	
	public function listAllStatuts(){
		$queryBuilder = $this->createQueryBuilder('s');
		$queryBuilder->where('s.typeStatut = 2');
		return $queryBuilder;
	}
	
	public function listAllStatutsActions(){
		$queryBuilder = $this->createQueryBuilder('s');
		$queryBuilder->where('s.typeStatut = 2');
		return $queryBuilder->getQuery()->getArrayResult();
	}
	
	public function listAllStatutSign(){
		$queryBuilder = $this->createQueryBuilder('s');
		$queryBuilder->where('s.typeStatut = 1');
		return $queryBuilder;
	}
	
	public function getArrayStatutImport() {
		$codeStatuts=array(Statut::ACTION_ABANDONNEE, Statut::ACTION_SOLDEE, Statut::ACTION_NON_ECHUE, Statut::ACTION_NOUVELLE, Statut::ACTION_EN_COURS);
		$statuts = $this->createQueryBuilder('s')
						->select('s.id')
						->where('s.code in(:statuts)')
						->setParameter('statuts',$codeStatuts)
						->getQuery()->getArrayResult();
		$tabStatuts = array();
		$i = 0;
		foreach($statuts as $ta) {
			$tabStatuts[$i] = $ta['id'];
			$i++;
		}
		return $tabStatuts;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Action $action
	 */
	public function getStatutForAction($action) {
		return $this->createQueryBuilder('q')
			->innerJoin('OrangeMainBundle:Action', 'a', 'WITH', 'a.etatReel = q.code')
			->where('a = :action')->setParameter('action', $action)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Tache $tache
	 */
	public function getStatutForTache($tache) {
		return $this->createQueryBuilder('q')
			->innerJoin('OrangeMainBundle:Tache', 't', 'WITH', 't.etatCourant = q.code')
			->where('t = :tache')->setParameter('tache', $tache)
			->getQuery()
			->getOneOrNullResult();
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Signalisation $signalisation
	 */
	public function getStatutForSignalisation($signalisation) {
		return $this->createQueryBuilder('q')
		->innerJoin('OrangeMainBundle:Signalisation', 'a', 'WITH', 'a.etatCourant = q.code')
		->where('a = :sign')->setParameter('sign', $signalisation)
		->getQuery()
		->getOneOrNullResult();
	}
}

?>