<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 18:14
 */

namespace FitnessBundle\Service\Role;


use FitnessBundle\Entity\Role;
use FitnessBundle\Repository\RoleRepository;

class RoleService implements RoleServiceInterface
{
	/** @var RoleRepository $roleRepo */
	private $roleRepo;

	/**
	 * RoleService constructor.
	 * @param RoleRepository $roleRepo
	 */
	public function __construct(RoleRepository $roleRepo)
	{
		$this->roleRepo = $roleRepo;
	}

	/**
	 * @param array $criteria
	 * @param array|null $orderBy
	 * @return Role|object|null
	 */
	public function findOneBy(array $criteria, array $orderBy = null)
	{
		return $this->roleRepo->findOneBy($criteria, $orderBy);
	}


	/**
	 * @param array $criteria
	 * @return
	 */
	public function removeRoles(array $criteria)
	{
		return $this->roleRepo->removeAllRoles($criteria);
	}

	public function removeOne(array $criteria)
	{
		// TODO: Implement removeOne() method.
	}
}