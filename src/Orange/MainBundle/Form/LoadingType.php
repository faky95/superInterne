<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;

class LoadingType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			    ->add('file', 'file', array('label' => 'Fichier', 'required' =>  true, 'attr' => array('accept' => 'text/csv')))
				->add('add', 'submit', array('label' => 'Importer', 'attr' => array('class' => 'btn btn-warning')))
                ->add('cancel', 'button', array('label' => 'Réinitialiser', 'attr' => array('class' => 'btn btn-die cancel')));
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'orange_mainbundle_loading';
	}
}
