<?php

namespace FitnessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserType
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
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
	 * @ORM\Column(name="username", type="string", length=255, unique=true, nullable=false)
	 *
	 * @Assert\NotBlank(message="The field is required")
	 * @Assert\Length(min = 5))
	 *
	 */
	private $username;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=191)
	 * @Assert\NotBlank(message="Паролата е задължителна.", groups={"registration"})
	 * @Assert\Length(
	 *     min="6",
	 *     max="12",
	 *     minMessage="Паролата трябва да е дълга поне {{ limit }} символа.",
	 *     maxMessage="Паролата трябва да съдържа не повече от {{ limit }} символа.",
	 *     groups={"registration"}
	 * )
	 * @Assert\Regex(
	 *     pattern="/^[A-Za-z0-9]+$/",
	 *     message="Паролата трябва се състои само от малки и главни букви и цифри.",
	 *     groups={"registration"}
	 * )
	 */
	private $password;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=255, unique=true)
	 * @Assert\NotBlank(message="The field is required")
	 * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
	 */
	private $email;

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
	 * @var ArrayCollection|Role[]
	 *
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\Role", inversedBy="users")
	 * @ORM\JoinTable(name="roles_users",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 * )
	 */
	private $roles;

	/**
	 * @var ArrayCollection|Activity[]
	 *
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\Activity", inversedBy="trainers")
	 * @ORM\JoinTable(name="trainers_users",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="trainer_id", referencedColumnName="id")}
	 * )
	 */
	private $trainersActivities;

	/**
	 * @var ArrayCollection|Activity[]
	 *
	 * @ORM\ManyToMany(targetEntity="FitnessBundle\Entity\Activity", inversedBy="clients")
	 * @ORM\JoinTable(name="clients_users",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="client_id", referencedColumnName="id")}
	 * )
	 */
	private $clientsActivities;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="enabled", type="boolean", nullable=true)
	 */
	private $enabled;

	/**
	 * @ORM\OneToMany(targetEntity="FitnessBundle\Entity\Card", mappedBy="user")
	 *
	 * @var Card[]/ArrayCollection $cards;
	 */
	private $cards;

	/**
	 * @var string
	 */
	private $role;



	/**
	 * User constructor.
	 * @throws \Exception
	 */
	public function __construct()
	{
		$this->roles = new ArrayCollection();
		$this->trainersActivities = new ArrayCollection();
		$this->clientsActivities = new ArrayCollection();
		$this->createdAt = new \DateTime();
		$this->updatedAt = new \DateTime();
		$this->setEnabled(true);
		$this->cards = new ArrayCollection();
	}


	public function __toString()
	{
		return $this->getUsername();
	}


	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return User
	 */
	public function setUsername($username): User
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword($password): User
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}

//	/**
//	 * @return string
//	 */
//	public function getPlainPassword(): ?string
//	{
//		return $this->plainPassword;
//	}
//
//	/**
//	 * @param string $plainPassword
//	 * @return string
//	 */
//	public function setPlainPassword($plainPassword): string
//	{
//		$this->plainPassword = $plainPassword;
//	}


	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return User
	 */
	public function setEmail($email): User
	{
		$this->email = $email;

		return $this;

	}

	/**
	 * @return ArrayCollection|Activity[]
	 */
	public function getTrainersActivities()
	{
		return $this->trainersActivities;
	}

	/**
	 * @param Activity $trainersActivities
	 * @return User
	 */
	public function setTrainersActivities(Activity $trainersActivities): self
	{
		$this->trainersActivities[] = $trainersActivities;

		return $this;
	}

	/**
	 * @return ArrayCollection|Activity[]
	 */
	public function getClientsActivities()
	{
		return $this->clientsActivities;
	}

	/**
	 * @param Activity $clientsActivities
	 * @return User
	 */
	public function setClientsActivities(Activity $clientsActivities): self
	{
		$this->clientsActivities[] = $clientsActivities;

		return $this;
	}



	public function getRole()
	{
		return $this->role;
	}

	public function setRole($role)
	{
		$this->role = $role;

		return $this;
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

		if ($this->roles){

			/** @var Role $role */
			foreach ($this->roles as $role) {

				$stringRoles[] = $role->getRole();
			}
		}

		return $stringRoles;

	}

	public function getRoleObject()
	{

		$roleReturn = '';

		if ($this->roles){

			/** @var Role $role */
			foreach ($this->roles as $role) {

				$roleReturn = $role;

			}
		}

		return $roleReturn;
	}

	public function getAllRoles()
	{
		return $this->roles;
	}


	/**
	 * @param Role $role
	 *
	 * @return User
	 */
	public function addRole(Role $role): ?User
	{
		$this->removeRole();

		$this->setRole($role);

		$this->roles[] = $role;

		return $this;
	}


	/**
	 * @param array $roles
	 * @return $this
	 */
	public function setRoles($roles): self
	{
		$this->removeRole();

		$this->roles = $roles;

		return $this;
	}


	public function removeRole()
	{
		$this->roles = array();

		return $this;
	}

