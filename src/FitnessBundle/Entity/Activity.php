<?php

namespace FitnessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="activities")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\ActivityRepository")
 */
class Activity
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
	 * @var  \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime")
	 */
    private $createdAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="updated_at", type="datetime")
	 */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_from", type="datetime")
     */
    private $validFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_to", type="datetime")
     */
    private $validTo;

    /**
     * @var int
     *
     * @ORM\Column(name="capacity", type="integer")
     */
    private $capacity;

    /**
     * @var int
     *
     * @ORM\Column(name="current_capacity", type="integer")
     */
    private $currentCapacity;


	/**
	 * @var ArrayCollection|User[]
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\User", mappedBy="trainersActivities")
	 */
    private $trainers;


	/**
	 * @var ArrayCollection|User[]
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\User", mappedBy="clientsActivities")
	 */

    private $clients;


	/**
	 * Activity constructor.
	 * @throws \Exception
	 */
    public function __construct()
    {
    	$this->trainers = new ArrayCollection();
    	$this->clients = new ArrayCollection();
    	$this->setValidFrom(new \DateTime('now'));
    	$this->setValidTo(new \DateTime('now'));
    	$this->setCreatedAt(new \DateTime('now'));
    	$this->capacity = 0;
    	$this->setCurrentCapacity();
    }

	/**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Activity
     */
    public function setName($name): Activity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set validFrom.
     *
     * @param \DateTime $validFrom
     *
     * @return Activity
     */
    public function setValidFrom($validFrom): Activity
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom.
     *
     * @return \DateTime
     */
    public function getValidFrom(): \DateTime
    {
        return $this->validFrom;
    }

    /**
     * Set validTo.
     *
     * @param \DateTime $validTo
     *
     * @return Activity
     */
    public function setValidTo($validTo): Activity
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Get validTo.
     *
     * @return \DateTime
     */
    public function getValidTo(): \DateTime
    {
        return $this->validTo;
    }

    /**
     * Set capacity.
     *
     * @param int $capacity
     *
     * @return Activity
     */
    public function setCapacity($capacity): Activity
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity.
     *
     * @return int
     */
    public function getCapacity(): int
    {
        return $this->capacity;
    }

	/**
	 * Set currentCapacity.
	 *
	 * @return Activity
	 */
    public function setCurrentCapacity(): Activity
    {
    	$clients = $this->getClients();

        $this->currentCapacity = $this->getCapacity() - count($clients);

        return $this;
    }

    /**
     * Get currentCapacity.
     *
     * @return int
     */
    public function getCurrentCapacity(): ?int
    {
        return $this->currentCapacity;
    }

	/**
	 * @return ArrayCollection
	 */
	public function getTrainers(): ?ArrayCollection
	{

//		$stringTrainers = [];
//
//		if ($this->trainers){
//
//			/** @var User $user */
//			foreach ($this->trainers as $trainer) {
//
//				$stringTrainers[] = $trainer->getTrainer();
//			}
//		}

//		return $stringTrainers;
//		dump($stringTrainers);


//		dump($this->trainers);
//		exit;
		return $this->trainers;
	}

	/**
	 * @param User $trainers
	 * @return Activity
	 */
	public function setTrainers(User $trainers): Activity
	{
		$this->trainers[] = $trainers;

		return $this;
	}

	/**
	 * @return ArrayCollection|User[]
	 */
	public function getClients(): ArrayCollection
	{
		return $this->clients;
	}

	/**
	 * @param User $clients
	 * @return Activity
	 */
	public function setClients(User $clients): Activity
	{
		$this->clients[] = $clients;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime
	{
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 */
	private function setCreatedAt(\DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}


	/**
	 * Set updatedAt.
	 *
	 * @return Activity
	 * @throws \Exception
	 */
	public function setUpdatedAt(): Activity
	{
		$this->updatedAt = new \DateTime('now');

		return $this;
	}

	/**
	 * Get updatedAt.
	 *
	 * @return string
	 */
	public function getUpdatedAt(): string
	{
		return $this->updatedAt->format('Y-m-d H:m:s');
	}

}
