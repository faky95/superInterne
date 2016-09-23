<?php

namespace Orange\QuickMakingBundle\Twig;

class CalendarExtension extends \Twig_Extension
{
	
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            //new \Twig_SimpleFunction('refresh_table', array($this, 'refresh_table'), array('is_safe' => array('html')))
        );
    }

    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'calendar';
    }
}
