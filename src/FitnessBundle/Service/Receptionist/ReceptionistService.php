<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-27
 * Time: 23:07
 */

namespace FitnessBundle\Service\Receptionist;


use FitnessBundle\Entity\User;
use FitnessBundle\Repository\CardRepository;
use FitnessBundle\Repository\ReceptionistRepository;
use FitnessBundle\Repository\UserRepository;

class ReceptionistService implements ReceptionistServiceInterface
{
	/**
	 * @var ReceptionistRepository
	 */
	private $clientRepository;


	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var CardRepository
	 */
	private $cardRepository;


	/**
	 * AdminService constructor.
	 * @param ReceptionistRepository $clientRepository
	 * @param UserRepository $userRepository
	 */
	public function __construct(ReceptionistRepository $clientRepository, UserRepository $userRepository, CardRepository $cardRepository)
	{
		$this->clientRepository = $clientRepository;
		$this->userRepository = $userRepository;
		$this->cardRepository = $cardRepository;
	}

	public function findCardByNumber(string $searchedNumber)
	{
		$id = (int)ltrim($searchedNumber, '0');

		return $this->clientRepository->findCard($id);
	}


	public function findCardByUsername(string $searchedUsername)
	{
		$userId = $this->userRepository->findByCriteriaUsername($searchedUsername);

		return $this->cardRepository->findById($userId);
	}

	public function findCardByEmail(string $searchedEmail)
	{

		$userId = $this->userRepository->findByCriteriaEmail($searchedEmail);

		return $this->cardRepository->findById($userId);
	}


	public function findIdCardOwnerByUsername(string $searchedUsername)
	{

		return $this->userRepository->findByCriteriaUsername($searchedUsername);

	}

	public function findIdCardOwnerByEmail(string $searchedEmail)
	{
		return $this->userRepository->findByCriteriaEmail($searchedEmail);

	}

	public function findIdCardOwnerByCardNumber(string $searchedNumber)
	{
		$cardId = (int)ltrim($searchedNumber, '0');


		$card = $this->cardRepository->findOneById($cardId);

		if (null === $card) {

			return null;
		}

		return $card->getUserId();

	}

	public function findUserById(int $id)
	{
		return $this->userRepository->findOneById($id);
	}
}