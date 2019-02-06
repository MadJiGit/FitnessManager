<?php

namespace FitnessBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
	/**
	 * @Route ("/login", name="security_login")
	 * @param AuthenticationUtils $authenticationUtils
	 * @param Security $security
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(AuthenticationUtils $authenticationUtils, Security $security): \Symfony\Component\HttpFoundation\Response
	{

		if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
			return $this->redirectToRoute('fitness_index');
		}

		$error = $authenticationUtils->getLastAuthenticationError();

		$lastUsername = $authenticationUtils->getLastUsername();

		if ($error) {
			$this->addFlash('danger', 'Invalid email or password!');
		}

		$error = $authenticationUtils->getLastAuthenticationError();

		return $this->render('security/login.html.twig', ['error' => $error,]);
	}


	/**
	 * @Route("/logout", name="security_logout")
	 * @throws \Exception
	 */
	public function logoutAction()
	{
		throw new \Exception('Logout failed!');
	}
}





























