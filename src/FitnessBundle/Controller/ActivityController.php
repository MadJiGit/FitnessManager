<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Activity;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\ActivityType;
use FitnessBundle\Form\ClientType;
use FitnessBundle\Service\Activity\ActivityServiceInterface;
use FitnessBundle\Service\Admin\AdminService;
use FitnessBundle\Service\Admin\AdminServiceInterface;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;


class ActivityController extends Controller
{

	/** @var AdminServiceInterface $adminService */
	private $adminService;

	/** @var UserServiceInterface $userService */
	private $userService;

	/** @var Security $security */
	private $security;

	/** @var ActivityServiceInterface $activityService */
	private $activityService;

	/** @var FormErrorServiceInterface $formErrorService */
	private $formErrorService;

	/**
	 * ActivityController constructor.
	 * @param Security $security
	 * @param ActivityServiceInterface $activityService
	 * @param FormErrorServiceInterface $formErrorService
	 */
	public function __construct(AdminServiceInterface $adminService, UserServiceInterface $userService, Security $security, ActivityServiceInterface $activityService, FormErrorServiceInterface $formErrorService)
	{
		$this->security = $security;
		$this->activityService = $activityService;
		$this->formErrorService = $formErrorService;
		$this->userService = $userService;
		$this->adminService = $adminService;
	}


	/**
	 * @Route ("/activity/add", name="add_activity")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function registerAction(Request $request)
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$activity = new Activity();

		$form = $this->createForm(ActivityType::class, $activity);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			$admin = $this->userService->findOneUserById(1);

			if (null === $admin) {
				$this->addFlash('info', 'Sorry, there are not trainers!');
				return $this->redirectToRoute('index');
			}

			/** @var User $trainer */
			$trainer = $form->get('trainers')->getData();
			$trainer->setTrainersActivities($activity);

			$activity->setTrainers($trainer);
			$activity->setUpdatedAt(new \DateTime('now'));

			$result = $this->activityService->saveActivity($activity);


			if (false === $result) {
				$this->addFlash('danger', 'something is wrong!');
				return $this->render('activity/add', [
					'form' => $form->createView(),
				]);
			}

			$this->addFlash('success', 'successful add new sport activity!');

