<?php
namespace Orange\MainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrangeMainBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\HttpKernel\Bundle\Bundle::boot()
	 */
	public function boot() {
		// TODO: Auto-generated method stub
		$ids	= $this->container->getParameter('ids');
		$types	= $this->container->getParameter('types');
		\Orange\MainBundle\Entity\TypeNotification::$ids = $ids['typeNotification'];
		\Orange\MainBundle\Entity\TypeStructure::$ids = $ids['typeStructure'];
		\Orange\MainBundle\Entity\Periodicite::$ids = $ids['periodicite'];
		\Orange\MainBundle\Entity\Extraction::$types = $types['extraction'];
	}
}
