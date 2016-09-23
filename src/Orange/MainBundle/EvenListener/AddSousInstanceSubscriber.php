<?php

namespace Orange\MainBundle\EvenListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Instance;

class AddSousInstanceSubscriber implements EventSubscriberInterface
{
	private $sousInstance;

	public function __construct($sousInstance)
	{
		$this->sousInstance = $sousInstance;
	}

	public static function getSubscribedEvents()
	{
		return array(
				FormEvents::PRE_SET_DATA  => 'preSetData',
				FormEvents::PRE_SUBMIT    => 'preSubmit'
		);
	}

	private function addSousInstanceToForm($form, $instance_id)
	{
		$formOptions = array(
				'class'         => 'OrangeMainBundle:Instance',
				'empty_value'   => 'Choisir la sous intance',
				'label'         => 'Sous Instance',
				'attr'          => array(
						'class' => 'inst_selector',
				),
				'query_builder' => function (EntityRepository $repository) use ($instance_id) {
					$qb = $repository->createQueryBuilder('i')
					->join('i.parent', 'parent')
					->where('parent.id = :parent')
					->setParameter('parent', $instance_id)
					;

					return $qb;
				}
		);

		$form->add($this->sousInstance, 'entity', $formOptions);
	}

	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if (null === $data) {
			return;
		}
		$accessor    = PropertyAccess::createPropertyAccessor();

		$ss_inst        = $accessor->getValue($data, $this->sousInstance);
		$instance_id = ($ss_inst) ? $ss_inst->getParent()->getId() : null;

		$this->addSousInstanceToForm($form, $instance_id);
	}

	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();
		
		$form = $event->getForm();
		$form->get('instance')->setData(false);
		$instance_id = array_key_exists('parent', $data) ? $data['parent'] : null;

		$this->addSousInstanceToForm($form, $instance_id);
	}
}