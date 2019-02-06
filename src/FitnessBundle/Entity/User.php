<?php

namespace FitnessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
	 * @var string
	 *
	 * @ORM\Column(name="first_name", type="string", length=255)
	 */
	private $firstName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="last_name", type="string", length=255)
	 */
	private $lastName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=50, unique=true)
	 */
	private $email;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="phone", type="string", length=50, nullable=true)
	 */
	private $phone;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="gender", type="string", length=1)
	 */
	private $gender;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data_create", type="datetime")
	 */
	private $dataCreate;


	/**
	 * @var ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="FitnessBundle\Entity\Article", mappedBy="author")
	 */
	private $articles;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="role", type="string", length=25)
	 */
	private $role;


	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\Role", inversedBy="users")
	 * @ORM\JoinTable(name="users_roles",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 *     )
	 */
	private $roles;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sport", type="string", length=25, nullable=true)
	 */
	private $sport;

	/**
	 * @var string
	 */
	private $cardNumber;

	/**
	 * @var string
	 */
	private $fullName;



	/**
	 * User constructor.
	 * @throws \Exception
	 */
	public function __construct()
	{
		$this->roles = new ArrayCollection();
		$this->articles = new ArrayCollection();
		$this->dataCreate = new \DateTime('now');
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
	 * Set username.
	 *
	 * @param string $username
	 *
	 * @return User
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
	 * @return User
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
	 * Set firstName.
	 *
	 * @param string $firstName
	 *
	 * @return User
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * Get firstName.
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * Set lastName.
	 *
	 * @param string $lastName
	 *
	 * @return User
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;

		return $this;
	}

	/**
	 * Get lastName.
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * Set email.
	 *
	 * @param string $email
	 *
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email.
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set phone.
	 *
	 * @param string|null $phone
	 *
	 * @return User
	 */
	public function setPhone($phone = null)
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 * Get phone.
	 *
	 * @return string|null
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * Set gender.
	 *
	 * @param string $gender
	 *
	 * @return User
	 */
	public function setGender($gender)
	{
		$this->gender = $gender;

		return $this;
	}

	/**
	 * Get gender.
	 *
	 * @return string
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * Set dataCreate.
	 *
	 * @param \DateTime $dataCreate
	 *
	 * @return User
	 */
	public function setDataCreate($dataCreate)
	{
		$this->dataCreate = $dataCreate;

		return $this;
	}

	/**
	 * Get dataCreate.
	 *
	 * @return string
	 */
	public function getDataCreate()
	{
		return $this->dataCreate->format('Y-m-d H:m:s');
	}


	/**
	 * @return ArrayCollection
	 */
	public function getArticles()
	{
		return $this->articles;
	}

	/**
	 * @param Article $article
	 *
	 * @return User
	 */
	public function setArticles($article)
	{
		$this->articles[] = $article;

		return $this;
	}

	/**
	 * @param string $fullName
	 */
	public function setFullName($fullName)
	{
		$this->fullName = $fullName;
	}


	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	/**
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @param string $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}


	/**
	 * Returns the roles granted to the user.
	 *
	 *     public function getRoles()
	 *     {
	 *         return array('ROLE_USER');
	 *     }
	 *
	 * Alternatively, the roles might be stored on a ``roles`` property,
	 * and populated in any number of different ways when the user object
	 * is created.
	 *
	 * @return array (Role|string)[] The user roles
	 */
	public function getRoles()
	{
		$stringRoles = [];
		foreach ($this->roles as $role) {
			/** @var $role Role */
			$stringRoles[] = $role->getRole();
		}

		return $stringRoles;
	}

	/**
	 * @param Role $role
	 *
	 * @return User
	 */
	public function addRole(Role $role)
	{
		$this->roles[] = $role;

		return $this;
	}


	/**
	 * @param $article
	 * @return bool
	 */
	public function isAuthor(Article $article)
	{
		return $article->getAuthorId() === $this->getId();
	}


	/**
	 * @return bool
	 */
	public function isSuperAdmin()
	{
		if ($this->getRole() === 'super_admin') {
			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		return $this->getRole() === 'Admin';
	}

	/**
	 * @return bool
	 */
	public function isManager()
	{
		return $this->getRole() === 'Manager';
	}


	/**
	 * @return bool
	 */
	public function isReceptionist()
	{
		return $this->getRole() === 'Receptionist';
	}

	/**
	 * @return bool
	 */
	public function isTrainer()
	{
		return $this->getRole() === 'Trainer';
	}

	/**
	 * @return bool
	 */
	public function isAccountant()
	{
		return $this->getRole() === 'Accountant';
	}

	/**
	 * @return bool
	 */
	public function isClient()
	{
		return $this->getRole() === 'Client';
	}

	/**
	 * @return string
	 */
	public function getSport()
	{
		return $this->sport;
	}

	/**
	 * @param string $sport
	 */
	public function setSport($sport)
	{
		$this->sport = $sport;
	}

	/**
	 * @return string
	 */
	public function setCardNumber()
	{
		return str_pad((int)$this->getId(), 8, '0', STR_PAD_LEFT);
	}


	/**
	 * @return string
	 */
	public function getCardNumber()
	{
//		return $this->cardNumber;
		return str_pad((int)$this->getId(), 8, '0', STR_PAD_LEFT);
	}


	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * This can return null if the password was not encoded using a salt.
	 *
	 * @return string|null The salt
	 */
	public function getSalt()
	{
		// TODO: Implement getSalt() method.
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials()
	{
		// TODO: Implement eraseCredentials() method.
	}

}
