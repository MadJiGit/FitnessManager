<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Client;
use FitnessBundle\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends Controller
{
	/**
	 * @Route ("/create", name="register_client")
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function registerAction(Request $request)
	{
		$client = new Client();
		$form = $this->createForm(ClientType::class, $client);
		$form->handleRequest($request);



		if ($form->isSubmitted()) {
			$password = $this->get('security.password_encoder')
				->encodePassword($client, $client->getPassword());
			$client->setPassword($password);


			$em = $this
				->getDoctrine()
				->getManager();
			$em->persist($client);
			$em->flush();

			return $this->redirectToRoute('security_login', array('' => $client));
		}

		return $this->render('user/register.html.twig');
	}
}
