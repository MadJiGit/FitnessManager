<?php

namespace FitnessBundle\Form;

use FitnessBundle\Entity\Activity;
use FitnessBundle\Entity\User;
use FitnessBundle\Repository\AdminRepository;
use FitnessBundle\Repository\UserRepository;
use FitnessBundle\Service\Activity\ActivityService;
use FitnessBundle\Service\Activity\ActivityServiceInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ActivityType extends AbstractType
{

	/** @var Security $security */
	private $security;


	/** @var UserRepository $userRepo */
	private $userRepo;

	/** @var ActivityServiceInterface $activity */
	private $activity;


	/**
	 * ProfileType constructor.
	 * @param Security $security
	 * @param UserRepository $userRepo
	 */
	public function __construct(Security $security, UserRepository $userRepo, ActivityServiceInterface $activity)
	{
		$this->userRepo = $userRepo;
		$this->security = $security;
		$this->activity = $activity;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

//		$trainers = $options['data']['trainers'];

//		dump($options['data']);
//		exit;

		$builder
			->add('name', ChoiceType::class, array(
				'choices' => [
					'' => '',
					'Fitness' => 'fitness',
					'Group' => 'group',
					'SPA' => 'spa',
				]))
			->add('validFrom', DateType::class, array(
				'html5' => false))
			->add('validTo', DateType::class, array(
				'html5' => false))
			->add('capacity', NumberType::class, array(
				'label' => 'Capacity',
				'attr' => [
					'placeholder' => 'capacity',
					'class' => 'form-control-sm'
				]));


		$builder->add('trainers', EntityType::class, [
					'class' => User::class,
					'choice_label' => 'username',
					'query_builder' => function (UserRepository $repo) {
						return $repo->createQueryBuilder('u')
							// add this to also load the related roles entities
							->addSelect('r')
							// Where roles is your property name in the User entity
							->leftJoin('u.roles', 'r')
							->where('r.name = :roleName')
							->setParameter('roleName', 'ROLE_TRAINER');
					},
			'label' => 'Chose trainer',
			'placeholder' => '',
//			'preferred_choices' => 'trainer',
//			'expanded' => true,
//			'multiple' => true,
					]);

		$builder
			->add('submit', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => Activity::class,
			'custom_val' => '',
			'activity' => null,

		));
	}

}
