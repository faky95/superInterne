<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parametrage
 *
 * @ORM\Table(name="parametrage")
 * @ORM\Entity
 */
class Parametrage
{
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private  $id;
	
	
	
}

