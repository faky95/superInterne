<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Orange\MainBundle\Repository\InstanceRepository;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Repository\DomaineRepository;
use Orange\MainBundle\Repository\TypeActionRepository;
use Orange\MainBundle\Repository\UtilisateurRepository;

class SignalisationCriteria extends AbstractCriteria
{
	

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
  				'query_builder' => function(UtilisateurRepository $er ) {
   				return $er->createQueryBuilder('q')
    			->innerJoin('q.signalisation', 'sign')
    			;
    			}
   			))
   			->add('dom', 'entity', array('class'=>'Orange\MainBundle\Entity\Domaine',
    			'label' => 'Domaine :',
   				'empty_value' => '--- Choisir le domaine ---',
   				'query_builder' => function(DomaineRepository $er ) {
	   				return $er->createQueryBuilder('q')
	   				          ->innerJoin('q.signalisation', 'sign');
    			}
   			))
   			->add('type', 'entity', array('class'=>'Orange\MainBundle\Entity\TypeAction',
   					'label' => 'Type de la signalisation :',
   					'empty_value' => '--- Choisir le type ---',
   					'query_builder' => function(TypeActionRepository $er ) {
	   					return $er->createQueryBuilder('q')
	   					->innerJoin('q.signalisation', 's');
	   					}
   					))
   			->add('utilisateur', 'entity', array('class'=>'Orange\MainBundle\Entity\Utilisateur',
   					'label' => 'La source :',
   					'required' => false,
   					'empty_value' => '--- Choisir la source ---',
  				'query_builder' => function(UtilisateurRepository $er ) {
   				return $er->createQueryBuilder('q')
   				          ->innerJoin('q.sources', 's')->innerJoin('s.signalisation', 'sign');
    			}
   					))
   			->add('statut', 'entity', array('class' => 'OrangeMainBundle:Statut', 'query_builder' => function(EntityRepository $er) {
   				return $er->createQueryBuilder('s')->where('s.typeStatut = 1')->andWhere('s.display = 1');
   					}, 'label' =>'Statut', 'empty_value' => '--- Choisir le statut ---'
   			))
	    	->add('fromDateConstat', 'date', array('label' => 'Date constat Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
	    	->add('toDateConstat', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
	    	->add('fromDateSignale', 'date', array('label' => 'Date signalé Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
	    	->add('toDateSignale', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
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
