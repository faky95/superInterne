<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormuleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$array = array(		'SHD' => 'SHD',
    						'SDD' => 'SDD',
    						'FDD' => 'FDD',
    						'FHD' => 'FHD',
    						'A' => 'A',
    						'DA' => 'DA',
    						'NE' => 'NE',
    						'ENS' => 'ENS'
    			
    	);
        $builder
        	->add('visibilite',  null, array('label' => 'Visible sur la page d\'accueil'))
            ->add('libelle',  null, array('label' => 'Libellé:'))
            ->add('couleur', null, array('label' => 'Couleur :'))
            ->add('num', 'choice', array('label' => 'Numérateur :',
    				'choices' => $array,
            		'multiple' => true,
						))
            		->add('denom', 'choice', array('label' => 'Dénominateur :',
    				'choices' => $array,
            		'multiple' => true,
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
            'data_class' => 'Orange\MainBundle\Entity\Formule'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_formule';
    }
}
