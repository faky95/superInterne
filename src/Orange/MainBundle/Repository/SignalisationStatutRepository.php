<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Espace;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\TypeStatut;

class SignalisationStatutRepository extends EntityRepository{
	
	public function getStatut($id){
		return $this->createQueryBuilder('s')
		->innerJoin('s.signalisation', 'i')
		->where("i.id = ".$id)
		->getQuery()->getResult();
	}
	
	public function findConfirmationSignalisation( $signalisation_id ) {
		
		return $this->createQueryBuilder('ss')
					->select('ss')
					->innerJoin('ss.statut', 'st')
					->innerJoin('st.typeStatut', 'ts')
					->where('ss.signalisation =:sign')
					->andWhere('st.code =:stateV OR st.code =:stateI')
					->andWhere('ts.libelle =:typestate')
					->setParameters(array('sign'=> $signalisation_id,
							'stateV'=>Statut::SIGNALISATION_VALIDE,
							'stateI'=>Statut::SIGNALISATION_INVALIDE,
							'typestate'=>TypeStatut::TYPE_SIGNALISATION ))
					->getQuery()
					->getResult();
	}
	
	public function tableauBord($code)
	{
		return $this->createQueryBuilder('q')
					->select('COUNT(s.id)')
					->innerJoin('q.signalisation', 's')
					->innerJoin('q.statut', 'e')
					->where('e.code =:code')
					->getQuery()
					->getResult()
		;
	}
	
}
?>