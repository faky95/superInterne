<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChantierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, array('label' => 'Nom du chantier :'))
            ->add('description', null, array('label' => 'Description :'))
            ->add('etat')
            ->add('projet', 'entity', array(
        		  							 'label' => 'Projet Associes: ',
            								 'empty_value' => 'Choisir le projet',
            								 'class' => 'OrangeMainBundle:Projet'
        		 				))
           ->add('tmp_membre', 'entity', array(
        		 						'multiple' => true,
        		 						'class'=>'Orange\MainBundle\Entity\Utilisateur',
        		 						'label' => 'Membres',
        		 						'empty_value' => 'Choisir les membres'
        		 				))
          ->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
          ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Chantier'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_chantier';
    }
}
