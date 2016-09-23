<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionStatutType extends AbstractType
{
	
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('erq', new DocumentType())
            ->add('commentaire', null,array('label'=>'Commentaire'))
            ->add('dateFinExecut', 'date', array(
            		'label' => 'Date',
            		'widget' => 'single_text',
            		'input'  => 'datetime',
            		'format' => 'dd/MM/yyyy',
            		'attr' => array('class' => 'datepicker'),
            ))
            ->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel', 'data-dismiss'=>'modal')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\ActionStatut'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_actionstatut';
    }
}
