<?php

namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Orange\MainBundle\Entity\TypeInstance;
use Orange\MainBundle\Repository\UtilisateurRepository;
use Orange\MainBundle\Repository\DomaineRepository;
use Orange\MainBundle\Repository\TypeActionRepository;
use Orange\MainBundle\Repository\InstanceRepository;
use Orange\MainBundle\Repository\StructureRepository;

class InstanceType extends AbstractType
{
	protected $typeInstance;
	
	public function __construct($typeInstance = null) {
		$this->typeInstance = $typeInstance;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$data=array();
        $builder
            ->add('libelle',null, array('label' => 'Libelle :'))
            ->add('couleur', null, array('label' => 'Couleur :'))
            ->add('description',null, array('label' => 'Description :'))
            ->add('tmp_animateur', 'entity', array(
            		'multiple' => true,
            		'class'=>'OrangeMainBundle:Utilisateur',
            		'label' => 'Animateurs :',
            		'query_builder'=>function(UtilisateurRepository $ur){
					          return $ur->filter();
					 }
            ))
            ->add('tmp_source', 'entity', array(
            		'multiple' => true,
            		'class'=>'OrangeMainBundle:Utilisateur',
            		'label' => 'Sources',
            		'empty_value' => 'Choisir les sources',
            		'query_builder'=>function(UtilisateurRepository $ur){
            		return $ur->filter();
            		}
            ))
           ->add('typeInstance', 'entity', array(
	            		 'class'=>'Orange\MainBundle\Entity\TypeInstance',
	            		 'label' => 'Type de l\'instance :',
           				'multiple' => false
	            		))
			->add('domaine', null, array(
						'label' => 'Domaines :',
						'multiple' => true,
						'empty_value' => 'choisir les domaines',
						'class'=>'Orange\MainBundle\Entity\Domaine',
						'query_builder'=>function(DomaineRepository $dr){
							return $dr->filter();
						}
	            		))
			->add('bu', null, array('label' => 'Bu :'))
			->add('typeAction', null, array(
				  		'label' => 'Type d\'actions :',
						'multiple' => true,
						'class'=>'Orange\MainBundle\Entity\TypeAction',
						'query_builder'=>function(TypeActionRepository $tr){
						     return $tr->filter();
						}
	            		))
            ->add('parent', null, array(
            				'label' => 'Instance Parente : ',
            				'attr' => array('class' => 'select'),
            				'empty_value' => 'Choisir l\'instance parente ...',
		            		'query_builder'=>function(InstanceRepository $ir){
		            			return $ir->filter();
		            		}
            				))
			->add('structure', null, array(
            						'multiple' => true,
									'class'=>'Orange\MainBundle\Entity\Structure',
            						'label' => 'Structures :',
            						'empty_value' => 'Choisir les structures',
									'query_builder'=>function(StructureRepository $sr)use ($data){
										return $sr->createQueryBuilder('s');
									}
            				))
            				->add('add', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
            				->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')))
        ;
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
        return 'orange_mainbundle_instance';
    }
}
