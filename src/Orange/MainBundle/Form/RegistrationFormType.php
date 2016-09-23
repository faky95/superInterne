<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);
		// add your custom field
		$builder->add('username', null, array('label' => 'Nom d\'utilisateur :'))
			->add('nom', null, array('label' => 'Nom :'))
			->add('prenom', null, array('label' => 'Prénom :'))
			->add('email', 'email', array('label' => 'Adresse Email :'))
			->add('telephone', null, array('label' => 'Téléphone :'))
			->add('manager', 'checkbox', array('label' => 'Est-il manager ?', 'required' => true ))
			->add('isAdmin', 'checkbox', array('label' => 'Est-il un administrateur ?', 'required' => true))
			->add('structure', null, array('label' => 'Structure :', 'empty_value' => 'Choisir la structure ---', 'required' => true))
			->add('matricule', null, array('label' => 'Matricule :'))
			->add('plainPassword', 'repeated', array(
            		'type' => 'password',
            		'options' => array('translation_domain' => 'FOSUserBundle'),
            		'first_options' => array('label' => 'form.password', 'attr' => array('placeholder' => 'Saisir le mot de passe')),
            		'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('placeholder' => 'Confirmation')),
            		'invalid_message' => 'fos_user.password.mismatch',
                  ));
	}

	public function getName() {
		return 'orange_user_registration';
	}
}