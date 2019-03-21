<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 18:12
 */

namespace FitnessBundle\Service\Role;

use FitnessBundle\Entity\Role;

interface RoleServiceInterface
{

	/**
	 * @param array $criteria
	 * @param array|null $orderBy
	 * @return Role|object|null
	 */
	public function findOneBy(array $criteria, array $orderBy = null): ?Role;


	public function removeOne(array $criteria);
}