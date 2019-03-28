<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Card;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\ClientForm;
use FitnessBundle\Service\Client\ClientServiceInterface;
use FitnessBundle\Service\FormError\FormErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends Controller
{
	/** @var Security $security */
	private $security;

	/** @var ClientServiceInterface $clientService */
	private $clientService;



	/** @var FormErrorService $formErrorService */
	private $formErrorService;

	/**
	 * CardOrderController constructor.
	 * @param Security $security
	 * @param ClientServiceInterface $clientService
	 * @param FormErrorService $formErrorService
	 */
	public function __construct(Security $security, ClientServiceInterface $clientService, FormErrorService $formErrorService)
	{
		$this->security = $security;
		$this->clientService = $clientService;
		$this->formErrorService = $formErrorService;
	}


	/**
	 * @Route ("/receptionist/body", name="search_card")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	
	public function search(Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$loggedInUser = $this->getUser();
		$loggedInUserId = $loggedInUser->getId();

		$this->checkPermission($loggedInUserId);


		$form = $this->createForm(ClientForm::class);
		$form->handleRequest($request);


		$searchedNumber = $request->get('cardNumber');
		$searchedUsername = $request->get('cardUsername');
		$searchedEmail = $request->get('cardEmail');

		$paginator = $this->get('knp_paginator');

		if ('' !== $searchedNumber) {

			if (false === is_numeric($searchedNumber)) {
				$this->addFlash('danger', 'Enter a valid number');
				return $this->render('default/index.html.twig');
			}



			/** @var Card $searchedCard */
			$searchedCard = $this->clientService->findCardByNumber($searchedNumber);

			if (false === $searchedCard){
				dump(false);
				exit;
			}


			dump(true);
			exit;

			return $this->render('card/view_one_card', [
				'card' => $searchedCard,
			]);


		}

		if ('' !== $searchedUsername) {
			if (0 !== preg_match('/[^a-zA-Z0-9_]/', $searchedUsername)) {
				$this->addFlash('danger', 'Enter a valid username');
				return $this->render('default/index.html.twig');
			}

			$cardOwnerId = $this->clientService->findCardOwnerByUsername($searchedUsername);

			/** @var Card $searchedCard */
//			$searchedCard = $this->clientService->findCardByUsername($searchedUsername);
			$searchedCard = $paginator->paginate(
				$this->clientService->
				findCardByUsername($searchedUsername),
				$request->query->getInt('page', 1), 6);

			if (false === $searchedCard){
				dump(false);
				exit;
			}


			dump(true);
			exit;

			return $this->render('card/view_all_cards', [
				'cards' => $searchedCard,
				'userId' => $cardOwnerId
			]);

		}

		if ('' !== $searchedEmail) {

			if (!filter_var($searchedEmail, FILTER_VALIDATE_EMAIL)){
				$this->addFlash('danger', 'Enter a valid email');
				return $this->render('default/index.html.twig');
			}

			$cardOwnerId = $this->clientService->findCardOwnerByEmail($searchedEmail);

			/** @var Card $searchedCard */
//			$searchedCard = $this->clientService->findCardByEmail($searchedEmail);

			$searchedCard = $paginator->paginate(
				$this->clientService->
				findCardByEmail($searchedEmail),
				$request->query->getInt('page', 1), 6);

			if (false === $searchedCard){
				dump(false);
				exit;
			}


			dump(true);
			exit;

//			dump($searchedCard);
//			exit;

			return $this->render('card/view_all_cards', [
				'cards' => $searchedCard,
				'userId' => $cardOwnerId
			]);


		}

		$this->addFlash('danger', 'Enter a data!');
		return $this->render('default/index.html.twig');


//		$searchedCardOwnerId = $searchedCard->getUserId();

//		$this->checkPermission($searchedCardOwnerId);





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
