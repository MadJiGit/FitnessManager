<?php

namespace FitnessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package FitnessBundle\Controller
 */

class SecurityController extends Controller
{
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

		$error = $authenticationUtils->getLastAuthenticationError();

		$lastUsername = $authenticationUtils->getLastUsername();

		if ($error) {
			$this->addFlash('danger', 'Invalid username or password!');
		}

		return $this->render('security/login.html.twig', [
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
