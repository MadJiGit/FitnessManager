<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
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
		if ($form->isSubmitted()) {
			$password = $this->get('security.password_encoder')
				->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

			$em = $this
				->getDoctrine()
				->getManager();
			$em->persist($user);
			$em->flush();

			return $this->profile();

		}

		return $this->render('user/register.html.twig');
	}

	/**
	 * @Route("/profile", name="user_profile")
	 */
	public function profile()
	{
		$userId = $this->getUser()->getId();
		$user = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($userId);

		return $this->render('default/index.html.twig', array('user' => $user));

	}

	/**
	 * @Route("/user/edit/{id}", name="user_edit")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editProfile($id, Request $request)
	{
		/** @var User $user */
		$user = $this->getDoctrine()->getRepository(User::class)->find($id);

		if ($user === null) {
			return $this->redirectToRoute('user_profile');
		}

		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		return $this->render('user/edit.html.twig',
			array('user' => $user,
				'form' => $form->createView()
			)
		);

	}

	/**
	 * @Route ("user/all/{param}", name="all_users")
	 * @param $param
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function findAllUsers($param)
	{
		if ($param === 'null') {
			$users = $this
				->getDoctrine()
				->getRepository(User::class)
				->findAll();

			return $this->render('user/all.html.twig', array('users' => $users));

		} else if ($param === 'client') {
			$users = $this
				->getDoctrine()
				->getRepository(User::class)
				->findBy(array('role' => 'Client'));

			return $this->render('user/all.html.twig', array('users' => $users));

		} else if ($param === 'trainer') {
			$users = $this
				->getDoctrine()
				->getRepository(User::class)
				->findBy(array('role' => 'Trainer'));

			return $this->render('user/all.html.twig', array('users' => $users));

		} else {
			$users = $this
				->getDoctrine()
				->getRepository(User::class)
				->findAll();

			$message = 'something is wrong';
			$this->addFlash('info', $message);
			return $this->render('user/all.html.twig', array('users' => $users));
		}

	}


	/**
	 * @Route ("user/one/{id}", name="user_view_one")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function findOneClient($id)
	{
		$user = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		return $this->render('user/one.html.twig', array('user' => $user));
	}


	/**
	 * @Route("/user/edit/{id}", name="user_edit")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editUser($id, Request $request)
	{
		/** @var User $user */
		$client = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($client === null) {
			return $this->redirectToRoute('user_view_one');
		}

		$pass = $client->getPassword();
		$form = $this->createForm(UserType::class, $client);
		$form->handleRequest($request);
		/** @var User $user */
		$user = $this->getUser();


		if ($user->isAdmin() || $user->isReceptionist()) {
			if ($form->isValid() && $form->isSubmitted()) {
				$oldPass = $request->get('oldPassword');
				$newPass = $request->get('newPassword');
				$repeatNewPass = $request->get('repeatNewPassword');

				$newPassHash = password_hash($newPass, PASSWORD_BCRYPT);

				$passCheck = password_verify($oldPass, $pass);

				if ($passCheck) {
					if ($newPass === $repeatNewPass) {
						if ($newPass === '') {
							$client->setPassword($pass);
						} else {
							$client->setPassword($newPassHash);
						}
						$em = $this->getDoctrine()->getManager();
						$em->persist($client);
						$em->flush();
						$message = ('Mr/s, ' . $user->getFullName() . 'update successful');

					} else {
						$message = ('Mr/s, ' . $user->getFullName() . 'password not match');
					}

				} else {
					$message = ('Mr/s, ' . $user->getFullName() . 'Wrong password');
				}

				$this->addFlash('info', $message);
				return $this->redirectToRoute('user_view_one',
					array('id' => $client->getId(), 'user' => $user));
			}
		} else {
			$message = ('Mr/s, ' . $user->getFullName() . '! You have no rights to edit this client');
			$this->addFlash('info', $message);
			return $this->redirectToRoute('user_view_one',
				array('id' => $client->getId(), 'user' => $user));
		}


		return $this->render('user/edit.html.twig',
			array(
				'form' => $form->createView(),
				'user' => $client)
		);
	}

	/**
	 * @Route("/user/delete/{id}", name="user_delete")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function deleteUser($id, Request $request)
	{
		$user = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($user === null) {
			return $this->redirectToRoute('user_view_one');
		}

		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		$admin = $this->getUser();


		if ($admin->isAdmin()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($user);
			$em->flush();

			$this->addFlash('info', 'delete successful');

			return $this->findAllUsers('null');
		}

		$this->addFlash('info', 'user is not deleted');

		return $this->findOneClient($id);
	}
}
