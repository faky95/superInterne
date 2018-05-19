<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ActionChangeType extends AbstractType
{
	
    //private $porteur;
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
			$builder->add('statutChange','entity', array('label'=>'Statut : ', 'class'=>'Orange\MainBundle\Entity\Statut', 
					'empty_value' => 'Changer le Statut', 'property' => 'statut', 'query_builder' => function(EntityRepository $ir){
						return $ir->createQueryBuilder('s')->where('s.change= 1');
					}
			))
			->add('dateFinExecut', 'date', array(
					'label' => 'Date',
					'widget' => 'single_text',
					'input'  => 'datetime',
					'format' => 'dd/MM/yyyy',
					'attr' => array('class' => 'datepicker'),
			))
			
			->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
			->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel', 'data-dismiss'=>'modal')));
				
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
	            'data_class' => 'Orange\MainBundle\Entity\Action',
	        	'validation_groups' => array('change_statut', 'Default')
	        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orange_mainbundle_action';
    }
}