//	/**
//	 * @return Role[]|ArrayCollection
//	 */
//	public function getProfileRoles()
//	{
//		return $this->roles;
//	}
//
//	/**
//	 * @param Role[]|ArrayCollection $roles
//	 * @return User
//	 */
//	public function setProfileRoles($roles): User
//	{
//		foreach ($roles as $role) {
//			$this->addProfileRole($role);
//		}
//
//		return $this;
//	}
//
//	/**
//	 * @param Role $role
//	 * @return User
//	 */
//	public function addProfileRole(Role $role): User
//	{
//		if (!$this->roles->contains($role)) {
//			$this->roles->add($role);
//
//			$this->role = $role;
//		}
//
//		return $this;
//	}


	public function getRoleName()
	{
		$temp = $this->getRoles();

		if ($temp === ''){
			return 'no role';
		}

		$result = explode('_', $temp[0]);

		switch (count($result)) {
			case 1:
			case 2:
				$test1 = strtolower(end($result));
				return ucfirst($test1);
				break;
			case 3:
				$first = strtolower($result[1]);
				$second = strtolower($result[2]);
				return ucfirst($first) . ' ' . ucfirst($second);
				break;
			default:
				return 'no role';
				break;


		}
	}


	/**
	 * @return bool
	 */
	public function isSuperAdmin(): ?bool
	{
		return in_array('ROLE_SUPER_ADMIN', $this->getRoles(), true);
	}

	/**
	 * @return bool
	 */
	public function isAdmin(): ?bool
	{
		return $this->isSuperAdmin() || in_array('ROLE_ADMIN', $this->getRoles(), true);
	}

	/**
	 * @return bool
	 */
	public function isReceptionist(): ?bool
	{
		return in_array('ROLE_RECEPTIONIST', $this->getRoles(), true);
	}

	/**
	 * @return bool
	 */
	public function isClient(): ?bool
	{
		return in_array('ROLE_CLIENT', $this->getRoles(), true);
	}


	/**
	 * @param \DateTime $date
	 * @return User
	 */
	public function setUpdatedAt(\DateTime $date): User
	{
		$this->updatedAt = $date;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt(): string
	{

		return $this->updatedAt->format('Y-m-d H:m:s');
	}

	/**
	 * @return string
	 */
	public function getCreatedAt(): string
	{
//		return $this->createdAt;

		return $this->createdAt->format('Y-m-d H:m:s');
	}

	/**
	 * @param \DateTime $createdAt
	 * @return User
	 */
	public function setCreatedAt(\DateTime $createdAt): User
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * Checks whether the user is enabled.
	 *
	 * Internally, if this method returns false, the authentication system
	 * will throw a DisabledException and prevent login.
	 *
	 * @return bool true if the user is enabled, false otherwise
	 *
	 * @see DisabledException
	 */
	public function isEnabled(): bool
	{
		return $this->enabled;
	}

	/**
	 * @param bool $enabled
	 */
	public function setEnabled(bool $enabled): void
	{
		$this->enabled = $enabled;

	}

	/**
	 * @return Card[]
	 */
	public function getCards(): array
	{
		return $this->cards->toArray();
	}

	/**
	 * @param Card[] $cards
	 */
	public function setCards(array $cards): void
	{
		$this->cards = $cards;
	}


	/**
	 * String representation of object
	 * @link https://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize()
	{
		return serialize([
			$this->id,
			$this->username,
			$this->password,
			// see section on salt below
			// $this->salt,
		]);
	}

	/**
	 * Constructs the object
	 * @link https://php.net/manual/en/serializable.unserialize.php
	 * @param string $serialized <p>
	 * The string representation of the object.
	 * </p>
	 * @return void
	 * @since 5.1.0
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->password,
			// see section on salt below
			// $this->salt
			) = unserialize($serialized, ['allowed_classes' => false]);
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

