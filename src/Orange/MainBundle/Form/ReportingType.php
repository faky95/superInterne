<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ReportingType extends AbstractType
{
	

	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pas',null,
            	array('label'=>'Périodicité :',
            	'empty_value' => 'Choisir la périodicité',
	            				'attr' => array('class' => 'select')
				  ))
			  ->add('libelle',null,
			  		array('label'=>'Libellé :'
			  		))
            ->add('dayOfMonth',null,
            	array('label'=>'Jour du mois :',
            	'empty_value' => 'Choisir le jour du mois',
	            				'attr' => array('class' => 'select')
				  ))
            ->add('dayOfWeek',null,
            	array('label'=>'Jour de la semaine :',
            	'empty_value' => 'Choisir le jour de la semaine',
	            				'attr' => array('class' => 'select')
				  ))
			->add('requete')
			->add('query')
			->add('param')
            ->add('arrayType')
            ->add('typeReporting')
			->add('parameter')
			->add('iteration', null,
				array('label'=>'Semaine:'))
            ->add('destinataire', null,
            	array('label'=>'Destinataire(s):',
            		'multiple' => true,
            		'empty_value' => '--- Choix destinataires ---'
            	 ))
           ->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
           ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Reporting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_action';
    }
}
