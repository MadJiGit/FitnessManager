<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Role;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WorkerController extends Controller
{
	/**
	 * @Route ("/register", name="user_register")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */

	public function registerAction(Request $request)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);
		// throw ERROR
//		if ($form->isSubmitted() && $form->isValid()){
		if ($form->isSubmitted()) {
			$password = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

//			$roleRepository = $this
//				->getDoctrine()
//				->getRepository(Role::class);
//			$userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
//
//			$user->addRole($userRole);

			$em = $this
				->getDoctrine()
				->getManager();
			$em->persist($user);
			$em->flush();

			return $this->redirectToRoute('security_login', array('user' => $user));
		}

		return $this->render('user/register.html.twig');
	}
}