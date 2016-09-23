<?php

namespace Orange\QuickMakingBundle\Twig;

class DatatableExtension extends \Twig_Extension
{
	
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('refresh_table', array($this, 'refreshTable'), array('is_safe' => array('html')))
        );
    }

    
    public function refreshTable($idTable) {
    	return sprintf('<script type="text/javascript">$("%s").dataTable().fnDraw()</script>', $idTable);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'datatable';
    }
}
