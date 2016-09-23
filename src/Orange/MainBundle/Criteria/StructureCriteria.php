<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Annotations\Annotation\Required;
use Orange\MainBundle\Repository\StructureRepository;

class StructureCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parent', null, array('label' =>'Structure parente', 'empty_value' => '--- Choisir la structure parente ---',
        		'query_builder'=>function(StructureRepository $sr){
        			return $sr->filter();
        		}
        		, 'property' => 'name'
        		
        ))
        ->add('typeStructure', null, array('label' =>'Type Structure', 'empty_value' => '--- Choisir le type de structure ---', 'required'=> false))
           ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Structure'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'structure_criteria';
    }
}
