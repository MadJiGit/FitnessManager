<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-31
 * Time: 22:47
 */

namespace FitnessBundle\Repository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class ClientRepository extends EntityRepository
{
	/** @var EntityManagerInterface $em */
	private $em;

	/**
	 * CarRepository constructor.
	 * @param EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
		parent::__construct($em, new ClassMetadata(Card::class));
	}

}