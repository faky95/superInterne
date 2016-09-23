<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Orange\MainBundle\Repository\InstanceRepository;
use Orange\MainBundle\Entity\Domaine;
use Orange\MainBundle\Repository\DomaineRepository;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Repository\TypeActionRepository;
use Orange\MainBundle\Repository\UtilisateurRepository;

class ActionType extends AbstractType
{
	
    //private $porteur;
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$espace = isset($options['attr']['espace_id']) ? $options['attr']['espace_id'] : null;
		$instance = isset($options['attr']['instance_id']) ? $options['attr']['instance_id'] : null;
        $optionManager = (isset($options['attr']['manager'])) ?  array('choices' => $options['attr']['manager']->getCollaborator()) : array();
        $builder->add('libelle', null, array('label'=>'Nom de l\'action :'))
            ->add('description', null,array('label'=>'Description :') )
            ->add('dateDebut', 'date', array('label' => 'Date de Début :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('porteur', null, array('label'=>'Porteur :', 'empty_value' => '--- Choix Porteur ---'
            ))
            ->add('dateInitial', 'date', array('label' => 'Délai Initial :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('priorite','entity', array('label'=>'Priorite :', 'class'=>'Orange\MainBundle\Entity\Priorite', 'empty_value' => '--- Choix Priorite ---'
            		
            ));
			if($instance){
				$builder->add('instance', null, array(
					'label'=>'Instance :', 'empty_value' => '--- Choisir l\'instance ---',
					'query_builder' => function(InstanceRepository $ir) use($instance){
							$queryBuilder = $ir->createQueryBuilder('i');
							return $queryBuilder->where('i.id='.$instance);
					}
				));
			}else{
				$builder->add('instance', null, array(
					'label'=>'Instance :', 'empty_value' => '--- Choisir l\'instance ---',
					'query_builder' => function(InstanceRepository $ir)use($builder) {
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
				));
			}
			$builder->add('domaine', null, array('label'=>'Domaine :', 'empty_value' => 'Choisissez un domaine ..',
            		'query_builder'=>function(DomaineRepository $do)use($espace){
            		if($espace!=null){
            			$queryBuilder = $do->createQueryBuilder('d');
            			return $queryBuilder->innerJoin('d.instance', 'i')->innerJoin('i.espace', 'e')->where('e.id='.$espace);
            		}
            		else
            			return $do->filter();
            		}))
            ->add('typeAction', null, array('label'=>'Type Action :', 'empty_value' => 'Choisissez un type Action ...',
            		'query_builder'=>function(TypeActionRepository $ta)use($espace){
            		if($espace!=null){
            			$queryBuilder = $ta->createQueryBuilder('t');
            			return $queryBuilder->innerJoin('t.instance', 'i')->innerJoin('i.espace', 'e')->where('e.id='.$espace);
            		}
            		else
            			return $ta->filter();
            		}
            ))
			->add('tmp_contributeur', 'entity', array(
            	 		'multiple' => true, 'class'=>'Orange\MainBundle\Entity\Utilisateur', 
						'label' => 'Contributeur :', 'empty_value' => 'Choisir les contributeurs',
						'query_builder'=>function(UtilisateurRepository $ur)use($espace){
						if($espace!=null)
							return $ur->getMembreEspace($espace);
						else
								return $ur->filter();
						}
            )) ;
		
            $builder->add('groupe', 'entity', array(
            	 		'multiple' => true, 'class'=>'Orange\MainBundle\Entity\Groupe', 'label' => 'Groupes :', 'empty_value' => 'Choisir les groupes',
            ))->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
           ->add('save_and_add', 'submit', array('label' => 'Enregistrer et Ajouter', 'attr' => array('class' => 'btn btn-warning')))
           ->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
            if($builder->getData()==null || $builder->getData()->getId()==null) {
            	$builder->add('erq', new DocumentType());
            }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Orange\MainBundle\Entity\Action'
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
