<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class modifierAnimateurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utilisateur', 'choice', array(
            		'empty_value' => '--- Choisir la périodicité ---',
            		'label'=>'Périodicité :',
            		'choices' => array(
			            				'HEBDOMADAIRE' => 'Périodicité hebdomadaire',
			            				'MENSUEL' => 'Périodicité mensuelle',
			            				'BIMESTRE' => 'Périodicité bimestrielle',
			            				'TRIMESTRE' => 'Périodicité trimestrielle',
			            				'SEMESTRE' => 'Périodicité semestrielle',
			            				'ANNUEL' => 'Périodicité annuelle'
            						 )
            					))
        	->add('action', new ActionType())
        	->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\ActionCyclique'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_actioncyclique';
    }
}