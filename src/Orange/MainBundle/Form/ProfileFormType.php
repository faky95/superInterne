<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);

        $builder
			->add('nom', null, array('label' => 'Nom :'))
			->add('prenom', null, array('label' => 'Prenom :'))
			->add('email', 'email', array('label' => 'Adresse Email :'))
			->add('telephone', null, array('label' => 'Telephone :'))
			->add('canCreateActionGenerique', 'checkbox', array('label' => 'Peut créer des actions génériques ?'))
			->add('manager', 'checkbox', array(
				    'label' => 'Est-il manager ?',
					'required' => true ))
			->add('isAdmin', 'checkbox', array(
				  'label' => 'Est-il un administrateur ?',
				  'required' => true
			        ))
			->add('structure', 'entity', array(
							'label' => 'Structure :',
							'class'=>'Orange\MainBundle\Entity\Structure',
							'required' => true,
					))
			->add('matricule', null, array('label' => 'Matricule :'))
             ->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-info marginR10 marginL10')))
            ->add('cancel', 'submit', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-danger')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'profile',
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getName()
    {
        return 'orange_user_profile';
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
        ;
    }
}
