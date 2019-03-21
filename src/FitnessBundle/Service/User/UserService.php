<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 18:01
 */

namespace FitnessBundle\Service\User;


use FitnessBundle\Entity\User;
use FitnessBundle\Repository\UserRepository;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService implements UserServiceInterface
{

	/** @var UserPasswordEncoderInterface $encoder */
	private $encoder;

	/** @var User $user */
	private $user;

	/** @var UserRepository $userRepo */
	private $userRepo;

	/**
	 * UserService constructor.
	 * @param UserPasswordEncoderInterface $encoder
	 * @param TokenStorageInterface $tokenStorage
	 * @param UserRepository $userRepo
	 */
	public function __construct(UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage, UserRepository $userRepo)
	{
		$this->encoder = $encoder;
		$this->user = $tokenStorage->getToken()->getUser();
		$this->userRepo = $userRepo;
	}


	/**
	 * @param User $user
	 * @return User
	 * @throws Exception
	 */
	public function newProfile(User $user): User
	{
		if (0 === count($user->getRoles())) {
			throw new \Exception('The profile must have at least one role!');
		}

		$password = $this->encoder->encodePassword($user, $user->getPassword());
		$user->setPassword($password);
		$this->userRepo->save($user);

		return $user;
	}

	/**
	 * @param User $user
	 * @return User
	 * @throws Exception
	 */
	public function editProfile(User $user): User
	{

		if (0 === count($user->getRoles())) {
			throw new \Exception('The profile must have at least one role!');
		}

		$user->setUpdatedAt(new \DateTime());
		$this->userRepo->save($user);

		return $user;
	}

	/**
	 * @param FormInterface $form
	 * @param User $user
	 * @return bool
	 * @throws Exception
	 */
	public function changePassword(FormInterface $form, User $user): bool
	{
		$oldPassword = $form->get('old_password')->getData();
		$newPassword = $form->get('new_password')->getData();

		if (!empty($oldPassword) && !empty($newPassword)) {
			if (!$this->encoder->isPasswordValid($user, $oldPassword)) {
				throw new Exception('Wrong password');
			}
			$user->setPassword($this->encoder->encodePassword($user, $newPassword));

			return true;
		}

		return false;
	}


	public function findOneUserById($id): ?User
	{
		return $this->userRepo->findOneById($id);
	}

	public function selectPaginatorAll(): \Doctrine\ORM\QueryBuilder
	{
		return $this->userRepo->selectByIdAscAll();
	}

	public function selectPaginatorWhere($param): \Doctrine\ORM\QueryBuilder
	{
		return $this->userRepo->selectByIdAscWhere($param);
	}
}