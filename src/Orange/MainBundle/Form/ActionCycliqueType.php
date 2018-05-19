<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\OrangeMainForms;

class ActionCycliqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pas', null, array('label'=>"Périodicité", 'query_builder' => function(EntityRepository $er) {
            			return $er->createQueryBuilder('q')->where('q.canBeCyclique = true');
        			}, 'attr' => array('class' => 'select pas'), 'empty_value' => 'Choix la périodicité ...'
            	))
            ->add('dayOfMonth', null, array(
            		'label'=>'Délai initial des occurences :', 'empty_value' => 'Choisir le jour du mois', 'attr' => array('class' => 'select')
            	))
            ->add('dayOfWeek', null, array(
            		'label' => 'Délai initial des occurences :', 'empty_value' => 'Choisir le jour de la semaine', 'attr' => array('class' => 'select')
            	))
            ->add('iteration', null, array('label' => 'Semaine:', 'attr' => array('min' => 1)))
        	->add('action', new ActionType(OrangeMainForms::ACTION_CYCLIQUE))
        	->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\ActionCyclique',
        	'validation_groups' => array('full' => 'Default'),
        	'cascade_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'actioncyclique';
    }
}
