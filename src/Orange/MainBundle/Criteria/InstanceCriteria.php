<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstanceCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle')
           ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Instance'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'instance_criteria';
    }
}
