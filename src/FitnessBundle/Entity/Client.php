<?php

namespace FitnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\ClientRepository")
 */
class Client
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="card_number", type="string", length=25, unique=true)
	 */
	private $cardNumber;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_added", type="datetime")
	 */
	private $dateAdded;

	/**
	 * Client constructor.
	 * @throws \Exception
	 */
	public function __construct()
	{
		$this->dateAdded = new \DateTime('now');

	}

	/**
	 * Get id.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set cardNumber.
	 *
	 * @param string $cardNumber
	 *
	 * @return Client
	 */
	public function setCardNumber($cardNumber)
	{
		$this->cardNumber = $cardNumber;

		return $this;
	}

	/**
	 * Get cardNumber.
	 *
	 * @return string
	 */
	public function getCardNumber()
	{
		return $this->cardNumber;
	}

	/**
	 * Set dateAdded.
	 *
	 * @param \DateTime $dateAdded
	 *
	 * @return Client
	 */
	public function setDateAdded($dateAdded)
	{
		$this->dateAdded = $dateAdded;

		return $this;
	}

	/**
	 * Get dateAdded.
	 *
	 * @return \DateTime
	 */
	public function getDateAdded()
	{
		return $this->dateAdded;
	}
}
