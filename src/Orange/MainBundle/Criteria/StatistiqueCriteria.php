<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Orange\MainBundle\Repository\StructureRepository;
use Orange\MainBundle\Repository\InstanceRepository;

class StatistiqueCriteria extends AbstractCriteria
{
	
	
	private $securityContext;
	
	public function __construct(SecurityContext $securityContext)
	{
		$this->securityContext = $securityContext;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$structures=(isset($options['attr']['structures']))?$options['attr']['structures']:null;
    	$instances=(isset($options['attr']['instances']))?$options['attr']['instances']:null;
    	$week = array();
    	for($i=1;$i<=52;$i++){
    		$week[$i] = sprintf('S%02d', $i);
    	}
        $builder->add('domaine', null, array('label' =>'Domaine'))
        		->add('typeAction', null, array('label' =>'Type action'))
		        ->add('utilisateur', null, array('label' =>'Porteur'))
		        ->add('structure', null, array('label' =>'Structure'))
		        ->add('semaine', 'choice', array('label' =>'Semaine', 'choices'=> $week))
		        ->add('instances', 'entity', array('label' => 'Instances', 'multiple'=>true,'class' => 'OrangeMainBundle:Instance', 'attr' => array('class' => 'select2')))
		        ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Statistique'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'statistique_criteria';
    }
}
