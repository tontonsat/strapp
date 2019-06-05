<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Mgilet\NotificationBundle\Annotation\Notifiable;
use Mgilet\NotificationBundle\NotifiableInterface;

use \App\Entity\Friendship;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields = {"email"},
 *  errorPath="email",
 *  message = "email already in use.",
 * )
 * @UniqueEntity(
 *  fields = {"username"},
 *  errorPath="username",
 *  message = "username already in use.",
 * )
 * @Notifiable(name="User")
 */
class User implements UserInterface, NotifiableInterface
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
     * @ORM\Column(type="string", length=255, nullable=true)
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

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $currentLocation = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bio;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friendship", mappedBy="user", orphanRemoval=true)
     */
    private $friendships;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="author", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rating", mappedBy="author", orphanRemoval=true)
     */
    private $ratings;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mmr;

    public function __construct()
    {
        $this->setCurrentLocation();
        $this->roles[] = 'ROLE_USER';
        $this->friendships = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function __toString(): ?string
    {
        return $this->username;
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

    public function setMood(string $mood = null): self
    {
        if ($mood == null) {
            $this->mood = 'I like trains';
        } else {
            $this->mood = $mood;
        }

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
        if ($this->confirmPassword == null) {
            return $this->password;
        }
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

    public function eraseCredentials()
    { }

    public function getSalt()
    { }

    public function getRoles()
    {
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

    public function getCurrentLocation(): ?array
    {
        return $this->currentLocation;
    }

    /**
     * set user location with Mapbox API
     *
     * @param string|null $coord
     * @return self
     */
    public function setCurrentLocation(?string $coord = null): self
    {
        if ($coord != null) {
            $data = json_decode(file_get_contents("https://api.mapbox.com/geocoding/v5/mapbox.places/{$coord}.json?access_token=pk.eyJ1IjoidG9udG9uc2F0IiwiYSI6ImNqc25jNTIwNjA5bDc0M280dGt4ejJtNXkifQ.h_Ox7WHHtfhpQK9Qr0oTlw"));

            if(!empty($data)) {
                $this->currentLocation['city'] = $data->features[2]->text;
                $this->currentLocation['state'] = $data->features[3]->text;
                $this->currentLocation['country'] = $data->features[4]->text;
                $this->currentLocation['coord'] = $coord;
            }
        }
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    /** FRIENDSHIP */
    /**
     * @return Collection|Friendship[]
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function addFriendship(Friendship $friendship): self
    {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships[] = $friendship;
            $friendship->getfriend()->addFriendship($friendship);
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship): self
    {
        if ($this->friendships->contains($friendship)) {
            $this->friendships->removeElement($friendship);
            // set the owning side to null (unless already changed)
            if ($friendship->getUser() === $this) {
                $friendship->setUser(null);
            }
        }

        return $this;
    }

    public function addFriend(User $friend)
    {
        $fs = new Friendship();
        $fs->setStatus(0)
            ->setDate(new \Datetime);
        $fs->setUser($this);
        $fs->setFriend($friend);

        $this->addFriendship($fs);

        return $fs;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setAuthor($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getAuthor() === $this) {
                $vote->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setAuthor($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->contains($rating)) {
            $this->ratings->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getAuthor() === $this) {
                $rating->setAuthor(null);
            }
        }

        return $this;
    }

    public function getMmr(): ?int
    {
        return $this->mmr;
    }

    public function setMmr(?int $mmr): self
    {
        $this->mmr = $mmr;

        return $this;
    }

    public function addMmr(): self
    {
        $this->mmr += 1;

        return $this;
    }

    public function removeMmr(): self
    {
        $this->mmr -= 1;

        return $this;
    }
}
