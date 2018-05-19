<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\OrangeMainForms;
use Orange\MainBundle\Criteria\ActionCriteria;

class RecoCriteria extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action', new ActionCriteria())
        	->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning')))
        	->add('effacer', 'submit', array('label' => 'Effacer', 'attr' => array('class' => 'btn btn-danger submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Reco',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reco_criteria';
    }
}

