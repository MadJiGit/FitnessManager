<?php

namespace FitnessBundle\Form;

use FitnessBundle\Entity\CardOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FitnessBundle\Entity\Card;

class CardType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('validFrom', DateType::class, array(
				'html5' => false))
			->add('validTo', DateType::class, array(
				'html5' => false));

//		$builder
//			->add('orders', CollectionType::class, [
//				'entry_type' => CardOrderType::class,
//				'entry_options' => ['label' => true]]);

		$builder
				->add('submit', SubmitType::class);



	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Card::class
		));
	}



}
