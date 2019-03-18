<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\ProfileType;
use FitnessBundle\Form\RoleType;
use FitnessBundle\Form\UserType;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\Profile\ProfileServiceInterface;
use FitnessBundle\Service\Role\RoleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;


class AdminController extends Controller
{
	/** @var FormErrorServiceInterface $formErrorService */
	private $formErrorService;

	/** @var ProfileServiceInterface $profileService */
	private $profileService;

	/** @var RoleServiceInterface $roleService */
	private $roleService;

	/** @var Security $security */
	private $security;

	/**
	 * UserController constructor.
	 * @param FormErrorServiceInterface $formErrorService
	 * @param ProfileServiceInterface $profileService
	 * @param RoleServiceInterface $roleService
	 * @param Security $security
	 */
	public function __construct(FormErrorServiceInterface $formErrorService, ProfileServiceInterface $profileService, RoleServiceInterface $roleService, Security $security)
	{
		$this->formErrorService = $formErrorService;
		$this->profileService = $profileService;
		$this->roleService = $roleService;
		$this->security = $security;
	}


	/**
	 * @Route ("/admin/register_user", name="register_user")
	 * @param Request $request
	 * @param TokenStorageInterface $tokenStorage
	 * @param SessionInterface $session
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function registerAction(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session)
	{

		$isSuperAdminHere = $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);

		if (false === $isSuperAdminHere) {
			$this->addFlash('info', 'You have not ADMIN rights!');
			return $this->redirectToRoute('index');
		}

		$user = new User();

		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			$role = $form->get('role')->getData();

//			$userRole = $this->roleService->findOneBy(['name' => $role]);

			$user->addRole($role);


			$passwordHash = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPassword());
			$user->setPassword($passwordHash);

			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			$this->addFlash('info', 'successful register new user');

			return $this->redirectToRoute('index');
		}

		return $this->render('/admin/register_user.html.twig', [
			'form' => $form->createView(),
		]);

	}

	/**
	 * @Route("/admin/edit/{id}", methods={"GET", "POST"}, name="admin_edit_user")
	 * @param $id;
	 * @param Request $request
	 * @param TokenStorageInterface $tokenStorage
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editUser($id, Request $request, TokenStorageInterface $tokenStorage): \Symfony\Component\HttpFoundation\Response
	{
		$isSuperAdminHere = $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);

		if (false === $isSuperAdminHere) {
			$this->addFlash('info', 'You have not ADMIN rights!');
			return $this->redirectToRoute('index');
		}

		/** @var User $user */

		$user = $this->profileService->find($id);
		$role = new Role();
		$role->setUsers($user);
		$userRoles = $user->getRoles();

		$form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
//		$formRole = $this->createForm(RoleType::class, $role, ['user' => $user]);
		$form->handleRequest($request);
//		$formRole->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {


			$oldPassword = $form->get('old_password')->getData();
			$newPassword = $form->get('new_password')->getData();

			if ($oldPassword !== null && $newPassword !== null){

				try {
					if (true === $this->profileService->changePassword($form, $user)) {
						$this->addFlash('success', 'The password was successful changed.');
					}
				} catch (\Exception $ex) {
					$this->addFlash('danger', $ex->getMessage());

					return $this->render('admin/edit.html.twig', [
						'user' => $user,
//						'roles' => $formRole->createView(),
						'form' => $form->createView(),
					]);
				}
			}


			/** @var Role $roleFromForm */
			$roleFromForm = $form->get('roles')->getData();

			if ($roleFromForm !== ''){

				$userId = $user->getId();
				$roles = $user->getProfileRoles();

				foreach ($roles as $role){
					$this->profileService->removeAllRoles($userId, $role->getId());
				}

//				$userRole = $this->roleService->findOneBy(['name' => $roleFromForm]);
//				$user->addRole($userRole);



				$user->addRole($roleFromForm);


			}

			$enabledFromForm = $form->get('enabled')->getData();
			$user->setEnabled($enabledFromForm);

			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

//			$this->profileService->editProfile($user);

			$this->addFlash('success', 'Profile >> ' . $user->getUsername() . ' << was successful updated.');

			return $this->redirectToRoute('admin_all_user');
		}


//		dump($user);
//		exit;

		return $this->render('admin/edit.html.twig', [
			'user' => $user,
//			'roles' => $formRole->createView(),
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
		$isSuperAdminHere = $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);

		if (false === $isSuperAdminHere) {
			$this->addFlash('info', 'You have not ADMIN rights!');
			return $this->redirectToRoute('index');
		}

		$paginator = $this->get('knp_paginator');
		/** User[] $users */
		$users = $paginator->paginate(
			$this
				->getDoctrine()
				->getRepository(User::class)
				->selectByIdAsc(),
			$request->query->getInt('page', 1), 6

		);

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
		$isSuperAdminHere = $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);

		if (false === $isSuperAdminHere) {
			$this->addFlash('info', 'You have not ADMIN rights!');
			return $this->redirectToRoute('index');
		}

		/** User[] $users */
		$user = $this->profileService->find($id);

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

		$isSuperAdminHere = $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);

		if (false === $isSuperAdminHere) {
			$this->addFlash('info', 'You have not ADMIN rights!');
			return $this->redirectToRoute('index');
		}

		return $this->render('/admin/view_delete_one',
			['user' => $user]);


	}

	/**
	 * @Route("/admin/delete/{id}", methods={"POST"}, name="admin_delete_user")
	 * @param User $user
	 * @return RedirectResponse
	 */
	public function deleteUserAction(User $user): RedirectResponse
	{

		$isSuperAdminHere = $this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);

		if (false === $isSuperAdminHere) {
			$this->addFlash('info', 'You have not ADMIN rights!');
			return $this->redirectToRoute('index');
		}

		$em = $this->getDoctrine()->getManager();
		$em->remove($user);
		$em->flush();
		$this->addFlash('success', "User with username {$user->getUsername()} deleted successfully!");
		return $this->redirectToRoute('admin_all_user');
	}



}

