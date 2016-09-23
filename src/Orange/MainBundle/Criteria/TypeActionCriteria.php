<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TypeActionCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type')
           ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\TypeAction'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'type_action_criteria';
    }
}
