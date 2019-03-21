<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\User;
use FitnessBundle\Form\ProfileType;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use FitnessBundle\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class UserController extends Controller
{

	/** @var FormErrorServiceInterface $formErrorService */
	private $formErrorService;

	/** @var UserServiceInterface $profileService */
	private $profileService;

	/** @var Security $security */
	private $security;

	/**
	 * UserController constructor.
	 * @param FormErrorServiceInterface $formErrorService
	 * @param UserServiceInterface $profileService
	 * @param Security $security
	 */
	public function __construct(FormErrorServiceInterface $formErrorService, UserServiceInterface $profileService, Security $security)
	{
		$this->formErrorService = $formErrorService;
		$this->profileService = $profileService;
		$this->security = $security;
	}

	/**
	 * @Route("/user/edit_test/{id}", methods={"GET", "POST"}, name="self_edit_user")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editUser($id, Request $request): \Symfony\Component\HttpFoundation\Response
	{
		$isUser = $this->security->isGranted(['ROLE_USER',
			'ROLE_CLIENT',
			'ROLE_RECEPTIONIST',
			'ROLE_TRAINER',
			'ROLE_OFFICE',
		]);

		/** @var User $user */
		$user = $this->getUser();
		$userId = (int)$user->getId();

		if (false === $isUser || $userId !== (int)$id) {
			$this->addFlash('info', 'You have not rights to change!');
			return $this->redirectToRoute('index');
		}

		$form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
		$form->handleRequest($request);

		$this->formErrorService->checkErrors($form);

		if ($form->isSubmitted() && $form->isValid()) {

			try {
				if (true === $this->profileService->changePassword($form, $user)) {
					$this->addFlash('success', 'The password was changed successful.');
				}
			} catch (\Exception $ex) {
				$this->addFlash('danger', $ex->getMessage());

				return $this->render('user/edit_test.html.twig', [
					'user' => $user,
					'form' => $form->createView(),
				]);
			}

			$this->profileService->editProfile($user);

			$this->addFlash('success', 'Profile was successful changed.');

			return $this->redirectToRoute('index');
		}

		return $this->render('user/edit_test.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
		]);
	}


	/**
	 * @Route("/user/view_one_user/{id}", name="view_one_user")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewOneUser($id): \Symfony\Component\HttpFoundation\Response
	{
		$user = $this->profileService->findOneUserById($id);

		return $this->render('user/view_one_user.html.twig', [
			'user' => $user,
		]);

	}

}

