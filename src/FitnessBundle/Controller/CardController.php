<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\CardOrder;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\CardOrderType;
use FitnessBundle\Form\CardType;
use FitnessBundle\Service\Card\CardService;
use FitnessBundle\Service\Card\CardServiceInterface;
use FitnessBundle\Service\FormError\FormErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CardController extends Controller
{
	/** @var Security $security */
	private $security;

	/** @var CardServiceInterface $cardService */
	private $cardService;

	/** @var FormErrorService $formErrorService */
	private $formErrorService;

	/**
	 * CardOrderController constructor.
	 * @param Security $security
	 * @param CardService $cardService
	 * @param FormErrorService $formErrorService
	 */
	public function __construct(Security $security, CardService $cardService, FormErrorService $formErrorService)
	{
		$this->security = $security;
		$this->cardService = $cardService;
		$this->formErrorService = $formErrorService;

	}


	/**
	 * @Route ("/card/add/{id}", methods={"GET", "POST"}, name="add_new_card")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */

	public function addCard($id, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($id);

		/** @var User $loggedInUser */
		$loggedInUser = $this->security->getUser();
		$loggedInUserId = $loggedInUser->getId();

		/** @var Card $card */
		$card = new Card();
//		/** @var CardOrder $cardOrder */
//		$cardOrder = new CardOrder();

//		$card->getOrders()->add($cardOrder);
		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid()) {

			$newCardNumber = $this->cardService->getNewId();
//			$visitLeft = $cardOrder->getVisitsOrder();

			$card->setUser($loggedInUser);
			$card->setUserId($loggedInUserId);
			$card->setCardNumber($newCardNumber);
			$card->setUpdatedAt(new \DateTime('now'));

//			$cardOrder->setVisitsLeft($visitLeft);
//			$cardOrder->setCard($card);

			$this->cardService->saveCard($card);

//			$em = $this->getDoctrine()->getManager();
////			$em->persist($cardOrder);
//			$em->persist($card);
//			$em->flush();

			$this->addFlash('info', 'successful add new card');

//			return $this->viewOneCard($card->getId());

			return $this->redirectToRoute('view_one_card', ['cardId' => $card->getId()]);
		}


		return $this->render('card/add', [
			'user' => $loggedInUser,
			'card' => $card,
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route ("/card/view_all_cards/{id}", methods={"GET", "POST"}, name="view_all_cards")
	 * @param int $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewAllCards(int $id, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($id);
		$isAdminRights = $this->isAdminHere();

		$paginator = $this->get('knp_paginator');

		if (false === $isAdminRights) {
			/** @var Card $card */
			$cards = $paginator->paginate(
				$this->cardService->
				findAllCardsByUserId($id),
				$request->query->getInt('page', 1), 6);

		} else {
			$cards = $paginator->paginate(
//				$this->cardService->
//				findAllCards(),
//				$request->query->getInt('page', 1), 6);
				$this->getDoctrine()
				->getRepository(Card::class)
				->getAllCards(),
				$request->query->getInt('page', 1), 6);

		}

		return $this->render('card/view_all_cards', [
			'cards' => $cards,
			'userId' => $id,
		]);


	}


	/**
	 * @Route ("/card/view_one/{cardId}", methods={"GET", "POST"}, name="view_one_card")
	 * @param $cardId
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewOneCard($cardId): \Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($cardId);

		/** @var Card $card */
		$card = $this->cardService->findOneCardById($cardId);

		return $this->render('card/view_one_card', [
			'card' => $card,
		]);
	}


	/**
	 * @Route ("/card/edit_card/{cardId}", methods={"GET", "POST"}, name="edit_card")
	 * @param $cardId
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editCard($cardId, Request $request): ?\Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($cardId);

		/** @var Card $card */
		$card = $this->cardService->findOneCardById($cardId);

		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			$card->setUpdatedAt();
			$card->setValidFrom($form->get('validFrom')->getData());
			$card->setValidTo($form->get('validTo')->getData());

			$em = $this->getDoctrine()->getManager();
			$em->persist($card);

			$em->flush();

			$this->addFlash('info', 'successful edit card');

			return $this->viewOneCard($cardId);

		}


		return $this->render('card/edit_card', [
			'form' => $form->createView(),
			'id' => $cardId,
		]);
	}

	/**
	 * @param $id
	 * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
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

	/**
	 * @return bool
	 */
	private function isAdminHere(): bool
	{
		if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_RECEPTIONIST'])) {
			return true;
		}

		return false;
	}
}
