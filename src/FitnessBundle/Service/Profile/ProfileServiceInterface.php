<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 17:54
 */

namespace FitnessBundle\Service\Profile;


use FitnessBundle\Entity\User;
use Exception;
use Symfony\Component\Form\FormInterface;

/**
 * Interface ProfileServiceInterface
 * @package FitnessBundle\Service\Profile
 */
interface ProfileServiceInterface
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
	 * @return object|null|User
	 */
	public function find($id);


	public function removeAllRoles($userId, $roleId): void;
}
