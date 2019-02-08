<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\ProfileType;
use FitnessBundle\Form\UserType;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\Profile\ProfileServiceInterface;
use FitnessBundle\Service\Role\RoleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;


class UserController extends Controller
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
	 * @Route("/user/edit_test/{id}", methods={"GET", "POST"}, name="self_edit_user")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editUser($id, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$isUser = $this->security->isGranted(['ROLE_USER']);

		/** @var User $user */
		$user = $this->getUser();
		$userId = (int)$user->getId();

		if (false === $isUser || $userId !== (int)$id){
			$this->addFlash('info', 'You have not rights to change!');
			return $this->redirectToRoute('index');
		}

		$form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				if (true === $this->profileService->changePassword($form, $user)) {
					$this->addFlash('success', 'The password was changed successful.');
				}
			} catch (\Exception $ex) {
				$this->addFlash('danger', $ex->getMessage());

				return $this->render('user/edit_test.html.twig', [
					'user' => $user,
					'form' => $form->createView(),
				]);
			}

			$this->profileService->editProfile($user);

			$this->addFlash('success', 'Profile was successful changed.');

			return $this->redirectToRoute('index');
		}

		return $this->render('user/edit_test.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route("/user/view_one_user/{id}", name="view_one_user")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewOneUser($id): \Symfony\Component\HttpFoundation\Response
	{
		$user = $this->profileService->find($id);

		return $this->render('user/view_one_user.html.twig', [
			'user' => $user,
		]);

	}

}
//	/**
//	 * @Route ("/user/register", name="self_register_user")
//	 * @param Request $request
//	 * @param TokenStorageInterface $tokenStorage
//	 * @param SessionInterface $session
//	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
//	 * @throws \Exception
//	 */
//	public function registerAction(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session)
//	{
//		$user = new User();
//
//		$form = $this->createForm(UserType::class, $user);
//
////		dump($this->createForm(UserType::class, $user));
////		exit;
//
//		$form->handleRequest($request);
//
//		$this->formErrorService->checkErrors($form);
//
//
//
//
////
////		/** @var User $currUser */
////		$currUser = $this->getUser();
////
////		$isAdmin = $currUser->isAdmin();
////
//////		dump($isAdmin);
//////		exit;
////
////		$em = $this->getDoctrine()->getManager();
////		$isUser = $em->getRepository(User::class)->findBy(array(), array('id' => 'DESC'), 1);
//
////		if ( $isAdmin === true) {
//
//		if ($form->isSubmitted() && $form->isValid()) {
//
////				$passwordHash = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
////				$user->setPassword($passwordHash);
//
//
////				if ($isUser) {
////					$choseRole = $user->getChoseRole();
////					$roleRepo = $this->getDoctrine()->getRepository(Role::class);
////					$userRole = $roleRepo->findOneBy(['name' => $choseRole]);
////				} else {
////					$roleRepo = $this->getDoctrine()->getRepository(Role::class);
////					$userRole = $roleRepo->findOneBy(['name' => 'ADMIN']);
////
////				}
//
//
//						$em = $this->getDoctrine()->getManager();
////			$em->persist($user);
////			$em->flush();
//
////			var_dump($form);
////			exit;
//
//
//			$userRole = $this->roleService->findOneBy(['name' => 'ROLE_SUPER_ADMIN']);
////			$userRole = 'ROLE_SUPER_ADMIN';
//			$user->addRole($userRole);
//
////			dump($user);
////			exit;
//
//			$this->profileService->newProfile($user);
//
//
//			$token = new UsernamePasswordToken(
//				$user,
//				$user->getPassword(),
//				'main',
//				$user->getRoles()
//			);
//
////			$em = $this->getDoctrine()->getManager();
////			$em->persist($user);
////			$em->flush();
//
//			$tokenStorage->setToken($token);
//			$session->set('_security_main', serialize($token));
//
//
//
//			$this->addFlash('info', 'successful register new user');
//
//			return $this->redirectToRoute('index');
//		}
//
//		return $this->render('user/self_register.html.twig', [
//				'form' => $form->createView(),
//			]);
//
//		}

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
