<?php
namespace Orange\MainBundle\EvenListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Instance;

class AddPorteurSubscriber implements EventSubscriberInterface
{
	private $porteur;

	public function __construct($porteur)
	{
		$this->porteur = $porteur;
	}

	public static function getSubscribedEvents()
	{
		return array(
				FormEvents::PRE_SET_DATA  => 'preSetData',
				FormEvents::PRE_SUBMIT    => 'preSubmit',
		);
	}

	private function AddPorteurToForm($form, $instance_id)
	{
		$formOptions = array(
				'class'         => 'OrangeMainBundle:Utilisateur',
				'empty_value'   => 'Choisir le porteur',
				'label'         => 'Porteur:',
				'attr'          => array(
						'class' => 'port_selector',
				),
				'query_builder' => function (EntityRepository $repository) use ($instance_id) {
					$qb = $repository->createQueryBuilder('u')
									 ->join('u.structure', 's')
									 ->add('from', 'OrangeMainBundle:Structure s1',true)
									 ->join('s1.instance', 'i1')
									 ->where('i1.id=:instance_id')->setParameter('instance_id', $instance_id)
									 ->andWhere('s.id =s1.id ')
									 ->andWhere('s.lvl >= s1.lvl')
									 ->andWhere('s.root = s1.root')
									 ->andWhere('s.lft  >= s1.lft')
									 ->andWhere('s.rgt <= s1.rgt')
					;
					return $qb;
				}
		);
		$form->add($this->porteur, 'entity', $formOptions);
	}

	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();	
		$data = $event->getData();

		if (null === $data) {
			return;
		}
	//	$accessor    = PropertyAccess::createPropertyAccessor();
	   
		$instance_id=$form->get('instance')->getData();
		$this->AddPorteurToForm($form, $instance_id);
	}

	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();
		$form->get('instance')->setData(false);
		$instance_id = array_key_exists('parent', $data) ? $data['parent'] : null;
		$this->AddPorteurToForm($form, $instance_id);
	}
}