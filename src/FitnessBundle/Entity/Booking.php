<?php

namespace FitnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Booking
 *
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\BookingRepository")
 */
class Booking
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


	/**
	 * @var Trainer
	 * @ORM\ManyToOne(targetEntity="FitnessBundle\Entity\Trainer", inversedBy="bookings")
	 */
    private $trainer;

	/**
	 * @var Client
	 * @ORM\ManyToOne(targetEntity="FitnessBundle\Entity\Client", inversedBy="bookings")
	 */
    private $client;


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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Booking
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

	/**
	 * @return Trainer
	 */
	public function getTrainer()
	{
		return $this->trainer;
	}

	/**
	 * @param Trainer $trainer
	 * @return Booking
	 */
	public function addTrainer($trainer)
	{
		$this->trainer = $trainer;

		return $this;
	}

	/**
	 * @return Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param Client $client
	 * @return Booking
	 */
	public function addClient($client)
	{
		$this->client = $client;

		return $this;
	}




}
