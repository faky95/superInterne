<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Orange\MainBundle\OrangeMainForms;

class TacheStatutType extends AbstractType
{
	/**
	 * @param string $action
	 */
	public function __construct($action = null) {
		$this->action = $action;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('commentaire', null,array('label'=>'Motif :'));
    	if($this->action && ($this->action==OrangeMainForms::TACHESTATUT_FAIT || $this->action==OrangeMainForms::TACHESTATUT_DEMANDE_ABANDON)) {
	    	$builder->add('erq', 'collection', array('type' => new DocumentType(), 'allow_add' => true, 'by_reference' => false));
    	}
    	if($this->action && $this->action==OrangeMainForms::TACHESTATUT_FAIT) {
    		$builder->add('dateFinExecut', 'date', array(
    				'label' => 'Date', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'datepicker'),
    			));
    	}
    	$builder->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */														
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\TacheStatut'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tachestatut';
    }
}
