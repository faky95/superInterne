<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Orange\MainBundle\Repository\InstanceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContext;
use Orange\MainBundle\Repository\DomaineRepository;
use Orange\MainBundle\Repository\TypeActionRepository;
use Orange\MainBundle\Repository\UtilisateurRepository;
use Orange\MainBundle\Entity\InstanceHasDomaine;
use Orange\MainBundle\Repository\InstanceHasDomaineRepository;
use Orange\MainBundle\Entity\InstanceHasTypeAction;
use Orange\MainBundle\Repository\InstanceHasTypeActionRepository;

class SignalisationCriteria extends AbstractCriteria
{
	

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$user =(isset($options['attr']['user']))?$options['attr']['user']:null;
		$structure_id = $user->getStructure()->getId();
		$ids=(isset($options['attr']['ids']))?$options['attr']['ids']:null;
		
    	$builder
    		->add('perimetre', 'entity', array('class'=>'Orange\MainBundle\Entity\Instance',
    			'label' => 'Périmètre :',
    			'empty_value' => '--- Choisir le périmètre ---',
    			'query_builder' => function(InstanceRepository $er ) {
    			return $er->createQueryBuilder('i')
    			->where('i.parent = 55 OR i.parent = 139');
    			}
   			))
    		->add('constat', 'entity', array('class'=>'Orange\MainBundle\Entity\Utilisateur',
    			'label' => 'Constat fait par :',
   				'empty_value' => '--- Choisir le constatateur ---',
  				'query_builder' => function(EntityRepository $er ) use ($structure_id) {
   				return $er->createQueryBuilder('q')
    			->innerJoin('q.structure', 'str')
    			->where('str.id = ?1')
   				->setParameter(1, $structure_id);
    			}
   			))
   			->add('dom', 'entity', array('class'=>'Orange\MainBundle\Entity\InstanceHasDomaine',
    			'label' => 'Domaine :',
   				'empty_value' => '--- Choisir le domaine ---',
    			'query_builder' => function(InstanceHasDomaineRepository $er ) {
    			return $er->createQueryBuilder('dom')
    			->where('dom.instance = 55 OR dom.instance = 139');
    			}
   			))
   			->add('type', 'entity', array('class'=>'Orange\MainBundle\Entity\InstanceHasTypeAction',
   					'label' => 'Type:',
   					'empty_value' => '--- Choisir le type ---',
    			'query_builder' => function(InstanceHasTypeActionRepository $er ) {
    			return $er->createQueryBuilder('ty')
    			->where('ty.instance = 55 OR ty.instance = 139');
    			}
   					))
   			->add('utilisateur', 'entity', array('class'=>'Orange\MainBundle\Entity\Utilisateur',
   					'label' => 'La souce :',
   					'required' => false,
   					'empty_value' => '--- Choisir la source ---',
  				'query_builder' => function(UtilisateurRepository $er ) use ($ids) {
   				return $er->createQueryBuilder('q')
   				->where('q.id IN (:ids)')->setParameters(array('ids' => $ids));
    			}
   					))
   			->add('statut', 'entity', array('class' => 'OrangeMainBundle:Statut', 'query_builder' => function(EntityRepository $er) {
   				return $er->createQueryBuilder('s')->where('s.typeStatut = 1')->andWhere('s.display = 1');
   					}, 'label' =>'Statut', 'empty_value' => '--- Choisir le statut ---'
   			))
	    	->add('fromDateConstat', 'date', array('label' => 'Date constat Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
	    	->add('toDateConstat', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
	    	->add('filtrer', 'submit', array('label' => 'Filtrer', 'attr' => array('class' => 'btn btn-warning submitLink')))
	        ->add('effacer', 'submit', array('label' => 'Effacer', 'attr' => array('class' => 'btn btn-danger submitLink')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Signalisation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'signalisation_criteria';
    }
}
