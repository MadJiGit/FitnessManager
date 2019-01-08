<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Client;
use FitnessBundle\Form\ClientType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
			$password = $client->getPassword();
			$client->setPassword($password);

			$em = $this
				->getDoctrine()
				->getManager();
			$em->persist($client);
			$em->flush();

			return $this->allClient();
		}

		return $this->render('client/create.html.twig');
	}


	/**
	 * @Route ("/all", name="all_clients")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function allClient()
	{
		$client = $this
			->getDoctrine()
			->getRepository(Client::class)
			->findAll();

		return $this->render('client/all.html.twig', array('client' => $client));


	}
}
