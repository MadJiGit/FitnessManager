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
		/** @var CardOrder $cardOrder */
		$cardOrder = new CardOrder();

		$card->getOrders()->add($cardOrder);
		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid()) {

			$newCardId = $this->cardService->getNewId();
			$visitLeft = $cardOrder->getVisitsOrder();

			$card->setUser($loggedInUser);
			$card->setUserId($loggedInUserId);
			$card->setCardNumber($newCardId);
			$card->setUpdatedAt(new \DateTime('now'));

			$cardOrder->setVisitsLeft($visitLeft);
			$cardOrder->setCard($card);

			$em = $this->getDoctrine()->getManager();
			$em->persist($cardOrder);
			$em->persist($card);
			$em->flush();

			$this->addFlash('info', 'successful add new card');

			return $this->viewOneCard($card->getId());
		}


		return $this->render('card/add', [
			'card' => $card,
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route ("/card/view_all_cards/{id}", name="view_all_cards")
	 * @param int $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewAllCards(int $id, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($id);
		$isAdminRights = $this->isAdminHere();

		$paginator = $this->get('knp_paginator');

//		if (false === $this->isAdminHere()){
			/** @var Card $card */
			$cards = $paginator->paginate (
				$this->cardService->
				findAllCardsByUserId($id),
			$request->query->getInt('page', 1), 6 );


//			$cards = $paginator->paginate(
//				$this->getDoctrine()
//					->getRepository(Card::class)
//					->selectByIdAsc($id),
//				$request->query->getInt('page', 1), 6
//			);
//		}




		return $this->render('card/view_all_cards', [
			'cards' => $cards,
			'userId' => $id,
		]);


	}


	/**
	 * @Route ("/card/view_one/{id}", name="view_one_card")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewOneCard($id): \Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($id);

		/** @var Card $card */
		$card = $this->cardService->findOneCardById($id);

		return $this->render('card/view_one_card', [
			'card' => $card,
		]);
	}

	/**
	 * @Route ("/card/edit_card/{id}", name="edit_card")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editCard($id, Request $request): ?\Symfony\Component\HttpFoundation\Response
	{
		$this->checkPermission($id);

		/** @var Card $card */
		$card = $this->cardService->findOneCardById($id);

		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			$card->setUpdatedAt(new \DateTime('now'));

			$em = $this->getDoctrine()->getManager();
			$em->persist($card);
			$em->flush();

			$this->addFlash('info', 'successful edit card');

//			$this->cardOrderService->editProfile($order);

			return $this->viewAllCards($id);

		}
		return $this->render('card/edit_card', [
			'form' => $form->createView(),
			'id' => $id,
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

		if ((int)$id === $loggedInUserId) {
			return true;
		}

		$this->addFlash('info', 'You have not permission!!');
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
