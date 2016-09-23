<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Repository\UtilisateurRepository;
use Orange\MainBundle\Entity\Structure;
use Orange\MainBundle\Repository\StructureRepository;

class UtilisateurCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        		->add('structure', null, array('label'=>'Structure', 'empty_value' => '--- Choisir la structure ---',
									'query_builder'=>function(StructureRepository $sr){
										return $sr->filter();
									}
        				, 'property' => 'name'
        		))
         	    ->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Utilisateur'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'utilisateur_criteria';
    }
}
