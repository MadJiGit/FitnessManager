<?php

namespace FitnessBundle\Repository;

use FitnessBundle\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * CardRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CardRepository extends \Doctrine\ORM\EntityRepository
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


	/**
	 * @param $id
	 * @return object|null
	 */
	public function findOneById($id)
	{

		try{

			return $this->find($id);
		}catch (\Exception $e){

			return null;
		}
	}

	public function addNewCard(Card $card): void
	{
		$this->save($card);
	}

	private function save($data): ?bool
	{
		try{
			$this->em->persist($data);
			$this->em->flush();

			return true;
		}catch (\Exception $e){

			return false;
		}
	}

	public function findLastId()
	{
		$qb = $this->createQueryBuilder('cards')
		->setMaxResults(1)
			->orderBy('cards.id', 'DESC');

		/** @var Card $lastCard */
		try {
			$lastCard = $qb->getQuery()->getOneOrNullResult();
			if ($lastCard){
				return $lastCard->getId();
			}
			return 0;
		} catch (NonUniqueResultException $e) {
			return 0;
		}

	}

	public function findById($id): \Doctrine\ORM\QueryBuilder
	{
		return $this->createQueryBuilder('card')
			->select('card')
			->orderBy('card.userId', 'ASC')
			->where('card.userId = :id')
			->setParameter('id', $id);
	}

	public function getAllCards(): \Doctrine\ORM\QueryBuilder
	{
		return $this->createQueryBuilder('card')
			->select('card')
			->orderBy('card.cardNumber', 'ASC');
	}
}

//			$cards = $paginator->paginate(
//				$this->getDoctrine()
//					->getRepository(Card::class)
//					->selectByIdAsc($id),
//				$request->query->getInt('page', 1), 6
//			);
//		}