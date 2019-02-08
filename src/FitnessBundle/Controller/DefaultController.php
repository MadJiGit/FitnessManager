<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\Profile\ProfileServiceInterface;
use FitnessBundle\Service\Role\RoleServiceInterface;

class DefaultController extends Controller
{

	/** @var FormErrorServiceInterface $formErrorService */
	private $formErrorService;

	/** @var ProfileServiceInterface $profileService */
	private $profileService;

	/** @var RoleServiceInterface $roleService */
	private $roleService;

	/**
	 * UserController constructor.
	 * @param FormErrorServiceInterface $formErrorService
	 * @param ProfileServiceInterface $profileService
	 * @param RoleServiceInterface $roleService
	 */
	public function __construct(FormErrorServiceInterface $formErrorService, ProfileServiceInterface $profileService, RoleServiceInterface $roleService)
	{
		$this->formErrorService = $formErrorService;
		$this->profileService = $profileService;
		$this->roleService = $roleService;
	}


	/**
	 * @Route("/", name="index")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(): \Symfony\Component\HttpFoundation\Response
	{

		$em = $this->getDoctrine()->getManager();
		$isSuperAdminRegister = $em->getRepository(User::class)->findBy(array(), array('id' => 'DESC'), 1);

		$user = $this->getUser();

//		dump($isSuperAdminRegister);
//		dump($user);
//		exit;

		if ($user) {
			return $this->render('default/index.html.twig', [
				'user' => $user,
			]);
		}

		if ($isSuperAdminRegister === null) {
			return $this->redirectToRoute('register_super_admin');

		}

		return $this->redirectToRoute('security_login');

	}

	/**
	 * @Route ("/first_user_only", name="register_super_admin")
	 * @param Request $request
	 * @param TokenStorageInterface $tokenStorage
	 * @param SessionInterface $session
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function registerAction(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session)
	{
		$user = new User();

		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			$userRole = $this->roleService->findOneBy(['name' => 'ROLE_SUPER_ADMIN']);
			$user->addRole($userRole);

			$this->profileService->newProfile($user);


			$token = new UsernamePasswordToken(
				$user,
				$user->getPassword(),
				'main',
				$user->getRoles()
			);


			$tokenStorage->setToken($token);
			$session->set('_security_main', serialize($token));

			$this->addFlash('info', 'successful register new ADMIN');

			return $this->redirectToRoute('index');
		}

		return $this->render('first_user_only.html.twig', [
			'form' => $form->createView(),
		]);

	}

}
