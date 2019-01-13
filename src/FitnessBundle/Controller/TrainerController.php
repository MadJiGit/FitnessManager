<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrainerController extends Controller
{
	/**
	 * @Route ("trainer/one/{id}", name="trainer_view_one")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function findOneClient($id)
	{
		$trainer = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		return $this->render('trainer/one.html.twig', array('trainer' => $trainer));
	}


	/**
	 * @Route("/trainer/edit/{id}", name="trainer_edit")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editTrainer($id, Request $request)
	{
		/** @var User $user */
		$trainer = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($trainer === null) {
			return $this->redirectToRoute('trainer_view_one');
		}

		$pass = $trainer->getPassword();
//		$dataCreated = $trainer->getDataCreate();
		$role = $trainer->getRole();
		$sport = $trainer->getSport();

		$form = $this->createForm(UserType::class, $trainer);
		$form->handleRequest($request);

		$currentUser = $this->getUser();
		$currentUserId = $currentUser->getId();


//		var_dump($currentUserId);
//		var_dump((int)$id);
//		exit();

		if ($currentUserId === (int)$id) {
			if ($form->isValid() && $form->isSubmitted()) {

				$oldPass = $request->get('oldPassword');
				$newPass = $request->get('newPassword');
				$repeatNewPass = $request->get('repeatNewPassword');



				$newPassHash = password_hash($newPass, PASSWORD_BCRYPT);
				$passCheck = password_verify($oldPass, $pass);

//				var_dump($pass);
//				var_dump($oldPass);
//				exit();

				if ($passCheck) {
					if ($newPass === $repeatNewPass) {
						if ($newPass === '') {
							$trainer->setPassword($pass);
						} else {
							$trainer->setPassword($newPassHash);
						}
						$trainer->setRole($role);
						$trainer->setSport($sport);
//						$trainer->setDataCreate($dataCreated);
						$em = $this->getDoctrine()->getManager();
						$em->persist($trainer);
						$em->flush();
						$message = ('Mr/s, ' . $currentUser->getFullName() . 'update successful');

					} else {
						$message = ('Mr/s, ' . $currentUser->getFullName() . 'password not match');
					}

				} else {
					$message = ('Mr/s, ' . $currentUser->getFullName() . 'Wrong password');
				}

				$this->addFlash('info', $message);
				return $this->redirectToRoute('trainer_view_one',
					array('id' => $trainer->getId(), 'trainer' => $currentUser));
			}

			return $this->render('trainer/edit.html.twig',
				array(
					'form' => $form->createView(),
					'trainer' => $trainer)
			);


		}

		$message = ('Mr/s, ' . $currentUser->getFullName() . '! You have no rights to edit this client');
		$this->addFlash('info', $message);
		return $this->redirectToRoute('trainer_view_one',
			array('id' => $trainer->getId(), 'trainer' => $currentUser));

	}


	/**
	 * @Route ("trainer/allClients/{param}", name="trainer_all_clients_view")
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
//		} else if ($param === 'trainer') {
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
