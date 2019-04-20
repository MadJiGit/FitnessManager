<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-20
 * Time: 13:05
 */

namespace FitnessBundle\Service\Admin;


use FitnessBundle\Entity\User;

interface AdminServiceInterface
{
	public function deleteUser(User $user) :bool;

	public function findOneById(int $id): ?User;

	public function save(User $user) : bool;

	public function findAllUsersByRole($role): ?array;

	public function update(User $trainer);

	public function findUserByEmail($email);

	public function findUserByUsername($username);


}