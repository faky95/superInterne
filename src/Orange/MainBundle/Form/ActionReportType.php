<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionReportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
            		'label' => 'Date :',
            		'widget' => 'single_text',
            		'input'  => 'datetime',
            		'format' => 'dd/MM/yyyy',
            		'attr' => array('class' => 'datepicker'),
            ))
            ->add('description', null,array('label'=>'Commentaire:') )
            ->add('enregistrer', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            ->add('valider', 'submit', array('label' => 'Valider', 'attr' => array('class' => 'btn btn-success')))
            ->add('invalider', 'submit', array('label' => 'Invalider', 'attr' => array('class' => 'btn btn-warning')))
            ->add('modifier', 'submit', array('label' => 'Modifier', 'attr' => array('class' => 'btn btn-default')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\ActionReport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_actionreport';
    }
}
