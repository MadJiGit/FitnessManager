<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\ProfileType;
use FitnessBundle\Service\Admin\AdminServiceInterface;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\Role\RoleServiceInterface;
use FitnessBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;


class AdminController extends Controller
{
	/** @var FormErrorServiceInterface $formErrorService */
	private $formErrorService;

	/** @var UserServiceInterface $profileService */
	private $profileService;

	/** @var Security $security */
	private $security;

	/** @var AdminServiceInterface $adminService */
	private $adminService;

	/** @var RoleServiceInterface $roleService */
	private $roleService;

	/**
	 * UserController constructor.
	 * @param FormErrorServiceInterface $formErrorService
	 * @param RoleServiceInterface $roleService
	 * @param UserServiceInterface $profileService
	 * @param Security $security
	 * @param AdminServiceInterface $adminService
	 */
	public function __construct(FormErrorServiceInterface $formErrorService, RoleServiceInterface $roleService, UserServiceInterface $profileService, Security $security, AdminServiceInterface $adminService)
	{
		$this->formErrorService = $formErrorService;
		$this->roleService = $roleService;
		$this->profileService = $profileService;
		$this->security = $security;
		$this->adminService = $adminService;
	}


	/**
	 * @Route ("/admin/register_user", name="register_user")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function registerAction(Request $request)
	{

		$isFullRights = $this->checkRights();

		$user = new User();

		$form = $this->createForm(ProfileType::class, $user);

		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);


		if ($form->isSubmitted() && $form->isValid()) {

			$passwordHash = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPassword());
			$user->setPassword($passwordHash);

			if( false === $isFullRights) {
				$role = $this->roleService->findOneBy(['name' => 'ROLE_CLIENT']);
				if (null === $role){
					$this->addFlash('danger', 'Please select ROLE');
					return $this->render('/admin/register_user.html.twig', [
						'form' => $form->createView(),
					]);
				}

				$user->addRole($role);

			} else {
				$role = $form->get('role')->getData();
				$user->addRole($role);
			}

			$isSave = $this->adminService->save($user);

			if (false === $isSave) {
				$this->addFlash('danger', 'User is not register');
				return $this->render('/admin/register_user.html.twig', [
					'form' => $form->createView(),
				]);

			}

			$this->addFlash('info', 'successful register new user');
			return $this->redirectToRoute('index');


		}

		return $this->render('/admin/register_user.html.twig', [
			'form' => $form->createView(),
		]);

	}

	/**
	 * @Route("/admin/edit/{id}", methods={"GET", "POST"}, name="admin_edit_user")
	 * @param $id ;
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editUser($id, Request $request): \Symfony\Component\HttpFoundation\Response
	{

		$this->checkRights();

		/** @var User $user */

		$user = $this->adminService->findOneById($id);
		$testRoleUser = $user->getRoleObject();
		$form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);


		if ($form->isSubmitted() && $form->isValid()) {

			try {
				if (true === $this->profileService->changePassword($form, $user)) {
					$this->addFlash('success', 'The password was successful changed.');
				}
			} catch (\Exception $ex) {
				$this->addFlash('danger', $ex->getMessage());

				return $this->render('admin/edit.html.twig', [
					'user' => $user,
					'form' => $form->createView(),
				]);
			}

			$roleFromForm = $form->get('role')->getData();


			if ($roleFromForm === null) {

				$roleFromForm = $testRoleUser;
			}

			$user->addRole($roleFromForm);

			$enabledFromForm = $form->get('enabled')->getData();
			$user->setEnabled($enabledFromForm);

			$isSave = $this->adminService->save($user);

			if (false === $isSave) {
				$this->addFlash('danger', 'User is not edited');
				return $this->render('admin/edit.html.twig', [
					'user' => $user,
					'form' => $form->createView(),
				]);

			}

			$this->addFlash('success', 'Profile >> ' . $user->getUsername() . ' << was successful updated.');
			return $this->redirectToRoute('admin_all_user');
		}


		return $this->render('admin/edit.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route("/admin/all", methods={"GET", "POST"}, name="admin_all_user")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */

	public function findAll(Request $request): \Symfony\Component\HttpFoundation\Response
	{

		$isFullRights = $this->checkRights();

		$paginator = $this->get('knp_paginator');
		/** User[] $users */

		if( false === $isFullRights) {

			/* user with ID to skip (DO NOT SHOW SUPER_ADMIN DATA)*/
			$userIdToSkip = 1;
			$users = $paginator->paginate(
				$this
					->getDoctrine()
					->getRepository(User::class)
					->selectByIdAscWhere($userIdToSkip),
				$request->query->getInt('page', 1), 6
			);

		} else {

			$users = $paginator->paginate(
				$this
					->getDoctrine()
					->getRepository(User::class)
					->selectByIdAscAll(),
				$request->query->getInt('page', 1), 6
			);

		}


		return $this->render('/admin/all.html.twig',
			['users' => $users]
		);

	}

	/**
	 * @Route("/admin/view_one/{id}", methods={"GET", "POST"}, name="admin_view_one")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */

	public function findOne($id): \Symfony\Component\HttpFoundation\Response
	{
		$this->checkRights();

		/** User[] $users */
		$user = $this->adminService->findOneById($id);

		if (null === $user) {
			$this->addFlash('danger', 'No such user!');
			return $this->redirectToRoute('index');
		}

		return $this->render('/admin/view_one',
			['user' => $user]
		);

	}


	/**
	 * @Route("/admin/delete/{id}", methods={"POST"}, name="admin_delete_user_prepare")
	 * @param User $user
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteUserActionPrepare(User $user): \Symfony\Component\HttpFoundation\Response
	{

		$this->checkRights();

		return $this->render('/admin/view_delete_one',
			['user' => $user]);

	}

	/**
	 * @Route("/admin/delete/{id}", name="admin_delete_user")
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function deleteUserAction(int $id): RedirectResponse
	{

		$this->checkRights();

		$user = $this->adminService->findOneById($id);

		if (null === $user) {
			$this->addFlash('danger', 'No such user!');
			return $this->redirectToRoute('index');
		}

		$username = $user->getUsername();

		$isDeleted = $this->adminService->deleteUser($user);

		if ($isDeleted) {

			$this->addFlash('success', "User with username {$username} deleted successfully!");
		} else {
			$this->addFlash('danger', "User with username {$username} IS NOT deleted!");

		}

		return $this->redirectToRoute('admin_all_user');
	}


	private function checkRights()
	{

		if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'])) {
			return true;
		}

		if ($this->security->isGranted(['ROLE_RECEPTIONIST'])) {
			return false;
		}

		$this->addFlash('info', 'You have not rights!!');
		return $this->redirectToRoute('index');

	}


}
