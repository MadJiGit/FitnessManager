<?php

namespace FitnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trainer
 *
 * @ORM\Table(name="trainer")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\TrainerRepository")
 */
class Trainer
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

	/**
	 * @var Booking
	 * @ORM\OneToMany(targetEntity="FitnessBundle\Entity\Booking", mappedBy="trainer")
	 */
	private $bookings;

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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255)
	 */
	private $password;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Trainer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return Trainer
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


	/**
	 * Set password.
	 *
	 * @param string $password
	 *
	 * @return Trainer
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
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
	 * @return Trainer
	 */
	public function setDateAdded($dateAdded)
	{
		$this->dateAdded = $dateAdded;

		return $this;
	}

	/**
	 * Get dateAdded.
	 *
	 * @return string
	 */
	public function getDateAdded()
	{
		return $this->dateAdded->format('Y-m-d H:i:s');
	}

	/**
	 * @return Booking
	 */
	public function getBookings()
	{
		return $this->bookings;
	}

	/**
	 * @param Booking $booking
	 * @return Trainer
	 */
	public function addBookings($booking)
	{
		$this->bookings = $booking;

		return $this;
	}
}
