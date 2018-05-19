<?php
namespace Orange\MainBundle\Criteria;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Repository\DomaineRepository;
use Orange\MainBundle\Repository\TypeActionRepository;
use Orange\MainBundle\Repository\UtilisateurRepository;
use Orange\MainBundle\Repository\StructureRepository;
use Orange\MainBundle\Repository\InstanceRepository;
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\MainBundle\Repository\ActionGeneriqueRepository;

class ActionCriteria extends AbstractCriteria
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$espace_id=(isset($options['attr']['espace_id'])) ? $options['attr']['espace_id'] : 0;
    	$structures = (isset($options['attr']['structures'])) ? $options['attr']['structures'] : null;
    	$instances=(isset($options['attr']['instances'])) ? $options['attr']['instances'] : null;
        $builder->add('domaine', null, array('label' =>'Domaine', 'query_builder'=>function (DomaineRepository $dr)use($espace_id) {
	        		return ($espace_id==0) ? $dr->filter() : $dr->getDomainesByEspace($espace_id);
        		}, 'empty_value' => 'Choisir le domaine ...'
        	));
		$builder ->add('instance', null, array('label' =>'Instance', 
				'query_builder'=>function (InstanceRepository $ir)use($instances){
					return ($instances==null) ?  $ir->filter() : $instances;
				}, 'empty_value' => 'Choisir l\'instance ...'
			));
		$builder->add('structure', null, array('label' =>'Structure', 
				      'query_builder'=>function (StructureRepository $sr)use($structures) {
					return ($structures==null) ? $sr->filter() : $structures;
				}, 'empty_value' => 'Choisir la structure ...'
			));
		$builder->add('porteur', null, array('label' =>'Porteur', 'query_builder'=>function (UtilisateurRepository $ur)use($espace_id){
		        		return($espace_id==0) ? $ur->filter() : $ur->getMembreEspace($espace_id);
		       	}, 'empty_value' => 'Choisir le porteur ...'
		    ));
		$builder->add('typeAction', null, array('label' =>'Type', 'query_builder'=>function (TypeActionRepository $tr)use($espace_id) {
						return($espace_id==0) ? $tr->filter() : $tr->getTypesByEspace($espace_id);
				}, 'empty_value' => 'Choisir le type ...'
		    ));
		$builder->add('statut', 'entity', array('class' => 'OrangeMainBundle:Statut', 'query_builder' => function(EntityRepository $er) {
				 	 	return $er->createQueryBuilder('s')->where('s.typeStatut = 2')->andWhere('s.display = 1');
		    	}, 'label' =>'Statut', 'empty_value' => 'Choisir le statut ...'
		    ));
		$builder->add('priorite', null, array('label' =>'Priorité', 'empty_value' => 'Choisir la priorité...'))
            	->add('fromInitial', 'date', array('label' => 'Délai initial Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('toInitial', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
		        ->add('fromDebut', 'date', array('label' => 'Date de debut Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('toDebut', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('fromCloture', 'date', array('label' => 'Date Clôture Du:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('toCloture', 'date', array('label' => 'Au:', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            	->add('instances', 'entity', array('class' => 'OrangeMainBundle:Instance', 'label' => 'Instances', 'multiple'=>true, 'attr' => array('class' => 'select2')
            			,'query_builder' => function(InstanceRepository $ir)use($builder) {
						$instance=$builder->getData() ? $builder->getData()->getInstance() : null;
						if(!$instance || !$instance->getEspace()){
							$data = $parameters = array();
							$queryBuilder = $ir->createQueryBuilder('i');
							$queryBuilder->where($queryBuilder->expr()->in('i.id', $ir->superAdminQueryBuilder($data)->getDQL()))
								->orWhere($queryBuilder->expr()->in('i.id', $ir->adminQueryBuilder($data)->getDQL()))
								->orWhere($queryBuilder->expr()->in('i.id', $ir->animateurQueryBuilder($data)->getDQL()));
							foreach($data as $value) {
								$parameters[$value->getName()] = $value->getValue();
							}
							return $queryBuilder->setParameters($parameters);
						}else{
							return $ir->createQueryBuilder('i')->innerJoin('i.espace', 'e')->where('e.id=:id')->setParameter('id', $instance->getEspace()->getId());
						}
					}
            	))
            	->add('actionsGeneriques', 'entity', array('class' => 'OrangeMainBundle:ActionGenerique', 'label' => 'Actions Génériques', 'multiple'=>true, 'attr' => array('class' => 'select2')
            			,'query_builder' => function(ActionGeneriqueRepository $agr){
            					return $agr->filter();
            			}
            			))
            	->add('hasActionGenerique', 'checkbox', array('label' => 'Rattaché à des actions génériques ?', 'required' => false))
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
        return 'action_criteria';
    }
}
