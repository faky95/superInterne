<?php 

namespace Orange\MainBundle\Repository;


class ActionAvancementRepository extends BaseRepository {
	
	public function findForManyAction($ids){
		$qb            = $this  -> createQueryBuilder('a')
								-> where('identity(a.action) IN (:ids)')
								-> setParameter('ids', $ids);
		return $qb->getQuery()->execute();
	}
		
}
?>