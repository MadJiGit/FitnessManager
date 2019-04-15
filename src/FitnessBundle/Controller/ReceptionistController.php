<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\CardType;
use FitnessBundle\Service\Card\CardServiceInterface;
use FitnessBundle\Service\Receptionist\ReceptionistServiceInterface;
use FitnessBundle\Service\FormError\FormErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;


class ReceptionistController extends Controller
{
	/** @var Security $security */
	private $security;

	/** @var ReceptionistServiceInterface $receptionistService */
	private $receptionistService;

	/** @var FormErrorService $formErrorService */
	private $formErrorService;

	/** @var CardServiceInterface $cardService */
	private $cardService;


	/**
	 * CardOrderController constructor.
	 * @param CardServiceInterface $cardService
	 * @param Security $security
	 * @param ReceptionistServiceInterface $receptionistService
	 * @param FormErrorService $formErrorService
	 */
	public function __construct(CardServiceInterface $cardService, Security $security, ReceptionistServiceInterface $receptionistService, FormErrorService $formErrorService)
	{
		$this->security = $security;
		$this->receptionistService = $receptionistService;
		$this->formErrorService = $formErrorService;
		$this->cardService = $cardService;
	}

	/**
	 * @Route ("/receptionist/add_card/{id}", methods={"GET", "POST"}, name="add_new_card_to_user")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */

	public function addCardToUser($id, Request $request)
	{
		/** @var User $loggedInUser */
		$loggedInUser = $this->security->getUser();
		$loggedInUserId = $loggedInUser->getId();

//		$this->checkPermission($loggedInUserId);

		/** @var User $searchedUser */
		$searchedUser = $this->receptionistService->findUserById($id);


//		dump($loggedInUserId);
//		dump($id);
//		exit;

		/** @var Card $card */
		$card = new Card();

		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid()){
			$newCardNumber = $this->cardService->getNewId();

			$card->setUser($searchedUser);
			$card->setUserId($id);
			$card->setCardNumber($newCardNumber);
			$card->setUpdatedAt(new \DateTime('now'));

			$this->cardService->saveCard($card);

			$this->addFlash('info', 'successful add new card');

			return $this->redirectToRoute('view_one_card', ['cardId' => $card->getId()]);

		}

		return $this->render('card/add', [
			'user' => $searchedUser,
			'card' => $card,
			'form' => $form->createView(),
		]);

		
	}

	/**
	 * @Route ("/receptionist/body", name="search_card")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function searchCard(Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$loggedInUser = $this->getUser();
		$loggedInUserId = $loggedInUser->getId();

		$this->checkPermission($loggedInUserId);


//		$form = $this->createForm(ClientForm::class);
//		$form->handleRequest($request);


		$searchedNumber = $request->get('cardNumber');
		$searchedUsername = $request->get('cardUsername');
		$searchedEmail = $request->get('cardEmail');

		$paginator = $this->get('knp_paginator');

		if ('' !== $searchedNumber) {

			if (false === is_numeric($searchedNumber)) {
				$this->addFlash('danger', 'Enter a valid number');
				return $this->render('default/index');
			}

			$cardOwnerId = $this->receptionistService->findIdCardOwnerByCardNumber($searchedNumber);

			/** @var Card $searchedCard */
			$searchedCard = $this->receptionistService->findCardByNumber($searchedNumber);

			if (null === $cardOwnerId){
				$this->addFlash('danger', 'There is not user with that card number');
				return $this->render('default/index');
			}

			return $this->render('card/view_one_card', [
				'card' => $searchedCard,
			]);


		}

		if ('' !== $searchedUsername) {
			if (0 !== preg_match('/[^a-zA-Z0-9_]/', $searchedUsername)) {
				$this->addFlash('danger', 'Enter a valid username');
				return $this->render('default/index');
			}

			$cardOwnerId = $this->receptionistService->findIdCardOwnerByUsername($searchedUsername);

			/** @var Card $searchedCard */
//			$searchedCard = $this->receptionistService->findCardByUsername($searchedUsername);
			$searchedCard = $paginator->paginate(
				$this->receptionistService->
				findCardByUsername($searchedUsername),
				$request->query->getInt('page', 1), 6);

			if (null === $cardOwnerId){
				$this->addFlash('danger', 'There is not user with that username');
				return $this->render('default/index');
			}


			return $this->render('card/view_all_cards', [
				'cards' => $searchedCard,
				'userId' => $cardOwnerId
			]);

		}

		if ('' !== $searchedEmail) {

			if (!filter_var($searchedEmail, FILTER_VALIDATE_EMAIL)){
				$this->addFlash('danger', 'Enter a valid email');
				return $this->render('default/index');
			}

			$cardOwnerId = $this->receptionistService->findIdCardOwnerByEmail($searchedEmail);

			/** @var Card $searchedCard */
//			$searchedCard = $this->receptionistService->findCardByEmail($searchedEmail);

			$searchedCard = $paginator->paginate(
				$this->receptionistService->
				findCardByEmail($searchedEmail),
				$request->query->getInt('page', 1), 6);

			if (null === $cardOwnerId){
				$this->addFlash('danger', 'There is not user with that email');
				return $this->render('default/index');
			}

			return $this->render('card/view_all_cards', [
				'cards' => $searchedCard,
				'userId' => $cardOwnerId
			]);


		}


		$this->addFlash('danger', 'Enter a data!');
		return $this->render('default/index');


	}

	private function checkPermission($id)
	{
		/** @var User $loggedInUser */
		$loggedInUser = $this->security->getUser();
		$loggedInUserId = $loggedInUser->getId();


		if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_RECEPTIONIST'])) {
			return true;
		}

		if ((int)$id === (int)$loggedInUserId) {
			return true;
		}


		$this->addFlash('info', 'You have not permission!!(checkPermission)');
		return $this->redirectToRoute('index');
	}
}
