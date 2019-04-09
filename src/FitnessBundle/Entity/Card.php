<?php

namespace FitnessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * CardType
 *
 * @ORM\Table(name="cards")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\CardRepository")
 */
class Card
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
	 * @var int
	 * @ORM\Column(name="user_id", type="integer")
	 */
	private $userId;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="FitnessBundle\Entity\User", inversedBy="cards")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="card_number", type="string", length=255)
	 */
	private $cardNumber;

	/**
	 * @var \DateTime
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
	 * @ORM\OneToMany(targetEntity="FitnessBundle\Entity\CardOrder", mappedBy="card")
	 *
	 * @var CardOrder[]/ArrayCollection $orders;
	 */
	private $orders;


	/**
	 * CardType constructor.
	 */
	public function __construct()
	{
		$this->orders = new ArrayCollection();
		$this->setCreatedAt(new \DateTime('now'));
		$this->setValidFrom(new \DateTime('now'));
		$this->setValidTo(new \DateTime('now'));
//		$this->setCardNumber($number);
	}

	/**
	 * Get id.
	 *
	 * @return int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->userId;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId(int $userId): void
	{
		$this->userId = $userId;
	}


	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user): void
	{
		$this->user = $user;
	}


	/**
	 * Set cardNumber.
	 * @param $number
	 * @return Card
	 */
	public function setCardNumber($number): Card
	{
		$this->cardNumber = str_pad($number, 8, '0', STR_PAD_LEFT);

		return $this;
	}

	/**
	 * Get cardNumber.
	 *
	 * @return string
	 */
	public function getCardNumber(): string
	{
		return $this->cardNumber;
	}

	/**
	 * Set createdAt.
	 *
	 * @param \DateTime $createdAt
	 *
	 * @return Card
	 */
	public function setCreatedAt($createdAt): Card
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * Get createdAt.
	 *
	 * @return \DateTime
	 */
	public function getCreatedAt(): ?\DateTime
	{
//	    return $this->createdAt->format('Y-m-d');

		return $this->createdAt;

	}

	/**
	 * Set updatedAt.
	 *
	 * @return Card
	 * @throws \Exception
	 */
	public function setUpdatedAt(): Card
	{
		$this->updatedAt = new \DateTime('now');
//		$this->updatedAt = $updatedAt;

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

	/**
	 * Set validFrom.
	 *
	 * @param \DateTime $validFrom
	 *
	 * @return Card
	 */
	public function setValidFrom($validFrom): Card
	{
		$this->validFrom = $validFrom;

		return $this;
	}

	/**
	 * Get validFrom.
	 *
	 * @return \DateTime|null
	 */
	public function getValidFrom(): ?\DateTime
	{
		return $this->validFrom;
//
//	    return $this->validFrom->format('Y-m-d');
	}

	/**
	 * Set validTo.
	 *
	 * @param \DateTime $validTo
	 *
	 * @return Card
	 */
	public function setValidTo($validTo): Card
	{
		$this->validTo = $validTo;

		return $this;
	}

	/**
	 * Get validTo.
	 *
	 * @return \DateTime
	 */
	public function getValidTo(): ?\DateTime
	{
		return $this->validTo;
//	    return $this->validTo->format('Y-m-d');
	}

	/**
	 * @return bool|null
	 */

	public function isValid(): ?bool
	{
		$valid = $this->getValidTo()->format('Y-m-d');
		if ($valid){
			return date('Y-m-d') <= $valid;
		}

		return false;

	}

	/**
	 * @return CardOrder[]
	 */
	public function getOrders()
	{
		return $this->orders;
	}

	/**
	 * @return CardOrder[]
	 */
	public function getAllOrders(): array
	{
		return $this->orders->toArray();
	}

	/**
	 * @param CardOrder[] $orders
	 */
	public function setOrders(array $orders): void
	{
		$this->orders = $orders;
	}


	public function getLastOrder(): CardOrder
	{
		$allOrders = $this->getAllOrders();
		$lastOrder = array_slice($allOrders, -1);

		dump($lastOrder);
		exit;
	}

}

























