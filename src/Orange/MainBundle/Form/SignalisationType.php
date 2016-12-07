<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use  Orange\MainBundle\EvenListener\AddSousInstanceSubscriber;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Orange\MainBundle\Repository\InstanceRepository;

class SignalisationType extends AbstractType
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
    	$user_id 		= $options['attr']['user_id'];
    	$structure_id 	= $options['attr']['structure_id'];
        $builder
            ->add('libelle',null,array('label'=>'Libellé :'))
            ->add('description',null,array('label'=>'Description :'))
            ->add('site',null,array('label'=>'Site :'))
            ->add('instance', null, array(
            		'label' => 'Périmètre :',
            		'empty_value' => '--- Choisir le périmètre ---',
            		'query_builder' => function(InstanceRepository $er ) use ( $user_id ) {
            			return $er->filterForSignalisation();
            		}
            ))
            ->add('constatateur', null, array(
            		'label' => 'Constat fait par :',
            		'empty_value' => '--- Choisir le constatateur ---',
            		'query_builder' => function(EntityRepository $er ) use ($structure_id) {
            			return $er->createQueryBuilder('q')
		            			  ->innerJoin('q.structure', 'str')
		            			  ->where('str.id = ?1')
		            			  ->setParameter(1, $structure_id);
            		}
            ))
            ->add('dateConstat', 'date', array('label' => 'Date de constat :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('domaine', null, array('label'=>'Domaine :', 'empty_value' => 'Choisissez un domaine ..'))
            ->add('typeSignalisation', null, array('label'=>'Type Signalisation :', 'empty_value' => 'Choisissez un type de signalisation...'))
            ->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-default cancel')))
          ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Signalisation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_signalisation';
    }
}
