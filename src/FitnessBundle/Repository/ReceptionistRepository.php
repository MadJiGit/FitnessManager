<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-28
 * Time: 15:58
 */

namespace FitnessBundle\Repository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use FitnessBundle\Entity\Card;

class ReceptionistRepository extends \Doctrine\ORM\EntityRepository
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
		parent::__construct($em, new ClassMetadata(Card::class));
	}

	public function findCard(int $id)
	{
		return $this->findOneBy(['id' => $id]);
//		dump($this->findOneBy(['id' => $id]));
//		exit;
	}

	public function findByUsername($username): \Doctrine\ORM\QueryBuilder
	{
//		return $this->findBy(['username' => $criteria]);

		return $this->createQueryBuilder('card')
			->select('card')
			->orderBy('card.id', 'ASC')
			->where('card.user.username = :username')
			->setParameter('id', $username);

	}

	public function findByEmail($email)
	{
//		return $this->findBy(['email' => $searchedEmail]);

		return $this->createQueryBuilder('card')
			->select('card')
			->orderBy('card.id', 'ASC')
			->where('card.user.email = :email')
			->setParameter('email', $email);

	}

	public function findUserIdByCardId(int $cardId)
	{

	}


}