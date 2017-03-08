<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membre d'un groupe
 *
 * @ORM\Table(name="membre_groupe")
 * @ORM\Entity
 * 
 */
class MembreGroupe
{

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private  $id;
	 
	/**
	 * @var \Utilisateur
	 *
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 * })
	 */
	private $utilisateur;

	/**
	 * @ORM\ManyToOne(targetEntity="Groupe", inversedBy="membreGroupe")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $groupe;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_affectation", type="date", nullable=true)
	 */
	private $dateAffectation;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateAffectation
     *
     * @param \DateTime $dateAffectation
     * @return MembreGroupe
     */
    public function setDateAffectation($dateAffectation)
    {
        $this->dateAffectation = $dateAffectation;

        return $this;
    }

    /**
     * Get dateAffectation
     *
     * @return \DateTime 
     */
    public function getDateAffectation()
    {
        return $this->dateAffectation;
    }

    /**
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return MembreGroupe
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set groupe
     *
     * @param \Orange\MainBundle\Entity\Groupe $groupe
     * @return MembreGroupe
     */
    public function setGroupe(\Orange\MainBundle\Entity\Groupe $groupe)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return \Orange\MainBundle\Entity\Groupe 
     */
    public function getGroupe()
    {
        return $this->groupe;
    }
}
