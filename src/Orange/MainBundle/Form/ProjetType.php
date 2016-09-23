<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, array('label' => 'Nom du projet :'))
            ->add('description', null, array('label' => 'Description :'))
            ->add('chefProjet', 'entity', array(
        		  							 'label' => 'Chef de projet',
            								 'empty_value' => 'Choisir le chef de projet',
            								 'class' => 'OrangeMainBundle:Utilisateur'
				 ))
        	->add('domaine', 'collection', array('label' => 'Ajouter un domaine',
        										 'type'  => new DomaineType(),
        										 'allow_add' => true,
            	  								 'allow_delete' => true,
            									 'by_reference' => false
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
            'data_class' => 'Orange\MainBundle\Entity\Projet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_projet';
    }
}
