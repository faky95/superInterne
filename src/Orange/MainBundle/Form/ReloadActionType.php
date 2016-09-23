<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReloadActionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add(	'isReload', 'checkbox', array(
		    			'label' => 'Recharger',
		    			'required' => false))
		    	->add('libelle')
		    	->add('dateDebut', 'date', array(
		    			'label' => 'Date de Debut :',
		    			'widget' => 'single_text',
		    			'input'  => 'datetime',
		    			'format' => 'dd/MM/yyyy'
		    	))
		    	->add('dateInitial', 'date', array(
		    			'label' => 'Délai :',
		    			'widget' => 'single_text',
		    			'input'  => 'datetime',
		    			'format' => 'dd/MM/yyyy'
		    	))
		    	->add('porteur','entity',
		    			array('label'=>'Porteur :',
		    					'class'=>'Orange\MainBundle\Entity\Utilisateur',
		    					'empty_value' => '--- Choix Porteur ---',
		    			))
				->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
				->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')))
		    
    	;
    }
    
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Action'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_reloadtype';
    }
}