<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\CardOrder;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\CardOrderType;
use FitnessBundle\Repository\CardRepository;
use FitnessBundle\Service\Card\CardServiceInterface;
use FitnessBundle\Service\CardOrder\CardOrderServiceInterface;
use FitnessBundle\Service\FormError\FormErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
	 * @Route ("/order/add/{id}", name="add_new_order")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addOrder($id, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		/** @var Card $card */
		$card = $this->cardService->findOneCardById($id);
		$cardIsValid = $card->isValid();

		$ownersUserId = $card->getUserId();

		if($this->checkPermission($ownersUserId)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		if ($cardIsValid){
			$order = new CardOrder();
			$form = $this->createForm(CardOrderType::class, $order);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()){

				$visitLeft = $order->getVisitsOrder();
				$order->setCard($card);
				$order->setVisitsLeft($visitLeft);
				$em = $this->getDoctrine()->getManager();
				$em->persist($order);
				$em->flush();

				$this->addFlash('info', 'successful add new order');
				return $this->viewAllOrders($id);

			}
			return $this->render('order/add', [
				'form_order' => $form->createView(),
				'id' => $id,
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
		$user = $order->getCard()->getUser();
		$username = $user->getUsername();

		$ownersUserId = $user->getId();

		if($this->checkPermission($ownersUserId)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		return $this->render('order/view_one_order', [
			'order' => $order,
			'username' => $username,
		]);
	}

	/**
	 * @Route ("/order/view_all_orders/{id}", name="view_all_orders")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewAllOrders($id): \Symfony\Component\HttpFoundation\Response
	{
		/** @var Card $card */
		$card = $this->cardService->findOneCardById($id);

		/** @var CardOrder[] $orders */
		$orders = $card->getAllOrders();
		$user = $card->getUser();

		$ownersUserId = $user->getId();

		if($this->checkPermission($ownersUserId)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}



		return $this->render('order/view_all_orders', [
			'orders' => $orders,
			'user' => $user,
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

		if($this->checkPermission($ownersUserId)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()){

			$newVisitsOrder = $form->get('visitsOrder')->getData();
			$order->setVisitsLeft($newVisitsOrder);

			$em = $this->getDoctrine()->getManager();
			$em->persist($order);
			$em->flush();

			$this->addFlash('info', 'successful edit order');

//			$this->cardOrderService->editProfile($order);

			return $this->viewAllOrders($cardId);

		}
		return $this->render('order/edit_order', [
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
		/** @var User $currentUser */
		$currentUser = $this->security->getUser();
		$currentUserId = $currentUser->getId();

		return ((int)$currentUserId !== (int)$id ||
			$currentUser->isAdmin() ||
			$currentUser->isOffice()) ||
			$currentUser->isSuperAdmin();
	}
}
