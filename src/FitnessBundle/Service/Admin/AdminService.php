<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-20
 * Time: 13:07
 */

namespace FitnessBundle\Service\Admin;


use FitnessBundle\Entity\User;
use FitnessBundle\Repository\AdminRepository;

class AdminService implements AdminServiceInterface
{
	/**
	 * @var AdminRepository
	 */
	private $adminRepository;

	/**
	 * AdminService constructor.
	 * @param AdminRepository $adminRepository
	 */
	public function __construct(AdminRepository $adminRepository)
	{
		$this->adminRepository = $adminRepository;
	}

	/**
	 * @param User $user
	 * @return bool
	 */
	public function deleteUser(User $user): bool
	{
		return $this->adminRepository->delete($user);
	}

	/**
	 * @param int $id
	 * @return User|object
	 */
	public function findOneById(int $id): ?User
	{
//		return $this->adminRepository->findById($id);
		return $this->adminRepository->findById($id);
	}


	/**
	 * @param User $user
	 * @return bool
	 */
	public function save(User $user): bool
	{
		return $this->adminRepository->saveUser($user);
	}


}