<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="complement")
 * @ORM\Entity
 */
class Complement
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
	 * @ORM\Column(name="avancements", type="string", length=50, nullable=true)
	 */
	private $avancements;

	/**
	 * @var string
	 * @ORM\Column(name="contributeurs", type="string", length=255, nullable=true)
	 */
	private $contributeurs;
	
	/**
	 * @var \Action
	 *
	 * @ORM\OneToOne(targetEntity="Action",inversedBy="complement")
	 * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
	 */
	private $action;



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
     * Set avancements
     *
     * @param string $avancements
     *
     * @return Complement
     */
    public function setAvancements($avancements)
    {
        $this->avancements .= " - " . $avancements;

        return $this;
    }

    /**
     * Get avancements
     *
     * @return string
     */
    public function getAvancements()
    {
        return $this->avancements;
    }

    /**
     * Set contributeurs
     *
     * @param string $contributeurs
     *
     * @return Complement
     */
    public function setContributeurs($contributeurs)
    {
        $this->contributeurs = $contributeurs;

        return $this;
    }

    /**
     * Get contributeurs
     *
     * @return string
     */
    public function getContributeurs()
    {
        return $this->contributeurs;
    }

   

    /**
     * Set action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     *
     * @return Complement
     */
    public function setAction(\Orange\MainBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \Orange\MainBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }
}
