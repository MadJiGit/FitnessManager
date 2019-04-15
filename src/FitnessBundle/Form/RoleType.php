<?php

namespace FitnessBundle\Form;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 * @return string
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$temp = $options['attr'];

//		dump($temp);
//		exit;

		$result = explode('_', $temp[0]);
		$role = '';

		switch (count($result)) {
			case 1:
			case 2:
				$test1 = strtolower(end($result));
				$role = ucfirst($test1);
				break;
			case 3:
				$first = strtolower($result[1]);
				$second = strtolower($result[2]);
				$role = ucfirst($first) . ' ' . ucfirst($second);
				break;
			default:
				$role = '';
				break;
		}

		/*
		$builder->add('category', EntityType::class, array(
			'class' => 'AppBundle:Category',
			'choice_value' => function (Category $category = null) {
				return null === $category ? '': $category->getDisplayName();
			},
		));
		*/

//		$builder->add('name', EntityType::class, array(
//			'class' => 'FitnessBundle::Role',
//			'choice_value' => function (Role $role = null) {
//				return null === $role ? '': $role->getDisplayName();
//			},
//		));


		$builder
			->add('name', ChoiceType::class, array(
				'choices' => [
					'' => '',
					'Super Admin' => 'ROLE_SUPER_ADMIN',
					'Admin' => 'ROLE_ADMIN',
					'Office' => 'ROLE_OFFICE',
					'User' => 'ROLE_USER',
					'Receptionist' => 'ROLE_RECEPTIONIST',
					'Client' => 'ROLE_CLIENT',
					'choice_value' => function (User $user = null) {
				return null === $user ? '': $user->getRoleName();
			}
		]));

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Role::class
		));

	}

}
