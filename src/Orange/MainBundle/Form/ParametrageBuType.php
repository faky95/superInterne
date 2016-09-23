<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParametrageBuType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hasValidation', null,array('label'=>'Validation des action ?'))
            ->add('hasSignalisation', null,array('label'=>'Signalisation ?'))
            ->add('affichageStats', null, array('label'=>'Affichage statistique direction:'))
            ->add('couleur', null, array('label'=>'Couleur :'))
            ->add('logoImage', null, array('label'=>'Logo:'))
            ->add('logoTexte', null, array('label'=>'Affichage statistique direction:'))
            ->add('isImage', null, array('label'=>'Logo Image ?'))
            ->add('bu')
            ->add('entete')
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
            'data_class' => 'Orange\MainBundle\Entity\ParametrageBu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_parametragebu';
    }
}
