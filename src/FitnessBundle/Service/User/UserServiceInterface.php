<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 17:54
 */

namespace FitnessBundle\Service\User;


use FitnessBundle\Entity\User;
use Exception;
use Symfony\Component\Form\FormInterface;

/**
 * Interface UserServiceInterface
 * @package FitnessBundle\Service\Profile
 */
interface UserServiceInterface
{

	/**
	 * @param User $user
	 * @return User
	 * * @throws Exception
	 */
	public function newProfile(User $user): User;

	/**
	 * @param User $user
	 * @return User
	 * @throws Exception
	 */
	public function editProfile(User $user): User;

	/**
	 * @param FormInterface $form
	 * @param User $user
	 * @return bool
	 * @throws Exception
	 */
	public function changePassword(FormInterface $form, User $user): bool;

	/**
	 * @param $id
	 * @return User|null
	 */
	public function findOneUserById($id): ?User;

	public function selectPaginatorAll(): \Doctrine\ORM\QueryBuilder;

	public function selectPaginatorWhere($param): \Doctrine\ORM\QueryBuilder;

}
