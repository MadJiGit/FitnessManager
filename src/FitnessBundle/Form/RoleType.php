<?php

namespace FitnessBundle\Form;

use FitnessBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', ChoiceType::class, array(
				'choices' => [
					'' => '',
					'ROLE_SUPER_ADMIN' => 'Super Admin',
					'ROLE_ADMIN' => 'Admin',
					'ROLE_OFFICE' => 'Office',
					'ROLE_USER' => 'User'
				]));

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Role::class
		));

	}

}
