<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Trainer;
use FitnessBundle\Form\TrainerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrainerController extends Controller
{
	/**
	 * @Route ("/create", name="register_trainer")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function registerAction(Request $request)
	{
		$trainer = new Trainer();
		$form = $this->createForm(TrainerType::class, $trainer);
		$form->handleRequest($request);



		if ($form->isSubmitted()) {
			$password = $this->get('security.password_encoder')
				->encodePassword($trainer, $trainer->getPassword());
			$trainer->setPassword($password);


			$em = $this
				->getDoctrine()
				->getManager();
			$em->persist($trainer);
			$em->flush();

			return $this->redirectToRoute('security_login', array('' => $trainer));
		}

		return $this->render('trainer/create.html.twig');
	}
}
