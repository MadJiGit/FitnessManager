<?php

namespace FitnessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ScheduleForm extends AbstractType
{

	/** @var Security $security */
	private $security;

	/**
	 * ProfileType constructor.
	 * @param Security $security
	 */
	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder
			->add('activity', ChoiceType::class, array(
				'choices' => [
					'' => '',
					'Fitness' => 'fitness',
					'Group' => 'group',
					'SPA' => 'spa',
					'unlimited' => '1000'
				]));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
//			'data_class' => Card::class
		));
	}

}
