<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\CardOrder;
use FitnessBundle\Entity\User;
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

		/** @var User $currentUser */
		$currentUser = $this->security->getUser();
		$currentUserId = $currentUser->getId();

		if ($this->checkPermission($id)) {
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		/** @var Card $card */
		$card = new Card();
		$cardOrder = new CardOrder();
		$card->getOrders()->add($cardOrder);
		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid()) {
			$newCardId = $this->cardService->getNewId();
			$visitLeft = $cardOrder->getVisitsOrder();

			$card->setUser($currentUser);
			$card->setUserId($currentUserId);
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
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route ("/card/view_all_cards/{id}", name="view_all_cards")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewAllCards($id): \Symfony\Component\HttpFoundation\Response
	{
		/** @var Card $card */
		$cards = $this->cardService->findAllCardsByUserId($id);

		if($this->checkPermission($id)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

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
		/** @var Card $card */
		$card = $this->cardService->findOneCardById($id);
		$userId = $card->getUserId();

		if($this->checkPermission($userId)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


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

		/** @var Card $card */
		$card = $this->cardService->findOneCardById($id);
		$userId = $card->getUserId();

		if($this->checkPermission($userId)){
			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$form = $this->createForm(CardType::class, $card);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()){

			$card->setUpdatedAt(new \DateTime('now'));

			$em = $this->getDoctrine()->getManager();
			$em->persist($card);
			$em->flush();

			$this->addFlash('info', 'successful edit card');

//			$this->cardOrderService->editProfile($order);

			return $this->viewAllCards($userId);

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
		/** @var User $currentUser */
		$currentUser = $this->security->getUser();
		$currentUserId = $currentUser->getId();

		return ((int)$currentUserId !== (int)$id || $currentUser->isAdmin() || $currentUser->isOffice());
	}
}
