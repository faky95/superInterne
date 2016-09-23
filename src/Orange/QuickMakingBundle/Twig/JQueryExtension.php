<?php

namespace Orange\QuickMakingBundle\Twig;

class JQueryExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('datepicker', array($this, 'datepicker'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('to_array', array($this, 'to_array'), array('is_safe' => array('html')))
        );
    }

    public function datepicker($selector, $function = null) {
        return sprintf('<script type="text/javascript">%s("%s");</script>', $function ? $function : 'dateChangeYearMonth', $selector);
    }

    public function to_array($data = array()) {
    	$str = null;
    	foreach($data as $value) {
        	$str .= (strlen($str)) ? ', '.$value : $value;
    	}
    	return sprintf('[%s]', $str);
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'jQuery';
    }
}
