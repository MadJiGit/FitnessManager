<?php

namespace FitnessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\RoleRepository")
 */
class Role
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true, unique=true)
     */
    private $name;


	/**
	 * @var ArrayCollection|User[]
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\User", mappedBy="roles")
	 */
    private $users;

    public function __construct()
    {
    	$this->users = new ArrayCollection();
    }


	/**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Role
     */
    public function setName($name): Role
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

	/**
	 * @return string
	 */

    public function getRole(): string
    {
    	return $this->getName();
    }

	/**
	 * @return ArrayCollection
	 */
	public function getUsers(): ArrayCollection
	{
		return $this->users;
	}

	/**
	 * @param ArrayCollection|User[] $users
	 * @return Role
	 */
	public function setUsers($users): Role
	{
		$this->users = $users;

		return $this;
	}


	public function __toString() {
		return ucfirst(strtolower(explode('_', $this->name)[1]));
	}
}

