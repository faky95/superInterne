<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domaine
 *
 * @ORM\Table(name="envoi")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\EnvoiRepository")
 */
class Envoi
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
   
    /**
     * @var \Orange\MainBundle\Entity\Reporting
     * @ORM\ManyToOne(targetEntity="Orange\MainBundle\Entity\Reporting", inversedBy="envoi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reporting_id", referencedColumnName="id")
     * })
     */
    private $reporting;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="date", nullable=true)
     */
    private $dateEnvoi;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="periodicite", type="integer", nullable=true)
     */
    private $periodicite;

	/**
	 * @return int
	 */
	public function getTypeReporting()
	{
		return $this->typeReporting;
	}

	/**
	 * @param int $typeReporting
	 */
	public function setTypeReporting($typeReporting)
	{
		$this->typeReporting = $typeReporting;
	}

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="type_reporting", type="integer", nullable=true)
	 *
	 */
	private $typeReporting;
   
	public function getId() {
		return $this->id;
	}
	public function getReporting() {
		return $this->reporting;
	}
	public function setReporting($reporting) {
		$this->reporting = $reporting;
		return $this;
	}
	public function getDateEnvoi() {
		return $this->dateEnvoi;
	}
	public function setDateEnvoi($dateEnvoi) {
		$this->dateEnvoi = $dateEnvoi;
		return $this;
	}
	public function getPeriodicite() {
		return $this->periodicite;
	}
	public function setPeriodicite($periodicite) {
		$this->periodicite = $periodicite;
		return $this;
	}
	
	
	
	 	
}
