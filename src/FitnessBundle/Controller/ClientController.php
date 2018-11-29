<?php

namespace FitnessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientController extends Controller
{
	public function indexAction($name)
	{
		return $this->render('', array('name' => $name));
	}
}
