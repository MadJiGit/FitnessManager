<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
	/**
	 * @Route ("/register", name="user_register")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */

	public function registerAction(Request $request)
	{
		$user = new User();
		$date_added = date('Y-m-d H:i:s');
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);
		if ($form->isSubmitted()){
			$password = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPassword());
			$user->setPassword($password);
			$user->setDataCreate($date_added);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			return $this->redirectToRoute('security_login');
		}

		return $this->render('user/register.html.twig');
	}
}
