<?php

namespace FitnessBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="fitness_index")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