			return $this->redirectToRoute('view_all_activities');

		}

		return $this->render('activity/add', [
			'form' => $form->createView(),
		]);


	}

	/**
	 * @Route ("/activity/view_all_activities", name="view_all_activities" )
	 */
	public function allActivities()
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$activities = $this->activityService->findAllActivities();

		if (null === $activities) {
			$this->addFlash('info', 'Sorry, there are not activities yet!');
			return $this->redirectToRoute('add_activity');
		}


		return $this->render('activity/view_all_activities', [
			'activities' => $activities,
		]);

	}


	/**
	 * @Route ("/activity/view_one_activity/{id}", name="view_one_activity")
	 * @param int $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function oneActivity(int $id): \Symfony\Component\HttpFoundation\Response
	{

		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$activity = $this->activityService->findOneActivityById($id);


		if (null === $activity) {
			$this->addFlash('info', 'Sorry, there are not activity!');
			return $this->redirectToRoute('view_all_activities');
		}

		$users = $activity->getTrainers();


		if (null === $users) {

			$this->addFlash('info', 'Sorry, there are not trainers yet!');
			return $this->redirectToRoute('view_all_activities');
		}


		return $this->render('activity/view_one_activity', [
			'activity' => $activity,
			'users' => $users
		]);

	}


	/**
	 * @Route ("/activity/add_trainer/{id}", name="add_trainer")
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addTrainer(Request $request, $id): \Symfony\Component\HttpFoundation\Response
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$activity = $this->activityService->findOneActivityById($id);

		if (null === $activity) {
			$this->addFlash('info', 'Sorry, there are not activity!');
			return $this->redirectToRoute('view_all_activities');
		}


		$form = $this->createForm(ClientType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$searchedId = (int)$form->get('id')->getData();

			if (null === $searchedId) {
				$this->addFlash('info', 'Enter ID!');
				return $this->render('activity/add_trainer');
			}

			$trainer = $this->userService->findOneUserById($searchedId);

			if (null === $trainer || false === $trainer) {
				$this->addFlash('danger', 'There are not trainer with THAT Id!');
				return $this->render('activity/add_trainer');
			}

			$result = $activity->setTrainers($trainer);
			$this->activityService->saveActivity($activity);
			$this->adminService->save($trainer);


			if (null === $result || false === $result) {
				$this->addFlash('info', 'Enter ID!');
				return $this->render('activity/add_trainer');
			}


			return $this->redirectToRoute('view_one_activity', [
				'id' => $id
			]);


		}


		return $this->render('activity/add_trainer', [
			'id' => $id,
			'activity' => $activity,
			'form' => $form->createView()
		]);

	}

	/**
	 * @Route ("/activity/edit/{id}", name="edit_activity")
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editActivity(Request $request, $id): \Symfony\Component\HttpFoundation\Response
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$activity = $this->activityService->findOneActivityById($id);
//		$trainers = $activity->getTrainers();
		$form = $this->createForm(ActivityType::class, $activity);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);


		if ($form->isSubmitted() ) {

//			$newTrainer = $form->get('trainers')->getData();

//			dump($trainers);
//			dump($newTrainer);
//			exit;


//			$activity->setTrainers($trainers);


			$isSave = $this->activityService->saveActivity($activity);




			if (false === $isSave) {
				$this->addFlash('danger', 'Activity is not edited');
				return $this->render('activity/edit_activity', [
					'activity' => $activity,
					'form' => $form->createView(),
				]);
			}

			$this->addFlash('success', 'Activity is edited successfully');
			return $this->redirectToRoute('view_one_activity', [
				'id' => $id,
			]);


		}

		return $this->render('activity/edit_activity', [
			'activity' => $activity,
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route ("/activity/clients/{id}", name="view_clients")
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewClients(Request $request, $id): \Symfony\Component\HttpFoundation\Response
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$activity = $this->activityService->findOneActivityById($id);

		if (null === $activity) {
			$this->addFlash('info', 'Sorry, there are not activity!');
			return $this->redirectToRoute('view_all_activities');
		}

		$paginator = $this->get('knp_paginator');

		$users = $paginator->paginate(
			$this
				->getDoctrine()
				->getRepository(Activity::class)
				->selectById($id),
			$request->query->getInt('page', 1), 6
		);

		if (null === $users) {

			$this->addFlash('info', 'Sorry, there are not clients yet!');
			return $this->redirectToRoute('view_all_activities');
		}

		return $this->render('activity/view_all_clients', [
			'id' => $id,
//			'activity' => $activity,
			'users' => $users
		]);
	}

	/**
	 * @Route ("/activity/trainers/{id}", name="view_trainers")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewTrainers($id): \Symfony\Component\HttpFoundation\Response
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$activity = $this->activityService->findOneActivityById($id);

		if (null === $activity) {
			$this->addFlash('info', 'Sorry, there are not activity!');
			return $this->redirectToRoute('view_all_activities');
		}


//		$users = $activity->getTrainers()->first();
		$users = $activity->getTrainers();

		if (null === $users) {

			$this->addFlash('info', 'Sorry, there are not trainers yet!');
			return $this->redirectToRoute('view_all_activities');
		}


		return $this->render('activity/view_all_users', [
			'activity' => $activity,
			'users' => $users
		]);
	}

	/**
	 * @Route ("/activity/add_client/{id}", name="add_client")
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addClient(Request $request, $id): \Symfony\Component\HttpFoundation\Response
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$activity = $this->activityService->findOneActivityById($id);

		if (null === $activity) {
			$this->addFlash('info', 'Sorry, there are not activity!');
			return $this->redirectToRoute('view_all_activities');
		}

		$form = $this->createForm(ClientType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$searchedId = (int)$form->get('id')->getData();

			if (null === $searchedId) {
				$this->addFlash('info', 'Enter ID!');
				return $this->render('activity/add_client');
			}

			$client = $this->userService->findOneUserById($searchedId);

//			$this->checkClient($client);
			if (null === $client || false === $client) {
				$this->addFlash('danger', 'There are not client with THAT ID!');
				return $this->render('activity/add_client');
			}

			$clientsCard = $client->getCards();


			if (0 === count($clientsCard)){
				$this->addFlash('danger', 'This client have not active card!');
				return $this->redirectToRoute('add_new_card', [
					'id' => $client->getId()
				]);
			}


			$result = false;

			foreach ($clientsCard as $card){
				if($card->isValid()){
					if (null !== $card->isVisitPossibleReturnOrder()){
						$result = true;
						break;
					}

					$this->addFlash('danger', 'This client have not active card order!');
					return $this->redirectToRoute('add_new_order', [
						'cardId' => $card->getId(),
					]);
				}
			}

			if (false === $result){
				$this->addFlash('danger', 'This client have not active card!');
				return $this->redirectToRoute('add_new_card', [
					'id' => $client->getId()
				]);
			}

			$result = $activity->setClients($client);
			$this->activityService->saveActivity($activity);
			$this->adminService->save($client);

			if (null === $result || false === $result) {
				$this->addFlash('info', 'Enter ID!');
				return $this->render('activity/add_client');
			}

			return $this->redirectToRoute('view_clients', [
				'id' => $id
			]);

		}


		return $this->render('activity/add_client', [
			'id' => $id,
			'activity' => $activity,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route ("/activity/edit_user/{id}", name="edit_user")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editUser($id): \Symfony\Component\HttpFoundation\Response
	{

		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		//TODO

		return null;

	}

	/**
	 * @Route ("/activity/remove_trainer/{userId}/{activityId}", methods={"GET"},  name="remove_trainer")
	 * @param $userId
	 * @param $activityId
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function removeTrainer($userId, $activityId): \Symfony\Component\HttpFoundation\Response
	{

		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$trainerToRemove = $this->adminService->findOneById($userId);
		$activity = $this->activityService->findOneActivityById($activityId);


		$result1 = $activity->removeTrainer($trainerToRemove);
		$result2 = $trainerToRemove->removeTrainersActivities($activity);
		$this->activityService->saveActivity($activity);
		$this->adminService->save($trainerToRemove);


		if (false === $result1 || false === $result2) {
			$this->addFlash('danger', 'Sorry, there are not that trainer!');

		} else {
			$this->addFlash('success', 'Successfully remove trainer!');
		}


		return $this->redirectToRoute('view_one_activity', [
			'id' => $activityId,
		]);


	}

	/**
	 * @Route ("/activity/remove_client/{userId}/{activityId}", methods={"GET"},  name="remove_client")
	 * @param $userId
	 * @param $activityId
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function removeClient($userId, $activityId): \Symfony\Component\HttpFoundation\Response
	{

		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}


		$clientToRemove = $this->adminService->findOneById($userId);
		$activity = $this->activityService->findOneActivityById($activityId);


		$result1 = $activity->removeClient($clientToRemove);
		$result2 = $clientToRemove->removeClientsActivities($activity);
		$this->activityService->saveActivity($activity);
		$this->adminService->save($clientToRemove);


		if (false === $result1 || false === $result2) {
			$this->addFlash('danger', 'Sorry, there are not that client!');

		} else {
			$this->addFlash('success', 'Successfully remove client!');
		}


		return $this->redirectToRoute('view_one_activity', [
			'id' => $activityId,
		]);


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

	private function checkClient(User $client)
	{
		if (null === $client || false === $client) {
			$this->addFlash('danger', 'There are not client with THAT ID!');
			return $this->render('activity/add_client');
		}

		$clientsCard = $client->getCards();


		if (0 === count($clientsCard)){
			$this->addFlash('danger', 'This client have not active card!');
			return $this->redirectToRoute('add_new_card', [
				'id' => $client->getId()
			]);
		}


		$result = false;

		foreach ($clientsCard as $card){
			if($card->isValid()){
				if (null !== $card->isVisitPossibleReturnOrder()){
					$result = true;
					break;
				}

				$this->addFlash('danger', 'This client have not active card order!');
				return $this->redirectToRoute('add_new_order', [
					'cardId' => $card->getId(),
				]);
			}
		}

		if (false === $result){
			$this->addFlash('danger', 'This client have not active card!');
			return $this->redirectToRoute('add_new_card', [
				'id' => $client->getId()
			]);
		}

		return true;
	}
}
