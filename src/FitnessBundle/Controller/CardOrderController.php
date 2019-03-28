<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\CardOrder;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\CardOrderType;
use FitnessBundle\Service\Card\CardServiceInterface;
use FitnessBundle\Service\CardOrder\CardOrderServiceInterface;
use FitnessBundle\Service\FormError\FormErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @property  formErrorService
 */
class CardOrderController extends Controller
{

	/** @var Security $security */
	private $security;

	/** @var CardServiceInterface $cardService */
	private $cardService;


	/** @var CardOrderServiceInterface $cardOrderService */
	private $cardOrderService;

	/** @var FormErrorService $formErrorService */
	private $formErrorService;

	/**
	 * CardOrderController constructor.
	 * @param Security $security
	 * @param CardServiceInterface $cardService
	 * @param CardOrderServiceInterface $cardOrderService
	 * @param FormErrorService $formErrorService
	 */
	public function __construct(Security $security, CardServiceInterface $cardService, CardOrderServiceInterface $cardOrderService, FormErrorService $formErrorService)
	{
		$this->security = $security;
		$this->cardService = $cardService;
		$this->cardOrderService = $cardOrderService;
		$this->formErrorService = $formErrorService;
	}


	/**
	 * @Route ("/order/add/{cardId}", name="add_new_order")
	 * @param $cardId
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addOrder($cardId, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		/** @var Card $card */
		$card = $this->cardService->findOneCardById($cardId);
		$cardIsValid = $card->isValid();

		$ownersUserId = $card->getUserId();

//		$this->checkPermission($ownersUserId);

		if (false === $this->checkPermission($ownersUserId)) {
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		if ($cardIsValid) {
			$order = new CardOrder();
			$form = $this->createForm(CardOrderType::class, $order);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				$visitLeft = $order->getVisitsOrder();
				$order->setCard($card);
				$order->setVisitsLeft($visitLeft);
				$em = $this->getDoctrine()->getManager();
				$em->persist($order);
				$em->flush();

				$this->addFlash('info', 'successful add new order');
				return $this->viewAllOrders($cardId);

			}
			return $this->render('order/add', [
				'form_order' => $form->createView(),
				'card' => $card,
			]);
		}

		$this->addFlash('info', 'Sorry, the validity of your card has expired!');
		return $this->redirectToRoute('index');

	}

	/**
	 * @Route ("/order/view_one_order/{id}", name="view_one_order")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewOneOrder($id): \Symfony\Component\HttpFoundation\Response
	{
		/** @var CardOrder $order */
		$order = $this->cardOrderService->findOneOrderById($id);
		$cardOwner = $order->getCard()->getUser();
		$usernameCardOwner = $cardOwner->getUsername();
		$ownersUserId = $cardOwner->getId();

		if (false === $this->checkPermission($ownersUserId)) {
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		return $this->render('order/view_one_order', [
			'order' => $order,
			'username' => $usernameCardOwner,
		]);
	}


	/**
	 * @Route ("/order/view_all_orders/{cardId}", name="view_all_orders")
	 * @param $cardId
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewAllOrdersByCardId($cardId, Request $request): \Symfony\Component\HttpFoundation\Response
	{

		/** @var Card $card */
		$card = $this->cardService->findOneCardById($cardId);
		$user = $card->getUser();
		$ownersUserId = $user->getId();

		if (false === $this->checkPermission($ownersUserId)) {
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$paginator = $this->get('knp_paginator');

		/** @var CardOrder $orders */
		$orders = $paginator->paginate(
			$this->cardOrderService->
			findAllOrdersByCardId($cardId),
			$request->query->getInt('page', 1), 6);


		return $this->render('order/view_all_orders', [
			'card' => $card,
			'orders' => $orders,
			'user' => $user,
		]);
	}

	/**
	 * @Route ("/order/view_total_orders/", name="view_total_orders")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewTotalOrders(Request $request): \Symfony\Component\HttpFoundation\Response
	{


		if (false === $this->isAdminHere()){

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('view_all_cards');
		}

		$paginator = $this->get('knp_paginator');

			/** @var CardOrder $orders */

			$orders = $paginator->paginate(
				$this->cardOrderService->
				findAllOrders(),
				$request->query->getInt('page', 1), 6);


		return $this->render('order/view_total_orders', [
			'orders' => $orders,
		]);
	}

	/**
	 * @Route ("/order/edit_order/{id}", name="edit_order")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editOrder($id, Request $request): ?\Symfony\Component\HttpFoundation\Response
	{

		/** @var CardOrder $order */
		$order = $this->cardOrderService->findOneOrderById($id);
		$cardId = $order->getCardId();
		$form = $this->createForm(CardOrderType::class, $order);
		$form->handleRequest($request);

		$ownersUserId = $order->getCard()->getUserId();

		if (false === $this->checkPermission($ownersUserId)) {
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			$newVisitsOrder = $form->get('visitsOrder')->getData();
			$order->setVisitsLeft($newVisitsOrder);

			$em = $this->getDoctrine()->getManager();
			$em->persist($order);
			$em->flush();

			$this->addFlash('info', 'successful edit order');

//			$this->cardOrderService->editProfile($order);

			return $this->viewAllOrdersByCardId($cardId, $request);

		}
		return $this->render('order/edit_order', [
			'form' => $form->createView(),
			'id' => $id,
		]);
	}

	/**
	 * @param $orderOwnerUserId
	 * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	private function checkPermission($orderOwnerUserId)
	{
		/** @var User $loggedInUser */
		$loggedInUser = $this->security->getUser();
		$loggedInUserId = $loggedInUser->getId();

		return ((int)$loggedInUserId === (int)$orderOwnerUserId ||
			$loggedInUser->isAdmin() ||
			$loggedInUser->isReceptionist() ||
			$loggedInUser->isSuperAdmin());


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
