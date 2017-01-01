<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Orange\MainBundle\Repository\InstanceRepository;
use Orange\MainBundle\Entity\Domaine;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\OrangeMainForms;

class ActionType extends AbstractType
{

	/**
	 * @var string
	 */
	private $action;
	
	/**
	 * @var number
	 */
	private $espaceId;
	
	public function __construct($action = null) {
		$this->action = $action;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$instance = $builder->getData()->getInstance();
    	if(isset($options['attr']['espace_id']) && $options['attr']['espace_id']) {
    		$this->action = OrangeMainForms::ACTION_ESPACE;
    		$this->espaceId = $options['attr']['espace_id'];
    	} elseif($instance && $instance->getEspace()) {
    		$this->action = OrangeMainForms::ACTION_ESPACE;
    		$this->espaceId = $instance->getEspace()->getId();
    	} else {
    		$this->action = OrangeMainForms::ACTION_BU;
    	}
    	if($this->action==OrangeMainForms::ACTION_BU) {
    		$this->buildFormForBu($builder, $options);
    	} elseif($this->action==OrangeMainForms::ACTION_ESPACE) {
    		$this->buildFormForEspace($builder, $options);
    	} elseif($this->action==OrangeMainForms::ACTION_PROJET) {
    		$this->buildFormForProjet($builder, $options);
    	} elseif($this->action==OrangeMainForms::ACTION_REAFFECTATION) {
    		$this->buildFormForReaffectation($builder, $options);
    	}
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildMainForm(FormBuilderInterface $builder, array $options) {
    	$builder->add('libelle', null, array('label'=>'Nom de l\'action :'))
            ->add('description', null,array('label'=>'Description :'))
            ->add('groupe', null, array('multiple' => true, 'label' => 'Groupes :', 'empty_value' => 'Choisir les groupes'))
            ->add('dateDebut', 'date', array('label' => 'Date de Début :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('dateInitial', 'date', array('label' => 'Date fin :', 'widget' => 'single_text', 'input'  => 'datetime', 'format' => 'dd/MM/yyyy'))
            ->add('priorite', null, array('label'=>'Priorite :', 'empty_value' => 'Choisir une priorité ...'))
            ->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
           	->add('save_and_add', 'submit', array('label' => 'Enregistrer et Ajouter', 'attr' => array('class' => 'btn btn-warning')))
           	->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
        if($builder->getData()==null || $builder->getData()->getId()==null) {
           	$builder->add('erq', new DocumentType());
        }
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildFormForBu(FormBuilderInterface $builder, array $options) {
    	$this->buildMainForm($builder, $options);
		$instance = isset($options['attr']['instance_id']) ? $options['attr']['instance_id'] : null;
		if($instance) {
			$builder->add('instance', null, array('label'=>'Instance :', 'query_builder' => function($er) use($instance) {
						return $er->createQueryBuilder('i')->where('i.id='.$instance);
				}
			));
		} else {
			$builder->add('instance', null, array('label'=>'Instance :', 'query_builder' => function($er) use ($builder) {
						$data = $parameters = array();
						$queryBuilder = $er->createQueryBuilder('i');
						$queryBuilder->where($queryBuilder->expr()->in('i.id', $er->superAdminQueryBuilder($data)->getDQL()))
							->orWhere($queryBuilder->expr()->in('i.id', $er->adminQueryBuilder($data)->getDQL()))
							->orWhere($queryBuilder->expr()->in('i.id', $er->animateurQueryBuilder($data, true)->getDQL()));
						foreach($data as $value) {
							$parameters[$value->getName()] = $value->getValue();
						}
						return $queryBuilder->setParameters($parameters);
					}, 'empty_value' => "Choisir l'instance ..."
			));
		};
		$builder->add('domaine', null, array('label'=>'Domaine :', 'query_builder'=>function($er) {
            			return $er->filter();
            		}, 'empty_value' => 'Choisissez un domaine ...'))
            ->add('typeAction', null, array('label'=>"Type d'action :", 'query_builder'=>function($er) {
            			return $er->filter();
            		}, 'empty_value' => "Choisissez un type d'action ..."
            ))
            ->add('porteur', null, array('label'=>'Porteur :', 'query_builder' => function($er) use($options) {
            		    return $er->filter();
        			}, 'empty_value' => 'Choix le porteur ...'
            ))
			->add('tmp_contributeur', 'entity', array('multiple' => true, 'label' => 'Contributeur :', 'query_builder' => function($er) {
						return $er->filter();
					}, 'empty_value' => 'Choisir les contributeurs', 'class'=>'Orange\MainBundle\Entity\Utilisateur'
            ));
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildFormForEspace(FormBuilderInterface $builder, array $options) {
    	$espace = $this->espaceId;
    	$this->buildMainForm($builder, $options);
		$builder->add('instance', null, array('label'=>'Instance :', 'query_builder' => function($er) use ($espace) {
						return $er->createQueryBuilder('i')->innerJoin('i.espace', 'e')->where('e.id = :id')->where('e.id = '.$espace);
					}, 'empty_value' => "Choisir l'instance ..."
			))
			->add('domaine', null, array('label'=>'Domaine :', 'query_builder'=>function($er) use ($espace) {
        			$queryBuilder = $er->createQueryBuilder('d');
           			return $queryBuilder->innerJoin('d.instance', 'i')->innerJoin('i.espace', 'e')->where('e.id='.$espace);
           		}, 'empty_value' => 'Choisissez un domaine ...'
           	))
            ->add('typeAction', null, array('label'=>"Type d'action :", 'query_builder'=>function($er) use ($espace) {
            			$queryBuilder = $er->createQueryBuilder('t');
            			return $queryBuilder->innerJoin('t.instance', 'i')->innerJoin('i.espace', 'e')->where('e.id='.$espace);
            		}, 'empty_value' => "Choisissez un type d'action ..."
            ))
            ->add('porteur', null, array('label'=>'Porteur :', 'query_builder' => function($er) use($options, $espace) {
            		    return $er->getMembreEspace($espace);
					}, 'empty_value' => 'Choix le porteur ...'
            ))
			->add('tmp_contributeur', 'entity', array('label' => 'Contributeur :', 'query_builder'=>function($er) use ($espace) {
						return $er->getMembreEspace($espace);
					}, 'empty_value' => 'Choisir les contributeurs', 'multiple' => true, 'class'=>'Orange\MainBundle\Entity\Utilisateur'
            ));
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildFormForProjet(FormBuilderInterface $builder, array $options) {
    	$this->buildMainForm($builder, $options);
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private function buildFormForReaffectation(FormBuilderInterface $builder, array $options) {
        $builder->add('porteur', null, array('label'=>'Porteur :', 'query_builder' => function($er) use($options) {
        			return $er->managerQueryBuilder(array(), true)->select('u4');
        		}, 'empty_value' => 'Choisir un porteur ...'
            ))
            ->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
           	->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel')));
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
