<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class UtilisateurType extends BaseType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
        $builder->add('prenom', null, array('label'=> 'Prénom : '))
            ->add('nom', null, array('label'=> 'Nom : '))
            ->add('matricule', null, array('label'=> 'Matricule : '))
            ->add('telephone', null, array('label'=> 'Telephone :'))
            ->add('manager', null, array('label'=> 'Est Manager? :'))
            ->add('structure', null, array('label'=> 'Structure :'))
            ->add('isAdmin', null, array('label'=> 'Est Admin? :'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password', 'attr' => array('placeholder' => 'Saisir le mot de passe')),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('placeholder' => 'Confirmation')),
                'invalid_message' => 'fos_user.password.mismatch',
              ))
            ->add('canCreateActionGenerique', 'checkbox', array('label' => 'Peut créer des actions génériques ?'))
            ->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Utilisateur',
        	'intention'  => 'profile'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fos_user_profile';
    }
}
