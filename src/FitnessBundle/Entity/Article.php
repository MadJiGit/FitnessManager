<?php

namespace FitnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleType
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="FitnessBundle\Repository\ArticleRepository")
 */
class Article
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime")
     */
    private $dateAdded;

	/**
	 * @var string
	 */
    private $summary;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="author_id", type="integer")
	 */
    private $authorId;


	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="FitnessBundle\Entity\User", inversedBy="articles")
	 *
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
	 */
    private $author;

	/**
	 * ArticleType constructor.
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
     * Set title.
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return Article
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


	/**
	 * @return string
	 */
	public function getSummary()
	{
		return $this->summary;
	}

	public function setSummary()
	{
		$this->summary = substr($this->getContent(), 0, strlen($this->getContent())/2) . '...';
	}

	/**
	 * @return int
	 */
	public function getAuthorId()
	{
		return $this->authorId;
	}

	/**
	 * @param int $authorId
	 *
	 * @return Article
	 */
	public function setAuthorId($authorId)
	{
		$this->authorId = $authorId;

		return $this;
	}


	/**
	 * @return User
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param User $author
	 *
	 * @return Article
	 */
	public function setAuthor($author)
	{
		$this->author = $author;

		return $this;
	}
}
