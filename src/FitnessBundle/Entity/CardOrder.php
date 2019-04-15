<?php

namespace FitnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * CardOrderType
 *
 * @ORM\Table(name="card_orders")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\OrdersRepository")
 */
class CardOrder
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
	 * @ORM\Column(name="card_id", type="integer", nullable=false)
	 */
    private $cardId;


	/**
	 * @var Card
	 *
	 * @ORM\ManyToOne(targetEntity="FitnessBundle\Entity\Card", inversedBy="orders")
	 * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
	 */
    private $card;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime")
     *
     */
    private $dueDate;

    /**
     * @var int
     *
     * @ORM\Column(name="visits_order", type="integer")
     * @Assert\GreaterThanOrEqual( value="0")
     */
    private $visitsOrder;

    /**
     * @var int
     *
     * @ORM\Column(name="visits_left", type="integer")
     */
    private $visitsLeft;


    public function __construct()
    {
	    $this->setStartDate(new \DateTime('now'));
	    $this->setDueDate(new \DateTime('now'));
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
	 * @return int
	 */
	public function getCardId(): int
	{
		return $this->cardId;
	}

	/**
	 * Set cardId
	 *
	 * @param int $cardId
	 *
	 * @return CardOrder
	 */
	public function setCardId($cardId): CardOrder
	{
		$this->cardId = $cardId;

		return $this;
	}

	/**
	 * @return Card
	 */
	public function getCard(): Card
	{
		return $this->card;
	}

	/**
	 * @param Card $card
	 * @return CardOrder
	 */
	public function setCard(Card $card): CardOrder
	{
		$this->card = $card;

		return $this;
	}


    /**
     * Set createdDate.
     *
     * @param \DateTime $startDate
     *
     * @return CardOrder
     */
    public function setStartDate($startDate): CardOrder
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
	    return $this->startDate;
//	    return $this->startDate->format('Y-m-d H:m:s');
    }

    /**
     * Set dueDate.
     *
     * @param \DateTime $dueDate
     *
     * @return CardOrder
     */
    public function setDueDate($dueDate): CardOrder
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate.
     *
     * @return \DateTime
     */
    public function getDueDate(): \DateTime
    {
	    return $this->dueDate;
//	    return $this->dueDate->format('Y-m-d H:m:s');
    }

    /**
     * Set visitsOrder.
     *
     * @param int $visitsOrder
     *
     * @return CardOrder
     */
    public function setVisitsOrder($visitsOrder): CardOrder
    {
        $this->visitsOrder = $visitsOrder;

        return $this;
    }

    /**
     * Get visitsOrder.
     *
     * @return int
     */
    public function getVisitsOrder(): ?int
    {
        return $this->visitsOrder;
    }

	/**
	 * Set visitsLeft.
	 *
	 * @param $visit
	 *
	 * @return CardOrder
	 */
    public function setVisitsLeft($visit): CardOrder
    {
        $this->visitsLeft = $visit;

        return $this;
    }


    public function oneVisit(): void
    {
        $this->visitsLeft = ($this->getVisitsLeft() - 1);
    }

    /**
     * Get visitsLeft.
     *
     * @return int
     */
    public function getVisitsLeft(): int
    {
        return $this->visitsLeft;
    }

	/**
	 * @return bool|null
	 * @throws \Exception
	 */
    public function isOutOfOrder(): ?bool
    {

	    $currentDate = new \DateTime('now');
//	    dump($currentDate);
//	    dump($this->getDueDate());

    	if ($this->getVisitsLeft() <= 0 && $this->getDueDate() > $currentDate){
    		return true;
	    }

    	return false;
    }
}
