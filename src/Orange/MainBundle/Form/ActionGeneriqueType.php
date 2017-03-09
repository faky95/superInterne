<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionGeneriqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference')
            ->add('libelle', null, array('label'=>"Libellé"))
            ->add('description', null, array('label'=>"Description"))
            ->add('dateDebut', 'date', array('label' => 'Date de Début :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('dateInitial', 'date', array('label' => 'Date fin :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('statut')
            ->add('porteur', null, array('label'=>'Porteur :', 'empty_value' => "Choisir le porteur ..."
            		))
            ->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
       		->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\ActionGenerique'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_actiongenerique';
    }
}
