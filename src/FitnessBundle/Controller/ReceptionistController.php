<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReceptionistController extends Controller
{
	/**
	 * @Route ("receptionist/one/{id}", name="receptionist_view_one")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function findOneClient($id)
	{
		$receptionist = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		return $this->render('receptionist/one.html.twig', array('receptionist' => $receptionist));
	}


	/**
	 * @Route("/receptionist/edit/{id}", name="receptionist_edit")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editReceptionist($id, Request $request)
	{
		/** @var User $user */
		$receptionist = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($receptionist === null) {
			return $this->redirectToRoute('receptionist_view_one');
		}

		$pass = $receptionist->getPassword();
		$role = $receptionist->getRole();
		$sport = $receptionist->getSport();

		$form = $this->createForm(UserType::class, $receptionist);
		$form->handleRequest($request);

		$currentUser = $this->getUser();
		$currentUserId = $currentUser->getId();


		if ($currentUserId === (int)$id) {
			if ($form->isValid() && $form->isSubmitted()) {

				$oldPass = $request->get('oldPassword');
				$newPass = $request->get('newPassword');
				$repeatNewPass = $request->get('repeatNewPassword');



				$newPassHash = password_hash($newPass, PASSWORD_BCRYPT);
				$passCheck = password_verify($oldPass, $pass);

				if ($passCheck) {
					if ($newPass === $repeatNewPass) {
						if ($newPass === '') {
							$receptionist->setPassword($pass);
						} else {
							$receptionist->setPassword($newPassHash);
						}
						$receptionist->setRole($role);
						$receptionist->setSport($sport);
						$em = $this->getDoctrine()->getManager();
						$em->persist($receptionist);
						$em->flush();
						$message = ('Mr/s, ' . $currentUser->getFullName() . 'update successful');

					} else {
						$message = ('Mr/s, ' . $currentUser->getFullName() . 'password not match');
					}

				} else {
					$message = ('Mr/s, ' . $currentUser->getFullName() . 'Wrong password');
				}

				$this->addFlash('info', $message);
				return $this->redirectToRoute('receptionist_view_one',
					array('id' => $receptionist->getId(), 'receptionist' => $currentUser));
			}

			return $this->render('receptionist/edit.html.twig',
				array(
					'form' => $form->createView(),
					'receptionist' => $receptionist)
			);


		}

		$message = ('Mr/s, ' . $currentUser->getFullName() . '! You have no rights to edit this client');
		$this->addFlash('info', $message);
		return $this->redirectToRoute('receptionist_view_one',
			array('id' => $receptionist->getId(), 'receptionist' => $currentUser));

	}


	/**
	 * @Route ("receptionist/allClients/{param}", name="receptionist_all_clients_view")
	 * @param $param
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function findAllClients($param)
	{

		$message = 'under construction';
		$this->addFlash('info', $message);
		return $this->render('default/index.html.twig');
//		if ($param === 'null') {
//			$users = $this
//				->getDoctrine()
//				->getRepository(User::class)
//				->findAll();
//
//			return $this->render('user/all.html.twig', array('users' => $users));
//
//		} else if ($param === 'client') {
//			$users = $this
//				->getDoctrine()
//				->getRepository(User::class)
//				->findBy(array('role' => 'Client'));
//
//			return $this->render('user/all.html.twig', array('users' => $users));
//
//		} else if ($param === 'receptionist') {
//			$users = $this
//				->getDoctrine()
//				->getRepository(User::class)
//				->findBy(array('role' => 'Trainer'));
//
//			return $this->render('user/all.html.twig', array('users' => $users));
//
//		} else {
//			$users = $this
//				->getDoctrine()
//				->getRepository(User::class)
//				->findAll();
//
//			$message = 'something is wrong';
//			$this->addFlash('info', $message);
//			return $this->render('user/all.html.twig', array('users' => $users));
//		}
//
	}
}
