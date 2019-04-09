<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-31
 * Time: 22:36
 */

namespace FitnessBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;

class ClientController extends Controller
{
	/** @var Security $security */
	private $security;

	/**
	 * ClientController constructor.
	 * @param Security $security
	 */
	public function __construct(Security $security)
	{
		$this->security = $security;
	}




}