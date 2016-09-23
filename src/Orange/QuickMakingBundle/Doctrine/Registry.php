<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orange\QuickMakingBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Registry as BaseRegistry;

/**
 * References all Doctrine connections and entity managers in a given Container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Registry extends BaseRegistry
{


	/**
	 * {@inheritdoc}
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getManager($name = null) {
		$em = parent::getManager($name);
		try {
			$token = $this->container->get('security.context')->getToken();
			$user = $token ? $token->getUser() : null;
		} catch(\Exception $e) {
			$user = null;
		}
		$em->setParameters(
				$this->container->hasParameter('ids') ? $this->container->getParameter('ids') : array(), 
				$this->container->hasParameter('states') ? $this->container->getParameter('states') : array(),
				$user
			);
		return $em;
	}
}
