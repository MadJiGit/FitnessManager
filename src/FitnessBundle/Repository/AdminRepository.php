<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-20
 * Time: 13:09
 */

namespace FitnessBundle\Repository;


use FitnessBundle\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FitnessBundle\Entity\User;

class AdminRepository extends \Doctrine\ORM\EntityRepository
{

	/** @var EntityManagerInterface $em */
	private $em;

	/**
	 * CarRepository constructor.
	 * @param EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
		parent::__construct($em, new ClassMetadata(User::class));
	}

	/**
	 * @param User $user
	 * @return bool
	 */
	public function delete(User $user): bool
	{

		try {
			$this->em->remove($user);
			$this->em->flush();

			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function saveUser(User $user): bool
	{
		try {

			$this->em->persist($user);
			$this->em->flush();

			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function findById(int $id): ?User
	{
		try {
			return $this->em->find(User::class, $id);

		} catch (\Exception $e) {

			return null;
		}
	}


}