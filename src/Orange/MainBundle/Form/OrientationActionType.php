<?php
namespace Orange\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Statut;

class OrientationActionType extends AbstractType
{
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('actionGenerique', 'entity', array('label'=>'Action générique : ', 'class'=>'Orange\MainBundle\Entity\ActionGenerique',
					'empty_value' => 'Choisir l\'action ', 'property' => 'libelle', 'required'   => true, 'query_builder' => $this->addQueryBuilderAG($options)
			))
			->add('save', 'submit', array('label' => 'Enregistrer', 'attr' => array('class' => 'btn btn-warning')))
			->add('cancel', 'button', array('label' => 'Annuler', 'attr' => array('class' => 'btn btn-warning cancel', 'data-dismiss'=>'modal')));
    }
    
    private function addQueryBuilderAG($options) {
    	return function(EntityRepository $ir) use($options) {
	    	$user = $options['attr']['user'];
	    	$ids    = $options['attr']['ids'];
	    	$queryBuilder = $ir->createQueryBuilder('a');
	    	$queryBuilder->leftJoin('a.actionGeneriqueHasAction', 'aha')
		    	->where('IDENTITY(a.porteur) = :user')->setParameter('user', $user->getId())
		    	->andWhere('a.statut  = :statut')->setParameter('statut', Statut::ACTION_NON_ECHUE);
	    	if(is_array($ids)==false) {
	    		$queryBuilder->andWhere('IDENTITY(aha.action) != :id or aha.id is null')->setParameter('id', $ids);
	    	} else {
	    		$queryBuilder->andWhere('IDENTITY(aha.action) not in (:id) or aha.id is null')->setParameter('id', $ids);
	    	}
	    	return $queryBuilder;
    	};
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
        return 'orientation_action';
    }
}
