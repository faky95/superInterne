<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Repository\InstanceRepository;

class ActionGeneriqueCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    
		$builder ->add('porteur', null, array('label'=>"Porteur", 'empty_value' => '--- Choisir le porteur ---'));
		$builder->add('statut', 'entity', array('class' => 'OrangeMainBundle:Statut', 'query_builder' => function(EntityRepository $er) {
				 	 	return $er->createQueryBuilder('s')->where('s.typeStatut = 2 and s.isGenerique = 1 ')->andWhere('s.display = 1');
		    	}, 'label' =>'Statut', 'empty_value' => '--- Choisir le statut ---'
		    ));
		$builder
            	->add('fromInitial', 'date', array('label' => 'Délai initial Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('toInitial', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
		        ->add('fromDebut', 'date', array('label' => 'Date de debut Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('toDebut', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('toCloture', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('instances', 'entity', array('class' => 'OrangeMainBundle:Instance', 'label' => 'Instances', 'multiple'=>true, 'attr' => array('class' => 'select2')
            			,'query_builder' => function(InstanceRepository $ir)use($builder) {
            				return   $ir->filter();
					}
            	))
		        ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning submitLink')))
		        ->add('effacer', 'submit', array('label' => 'Effacer', 'attr' => array('class' => 'btn btn-danger submitLink')));
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
        return 'actiongenerique_criteria';
    }
}
