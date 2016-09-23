<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BuType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle', null, array('label' => 'Libelle :'))
            ->add('structureInDashboard', null, array('label' => "Structure(s) affichées à l'accueil"))
            ->add('niveauValidation', 'choice', array(
        		  'choices' => array(
			        		  		'1' => 'N + 1',
			        		  		'2' => 'N + 2',
			        		  		'3' => 'N + 3',
			        		  		'4' => 'N + 4'
            						),
            		'label' => 'Niveau de validation :',
            		'empty_value' => '--- Choix un niveau de validation ---'
       			 				)
        		  )
            ->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            ->add('add_and_new_structure', 'submit', array('label' => 'Enregistrer et ajouter structure', 'attr' => array('class' => 'btn btn-warning')))
            ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel', 'data-dismiss'=>'modal')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Bu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_bu';
    }
}
