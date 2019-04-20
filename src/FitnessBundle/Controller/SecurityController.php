<?php

namespace FitnessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use FitnessBundle\Entity\User;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\User\UserServiceInterface;
use FitnessBundle\Service\Role\RoleServiceInterface;

/**
 * Class SecurityController
 * @package FitnessBundle\Controller
 */

class SecurityController extends Controller
{

	/** @var FormErrorServiceInterface $formErrorService */
	private $formErrorService;

	/** @var UserServiceInterface $profileService */
	private $profileService;

	/** @var RoleServiceInterface $roleService */
	private $roleService;

	/**
	 * UserController constructor.
	 * @param FormErrorServiceInterface $formErrorService
	 * @param UserServiceInterface $profileService
	 * @param RoleServiceInterface $roleService
	 */
	public function __construct(FormErrorServiceInterface $formErrorService, UserServiceInterface $profileService, RoleServiceInterface $roleService)
	{
		$this->formErrorService = $formErrorService;
		$this->profileService = $profileService;
		$this->roleService = $roleService;
	}

	/**
	 * @Route("/login", name="security_login")
	 * @param AuthenticationUtils $authenticationUtils
	 * @param Security $security
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(AuthenticationUtils $authenticationUtils, Security $security): \Symfony\Component\HttpFoundation\Response
	{

		if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
//			$this->addFlash('success', 'Successful login!');
			return $this->redirectToRoute('index');
		}


		$em = $this->getDoctrine()->getManager();
		$isSuperAdminRegister = $em->getRepository(User::class)->findBy(array(), array('id' => 'DESC'), 1);


		if (0 === count($isSuperAdminRegister)) {
			return $this->redirectToRoute('register_super_admin');

		}

		$error = $authenticationUtils->getLastAuthenticationError();

		$lastUsername = $authenticationUtils->getLastUsername();

		if ($error) {
			$this->addFlash('danger', 'Invalid username or password!');
		}

		return $this->render('security/login', [
			'last_username' => $lastUsername,
			'error' => $error,
		]);
	}


	/**
	 * This is the route the user can use to logout.
	 *
	 * But, this will never be executed. Symfony will intercept this first
	 * and handle the logout automatically. See logout in app/config/security.yml
	 *
	 * @Route("/logout", name="security_logout")
	 * @throws \Exception
	 */
	public function logoutAction()
	{
		throw new \Exception('Logout failed!');
	}
}
