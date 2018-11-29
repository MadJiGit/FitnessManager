<?php

namespace FitnessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReceptionistController extends Controller
{
	public function createClient()
	{
		return $this->render('receptionist/createClient.html.twig');
	}
}
