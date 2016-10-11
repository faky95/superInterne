<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Validator\Constraints\ActionReportDate as ARAssert;

/**
 * ActionReport
 *
 * @ORM\Table(name="action_report")
 * @ORM\Entity
 * @ARAssert
 */
class ActionReport
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
     * @var \Doctrine\Common\Collections\Collection
     * 
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="report")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     **/
    private $action;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_report", type="date", nullable=false)
     * @Assert\NotBlank(message="Vous devez préciser la date de report ")
     */
    private $date;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Assert\NotBlank(message="Merci de préciser le motif ")
     */
    private $description;

    

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
     * Set date
     *
     * @param \DateTime $date
     * @return ActionReport
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ActionReport
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return ActionReport
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
