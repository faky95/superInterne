<?php
namespace Orange\MainBundle\CustomInterface;

use Doctrine\ORM\QueryBuilder;

interface RepositoryInterface  {
	
	public function filter();
	
	public function filterByProfile(QueryBuilder $queryBuilder, $alias = null);
	
}