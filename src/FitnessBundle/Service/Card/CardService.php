<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-02-01
 * Time: 12:17
 */

namespace FitnessBundle\Service\Card;


use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\User;
use FitnessBundle\Repository\CardRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CardService implements CardServiceInterface
{

	/** @var Card $card */
	private $card;

	/** @var CardRepository $cardRepository */
	private $cardRepository;

	/**
	 * CardService constructor.
	 * @param CardRepository $cardRepository
	 * @param TokenStorageInterface $tokenStorage
	 */
	public function __construct(CardRepository $cardRepository, TokenStorageInterface $tokenStorage)
	{
		$this->cardRepository = $cardRepository;
	}


	/**
	 * @param $id
	 * @return object|null
	 */
	public function findOneCardById($id)
	{
		return $this->cardRepository->findOneById($id);

	}


	public function addCard(Card $card)
	{
		$this->cardRepository->addNewCard($card);
	}

	public function getNewId()
	{
		/* for new ID get last one and add 1 */

		return ($this->cardRepository->findLastId() + 1);
	}

	public function selectByIdAsc($id)
	{
		return $this->cardRepository->findById($id);
	}

	public function findAllCardsByUserId($id)
	{
//		return $this->cardRepository->find(['userId' => $id]);
		return $this->cardRepository->findBy(['userId' => $id]);
	}

	public function findAllCards()
	{
		return $this->cardRepository->getAllCards();
	}
}