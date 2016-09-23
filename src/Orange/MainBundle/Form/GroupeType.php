<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Orange\MainBundle\Repository\UtilisateurRepository;

class GroupeType extends AbstractType
{
	

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null, array('label' => 'Nom du groupe :'))
            ->add('email', 'email', array('label' => 'Email :'))
            ->add('description',null,array('label' => 'Description :'))
            ->add('tmp_membre', 'entity', array(
            		'class' => 'OrangeMainBundle:Utilisateur',
            		'label' => 'Membres :',
            		'empty_value' => 'Choisir les membres ...',
            		'multiple' => true,
            		'query_builder'=>function(UtilisateurRepository $ur){
            				return $ur->filter();
            		}
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
            'data_class' => 'Orange\MainBundle\Entity\Groupe'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_groupe';
    }
}
