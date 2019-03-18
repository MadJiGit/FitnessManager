<?php
declare(strict_types=1);

namespace FitnessBundle\Form;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class ProfileType
 * @package FitnessBundle\Form
 */
class ProfileType extends AbstractType
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

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		/** @var User $currentUser */
		$currentUser = $options['user'];

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
			]);
//			->add('submit', SubmitType::class);

		$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($currentUser) {
			/** @var User $user */
			$user = $event->getData();
			$form = $event->getForm();

			if (!$user || null === $user->getId()) {
				$form->add('password', RepeatedType::class, [
						'type' => PasswordType::class,
						'invalid_message' => 'Password do not match.',
						'first_options' => [
							'label' => 'Password',
							'attr' => [
								'placeholder' => 'Password',
								'class' => 'form-control-sm'
							],
						],
						'second_options' => [
							'label' => 'Repeat password',
							'attr' => [
								'placeholder' => 'Repeat password',
								'class' => 'form-control-sm'
							]
						],
					]
				);
			} else {
				$form->add('old_password', PasswordType::class, [
					'mapped' => false,
					'label' => 'Old password',
					'attr' => [
						'placeholder' => 'Old password',
						'class' => 'form-control-sm'
					]
				])
					->add('new_password', PasswordType::class, [
						'mapped' => false,
						'label' => 'New password',
						'attr' => [
							'placeholder' => 'New password',
							'class' => 'form-control-sm'
						],
						'constraints' => [

						]
					]);
			}


			if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'])
//				&& $user->getId() !== $currentUser->getId()
			) {
				$form
//					 it's work
					->add('roles', EntityType::class, [
					'class' => Role::class,
					'choice_label' => 'name',
					'placeholder' => 'Select one role',
					])

//					->add('role', ChoiceType::class, [
//							'choices' => [
//								'' => '',
//								'Admin' => 'ROLE_ADMIN',
//								'Office' => 'ROLE_OFFICE',
//								'User' => 'ROLE_USER',
//							]
//						]
//					)

					->add('enabled', CheckboxType::class, [
						'label' => 'Is user is active?',
						'required' => false,
					]);
			}

			$form->add('submit', SubmitType::class);
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => User::class,
			'user' => null,
			'validation_groups' => function (FormInterface $form) {
				/** @var User $user */
				$user = $form->getData();
//                $oldPassword = $form->get('old_password')->getData();
//                $newPassword = $form->get('new_password')->getData();
				if (!$user || null === $user->getId()) {
					return ['registration'];
				}
				return [];
			},
		]);
	}
}