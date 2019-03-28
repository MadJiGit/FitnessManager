<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-27
 * Time: 23:07
 */

namespace FitnessBundle\Service\Client;


use FitnessBundle\Entity\User;
use FitnessBundle\Repository\CardRepository;
use FitnessBundle\Repository\ClientRepository;
use FitnessBundle\Repository\UserRepository;

class ClientService implements ClientServiceInterface
{
	/**
	 * @var ClientRepository
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
	 * @param ClientRepository $clientRepository
	 * @param UserRepository $userRepository
	 */
	public function __construct(CLientRepository $clientRepository, UserRepository $userRepository, CardRepository $cardRepository)
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


	public function findCardByUsername($searchedUsername)
	{
		/** @var User $user */
		$user = $this->userRepository->findBy(['username' => $searchedUsername])[0];

//		dump($user);
//		exit;

		$userId = $user->getId();

		return $this->cardRepository->findById($userId);
	}

	public function findCardByEmail($searchedEmail)
	{
		/** @var User $user */
		$user = $this->userRepository->findBy(['email' => $searchedEmail])[0];
		$userId = $user->getId();

//		dump($user);
//		exit;

		return $this->cardRepository->findById($userId);
	}


	public function findCardOwnerByUsername($searchedUsername)
	{
		/** @var User $user */
		$user = $this->userRepository->findBy(['username' => $searchedUsername])[0];

		return  $user->getId();
	}

	public function findCardOwnerByEmail($searchedEmail)
	{
		/** @var User $user */
		$user = $this->userRepository->findBy(['email' => $searchedEmail])[0];

		return  $user->getId();
	}
}