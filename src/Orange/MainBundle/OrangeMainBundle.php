<?php

namespace Orange\MainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrangeMainBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
