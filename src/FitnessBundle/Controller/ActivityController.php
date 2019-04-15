<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Activity;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\ActivityType;
use FitnessBundle\Service\Activity\ActivityServiceInterface;
use FitnessBundle\Service\Admin\AdminService;
use FitnessBundle\Service\Admin\AdminServiceInterface;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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


//			dump($form);
//			exit;

			$admin = $this->userService->findOneUserById(1);

			if (null === $admin) {
				$this->addFlash('info', 'Sorry, there are not trainers!');
				return $this->redirectToRoute('index');
			}

			/** @var User $trainer */
			$trainer = $form->get('trainers')->getData();
			$trainer->setTrainersActivities($activity);

//			dump($trainer);
//			exit;

			$activity->setTrainers($trainer);
//			$activity->setClients($admin);
			$activity->setUpdatedAt(new \DateTime('now'));

			$result = $this->activityService->saveActivity($activity);

			if (false === $result) {
				$this->addFlash('danger', 'something is wrong!');
				return $this->render('activity/add', [
					'form' => $form->createView(),
				]);
			}

//			dump($activity);
//			dump($trainer);
//			exit;

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
	public function oneActivity(int $id)
	{

		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		$activity = $this->activityService->findOneActivityById($id);


		dump($activity);
//		dump($activity->getCapacity());
		exit;

		$trainer = $activity->getTrainers()->first();


		if (null === $activity) {
			$this->addFlash('info', 'Sorry, there are not activity!');
			return $this->redirectToRoute('view_all_activities');
		}


		return $this->render('activity/view_one_activity', [
			'activity' => $activity,
			'trainer' => $trainer,
		]);

	}


	/**
	 * @Route ("/activity/add_trainer/{id}", name="add_trainer")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function addTrainer($id)
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

		return $trainers = $this->adminService->findAllUsersByRole('ROLE_TRAINER');


	}

	/**
	 * @Route ("/activity/edit/{id}", name="edit_activity")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function editActivity($id)
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		return null;
	}


	/**
	 * @Route ("/activity/clients/{id}", name="view_clients")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function viewClients($id)
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		return null;
	}

	/**
	 * @Route ("/activity/trainers/{id}", name="view_trainers")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function viewTrainers($id)
	{
		if (false === $this->isAdminHere()) {

			$this->addFlash('info', 'Sorry, you have no permission!');
			return $this->redirectToRoute('index');
		}

		return null;
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
