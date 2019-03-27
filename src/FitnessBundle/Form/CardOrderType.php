<?php

namespace FitnessBundle\Form;

use FitnessBundle\Entity\CardOrder;
use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CardOrderType extends AbstractType
{
//	/** @var Security $security */
//	private $security;
//
//	/**
//	 * ProfileType constructor.
//	 * @param Security $security
//	 */
//	public function __construct(Security $security)
//	{
//		$this->security = $security;
//	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('startDate', DateType::class, array(
				'html5' => false))
			->add('dueDate', DateType::class, array(
				'html5' => false))
			->add('visitsOrder', ChoiceType::class, array(
				'choices' => [
					'' => '',
					'8' => 8,
					'12' => 12,
					'16' => 16,
					'unlimited' => '1000'
				]));
		$builder
			->add('submit', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => CardOrder::class
		));
	}


}
