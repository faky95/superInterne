<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StructureType extends AbstractType
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$bu = $builder->getData()->getBuPrincipal();
        $builder->add('libelle', null, array('label' => 'Nom de la structure : ', 'attr' => array('placeholder' => 'Ex: SDA')))
        		->add('parent', null, array('label' => 'Structure Parente : ', 'empty_value' => 'Choisir la structure parente ...',
        				'query_builder' => function($er) {
        					return $er->filter();
        				}, 'attr' => array('class' => 'select')
        		))
        		->add('typeStructure', null, array('label' => 'Type de la structure', 'empty_value' => 'Choisir le type de structure', 'attr' => array('class' => 'select')))
				->add('buPrincipal', null, array('label' => 'BU principal', 'empty_value' => 'Choisir le BU principal', 'attr' => array('class' => 'select')))
	            ->add('bu', null, array('label' => 'Choisir les BU associes :', 'empty_value' => 'les bu associés : ', 'by_reference' => false, 'attr' => array('class' => 'select2')
	            ))->add('rapporteurs', null, array_merge(array('label'=>'Choisir les rapporteurs :', 'empty_value' => '--- Choix Rapporteurs ---',
            		'query_builder' => function($er) use ($bu) {
            				$queryBuilder = $er->createQueryBuilder('u');
							return $queryBuilder->innerJoin('u.structure','st')->where('st.buPrincipal= :bu')->setParameter('bu', $bu);
            		}
            	)))
	            ->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
	            ->add('save_and_add', 'submit', array('label' => 'Enregistrer et ajouter', 'attr' => array('class' => 'btn btn-warning')))
	            ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
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
        return 'orange_main_structure';
    }
}