//	/**
//	 * @Route("/{user}/edit", name="user_edit", methods={"GET", "POST"}, requirements={"user": "\d+"})
//	 * @param Request $request
//	 * @param User $user
//	 * @return \Symfony\Component\HttpFoundation\Response
//	 * @throws \Exception
//	 */
//	public function editAction(Request $request, User $user)
//	{
//		if ($user->getId() === $this->getUser()->getId()) {
//			return $this->redirectToRoute('profile_edit');
//		}
//
//		$form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
//		$form->handleRequest($request);
//
//		$this->formErrorService->checkErrors($form);
//
//		if ($form->isSubmitted() && $form->isValid()) {
//			try {
//				if (true === $this->profileService->changePassword($form, $user)) {
//					$this->addFlash('success', 'Паролата бе успешно променена.');
//				}
//			} catch (\Exception $ex) {
//				$this->addFlash('danger', $ex->getMessage());
//
//				return $this->render('user/edit.html.twig', [
//					'user' => $user,
//					'form' => $form->createView(),
//				]);
//			}
//
//			$this->profileService->editProfile($user);
//			$this->addFlash('success', 'Профилът бе успешно редактиран.');
//
//			return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);
//		}
//
//		return $this->render('admin/edit.html.twig', [
//			'user' => $user,
//			'form' => $form->createView()
//		]);
//	}

//		$this->addFlash('info', 'you are not admin, login like admin');
//
//		return $this->redirectToRoute('hair_index');
//	}


//	/**
//	 * @Route ("/edit_test/{id}", name="edit_user")
//	 * @param $id
//	 * @param Request $request
//	 * @param UserPasswordEncoderInterface $passwordEncoder
//	 * @param AuthenticationUtils $authenticationUtils
//	 * @return \Symfony\Component\HttpFoundation\Response
//	 */
//	public function editUser($id, Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils): \Symfony\Component\HttpFoundation\Response
//	{
//
//		/** @var User $currentUser */
//		$currentUser = $this->getUser();
//
//		$form = $this->createForm(UserType::class, $currentUser);
//		$form->handleRequest($request);
//
//		$error = $authenticationUtils->getLastAuthenticationError();
//
//
//		if ($form->isSubmitted() && $form->isValid()) {
//
//			$em = $this->getDoctrine()->getManager();
//			$em->flush();
//
//			$this->addFlash('info', 'edit successful');
//
//			return $this->redirectToRoute('hair_index');
//		}
//
//		return $this->render('user/edit_test.html.twig',
//			['form' => $form->createView(),
//				'user' => $currentUser,
//				'error' => $error]);
//
//
//	}

//	/**
//	 * @Route ("/edit_test/{id}", name="edit_user")
//	 * @param $id
//	 * @param Request $request
//	 * @param AuthenticationUtils $authenticationUtils
//	 * @return \Symfony\Component\HttpFoundation\Response
//	 */
//	public function editUser($id, Request $request, AuthenticationUtils $authenticationUtils): \Symfony\Component\HttpFoundation\Response
//	{
//
//		/** @var User $currentUser */
//		$user = $this->getUser();
//
//		dump($user);
//		exit;
//
//		$form = $this->createForm(UserType::class, $user);
//		$form->handleRequest($request);
//
////		$form = $this->createFormBuilder($user)
////			->add('username', TextType::class)
////			->add('email', EmailType::class)
////			->add('chose_role', ChoiceType::class, array(
////				'choices' => array(
////					'Admin' => 'ROLE_ADMIN',
////					'User' => 'ROLE_USER',
////					'Client' => 'ROLE_CLIENT'
////				),
////				'placeholder' => 'Select role',
////				'required' => true
////			))
////			->add('submit', SubmitType::class);
//
//
//		$error = $authenticationUtils->getLastAuthenticationError();
//
//
//		if ($form->isSubmitted() && $form->isValid()) {
//
//			$em = $this->getDoctrine()->getManager();
//			$em->flush();
//
//			$this->addFlash('info', 'edit successful');
//
//			return $this->redirectToRoute('hair_index');
//		}
//
//		return $this->render('user/edit_test.html.twig',
//			['form' => $form->createView(),
//				'error' => $error]);
//
//
//	}
//}
