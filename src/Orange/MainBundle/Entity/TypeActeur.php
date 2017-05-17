<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeActeur
 * @ORM\Table(name="typeacteur")
 * @ORM\Entity()
 */
class TypeActeur
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *  @ORM\OneToMany(targetEntity="Statistique", mappedBy="type", cascade={"persist","remove","merge"})
     */
    private $statistique;
    
    
    public function __construct(){
    }
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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Type
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Add statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     *
     * @return Type
     */
    public function addStatistique(\Orange\MainBundle\Entity\Statistique $statistique)
    {
        $this->statistique[] = $statistique;

        return $this;
    }

    /**
     * Remove statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     */
    public function removeStatistique(\Orange\MainBundle\Entity\Statistique $statistique)
    {
        $this->statistique->removeElement($statistique);
    }

    /**
     * Get statistique
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatistique()
    {
        return $this->statistique;
    }
}
