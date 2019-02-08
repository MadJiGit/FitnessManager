<?php

namespace FitnessBundle\Form;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('username', TextType::class, [
				'label' => 'Username',
				'attr' => [
					'placeholder' => 'Username',
					'class' => 'form-control-sm'
				]
			])
			->add('email', EmailType::class, [
				'label' => 'Email',
				'attr' => [
					'placeholder' => 'Email',
					'class' => 'form-control-sm'
				]
			])
			->add('password', RepeatedType::class, [
					'type' => PasswordType::class,
					'first_options' => [
						'label' => 'Password',
						'attr' => [
							'placeholder' => 'Password',
						]
					],
					'second_options' => [
						'label' => 'Repeat Password',
						'attr' => [
							'placeholder' => 'Repeat Password',
						]
					]
				]
			)
			->add('role', ChoiceType::class, [
					'choices' => [
						'' => '',
						'Admin' => 'ROLE_ADMIN',
						'Office' => 'ROLE_OFFICE',
						'User' => 'ROLE_USER',
					]
				]
			)
			->add('submit', SubmitType::class);


	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(['data_class' => User::class]);

	}

}
