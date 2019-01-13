<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends Controller
{
	/**
	 * @Route ("client/one/{id}", name="client_view_one")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function findOneClient($id)
	{
		$client = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		return $this->render('client/one.html.twig', array('client' => $client));
	}


	/**
	 * @Route("/client/edit/{id}", name="client_edit")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editClient($id, Request $request)
	{
		/** @var User $user */
		$client = $this
			->getDoctrine()
			->getRepository(User::class)
			->find($id);

		if ($client === null) {
			return $this->redirectToRoute('client_view_one');
		}

		$pass = $client->getPassword();
		$dataCreated = $client->getDataCreate();
		$role = $client->getRole();
		$sport = $client->getSport();

		$form = $this->createForm(UserType::class, $client);
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
							$client->setPassword($pass);
						} else {
							$client->setPassword($newPassHash);
						}
						$client->setRole($role);
						$client->setSport($sport);
//						$client->setDataCreate($dataCreated);
						$em = $this->getDoctrine()->getManager();
						$em->persist($client);
						$em->flush();
						$message = ('Mr/s, ' . $currentUser->getFullName() . 'update successful');

					} else {
						$message = ('Mr/s, ' . $currentUser->getFullName() . 'password not match');
					}

				} else {
					$message = ('Mr/s, ' . $currentUser->getFullName() . 'Wrong password');
				}

				$this->addFlash('info', $message);
				return $this->redirectToRoute('client_view_one',
					array('id' => $client->getId(), 'client' => $currentUser));
			}

			return $this->render('client/edit.html.twig',
				array(
					'form' => $form->createView(),
					'client' => $client)
			);


		}

		$message = ('Mr/s, ' . $currentUser->getFullName() . '! You have no rights to edit this client');
		$this->addFlash('info', $message);
		return $this->redirectToRoute('client_view_one',
			array('id' => $client->getId(), 'client' => $currentUser));

	}
}
