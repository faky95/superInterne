<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Orange\MainBundle\Entity\Structure;

class NotificationCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('typeNotification', null, array('label' => 'Type de notification'))
    		->add('startDate', 'date', array('label' => 'Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
    		->add('endDate', 'date', array('label' => 'au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
	    	->add('destinataire', null, array('label' => 'Porteur', 'query_builder'=>function($sr) { return $sr->filter(); }, 'multiple' => true))
	    	->add('copy', null, array('label' => 'En copie', 'query_builder'=>function($sr) { return $sr->filter(); }, 'multiple' => true))
    		->add('structure', 'entity', array('class' => 'OrangeMainBundle:Structure', 'label' => 'Structure', 'empty_value' => 'Choisir la structure ...', 'property' => 'name',
									'query_builder'=>function($sr) { return $sr->filter(); }
        		));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
	            'data_class' => 'Orange\MainBundle\Entity\Notification'
	        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'notification_criteria';
    }
}
