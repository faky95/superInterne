<?php
namespace Orange\MainBundle\Mapping;

class AbstractMapping {
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @return \Orange\MainBundle\Mapping\RelanceMapping
	 */
	public function getRelance() {
		return new RelanceMapping();
	}
	
	/**
	 * @return \Orange\MainBundle\Mapping\ReportingMapping
	 */
	public function getReporting() {
		return new ReportingMapping();
	}
	
	/**
	 * @return \Orange\MainBundle\Mapping\ExtractionMapping
	 */
	public function getExtraction() {
		return new ExtractionMapping();
	}
	
	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function setEntityManager($entityManager) {
		$this->em = $entityManager;
		return $this;
	}
}