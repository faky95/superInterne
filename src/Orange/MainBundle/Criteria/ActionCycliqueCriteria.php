<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Criteria\ActionCriteria;

class ActionCycliqueCriteria extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pas', null, array('label'=>"Périodicité", 'query_builder' => function(EntityRepository $er) {
            			return $er->createQueryBuilder('q')->where('q.canBeCyclique = true');
        			}, 'attr' => array('class' => 'select pas'), 'empty_value' => 'Choix la périodicite ...'
            ))
            ->add('dayOfMonth', null, array(
            		'label'=>'Délai initial des occurences :', 'empty_value' => 'Choisir le jour du mois', 'attr' => array('class' => 'select')
            ))
            ->add('dayOfWeek', null, array(
            		'label'=>'Délai initial des occurences :', 'empty_value' => 'Choisir le jour de la semaine', 'attr' => array('class' => 'select')
            ))
            ->add('iteration', null, array('label'=>'Semaine:'))
        	->add('action', new ActionCriteria())
        	->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning')))
        	->add('effacer', 'submit', array('label' => 'Effacer', 'attr' => array('class' => 'btn btn-danger submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\ActionCyclique',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'actioncyclique_criteria';
    }
}

