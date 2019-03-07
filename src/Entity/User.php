<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields = {"email"},
 *  errorPath="email",
 *  message = "email déjà pris",
 * )
 * @UniqueEntity(
 *  fields = {"username"},
 *  errorPath="username",
 *  message = "username déjà pris",
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 8, minMessage="mdp trop court")
     * @Assert\EqualTo(propertyPath="confirmPassword", message="must be equal to confirm")
     */
    private $password;

    /**
     * [private description]
     * @var [type]
     * @Assert\EqualTo(propertyPath="password", message="must be equal to password")
     */
    private $confirmPassword;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSignup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mood;

    /**
     * @ORM\Column(type="float")
     */
    private $ratingWriter;

    /**
     * @ORM\Column(type="float")
     */
    private $ratingReader;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Media", inversedBy="owner", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $Media;

    public function __construct() {
        $this->roles[] = 'ROLE_USER';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDateSignup(): ?\DateTimeInterface
    {
        return $this->dateSignup;
    }

    public function setDateSignup(\DateTimeInterface $dateSignup): self
    {
        $this->dateSignup = $dateSignup;

        return $this;
    }

    public function getMood(): ?string
    {
        return $this->mood;
    }

    public function setMood(string $mood): self
    {
        $this->mood = $mood;

        return $this;
    }

    public function getRatingWriter(): ?float
    {
        return $this->ratingWriter;
    }

    public function setRatingWriter(float $ratingWriter): self
    {
        $this->ratingWriter = $ratingWriter;

        return $this;
    }

    public function getRatingReader(): ?float
    {
        return $this->ratingReader;
    }

    public function setRatingReader(float $ratingReader): self
    {
        $this->ratingReader = $ratingReader;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }
    
    public function setConfirmPassword(string $password): self
    {
        $this->confirmPassword = $password;

        return $this;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($data): self
    {
        $this->roles[] = $data;

        return $this;
    }

    public function eraseCredentials() {}

    public function getSalt() {}

    public function getRoles() {
        return $this->roles;
    }

    public function getMedia(): ?Media
    {
        return $this->Media;
    }

    public function setMedia(Media $Media): self
    {
        $this->Media = $Media;

        return $this;
    }
    
}
