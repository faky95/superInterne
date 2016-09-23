<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatistiqueActionCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('domaine', null, array('label' =>'Domaine', 'empty_value' => '--- Choisir le domaine ---'))
		        ->add('instance', null, array('label' =>'Instance', 'empty_value' => '--- Choisir l\'instance ---' ))
		        ->add('porteur', null, array('label' =>'Porteur', 'empty_value' => '--- Choisir le porteur ---'))
		        ->add('typeAction', null, array('label' =>'Type', 'empty_value' => '--- Choisir le type ---'))
// 		        ->add('intervalleInf','date', array(
// 		        		'label' => 'Entre',
// 		        		'widget' => 'single_text',
// 		        		'input'  => 'datetime',
// 		        		'format' => 'dd/MM/yyyy'
// 		        ))
// 		        ->add('intervalleSup','date', array(
// 		        		'label' => 'Et',
// 		        		'widget' => 'single_text',
// 		        		'input'  => 'datetime',
// 		        		'format' => 'dd/MM/yyyy'
// 		        ))
		        ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Action',
        	'validation_groups' => ['form_validation_only']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'statistique_action_criteria';
    }
  
}
