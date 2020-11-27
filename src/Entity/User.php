<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email",
 *      message="Un compte est déjà enregistré avec cet e-mail"
 * )
 * @UniqueEntity("username",
 *      message="Ce nom d'utilisateur est déjà prit"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Uuid(strict=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *      groups={"register"},
     *      min = 3,
     *      max = 50,
     *      minMessage = "Le nom d'utilisateur doit faire au moins {{ limit }} caractères",
     *      maxMessage = "Le nom d'utilisateur doit faire au plus {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     * @Assert\NotNull
     * @Assert\NotBlank(groups={"register"})
     */
    private $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern = "^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$^",
     *     message="Le mot de passe doit faire au minimum 8 caractères, avec une majuscule, une minuscule, un chiffre et un symbole."
     * )
     * @Assert\NotBlank(groups={"register"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas une adresse e-mail valide.",
     *     groups={"registration", "forgot_password"}
     * )
     * @Assert\NotBlank(groups={"forgot_password", "register"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\LessThanOrEqual("-13 years", message="Vous devez être âgé d'au moins 13 ans pour vous inscrire.")
     */
    private $birthday;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $followers_nb;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $following_nb;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $publications;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $private;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $banned;

    /**
     * @Assert\IsTrue(message="Vous devez accepter les conditions générales d'utilisations.")
     */
    private $cgu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="author")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     */
    private $posts;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Bookmark::class, mappedBy="user")
     */
    private $bookmarks;

    /**
     * @ORM\ManyToMany(targetEntity=IdentificationPost::class, mappedBy="user")
     */
    private $identificationPosts;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $followers = [];

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $following = [];

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $await_follow = [];

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="dest")
     */
    private $notifications;

    public function __construct()
    {
        $this->comments             = new ArrayCollection();
        $this->bookmarks            = new ArrayCollection();
        $this->identificationPosts  = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function getCgu(): ?bool
    {
        return $this->cgu;
    }

    public function setCgu(bool $cgu): self
    {   

        $this->cgu = $cgu;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getFollowers_nb(): ?int
    {
        return $this->followers_nb;
    }

    public function setFollowers_nb(?int $followers): self
    {
        $this->followers_nb = $followers;

        return $this;
    }

    public function getFollowing_nb(): ?int
    {
        return $this->following_nb;
    }

    public function setFollowing_nb(?int $following): self
    {
        $this->following_nb = $following;

        return $this;
    }

    public function getPublications(): ?int
    {
        return $this->publications;
    }

    public function setPublications(?int $publications): self
    {
        $this->publications = $publications;

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(?bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    public function getBanned(): ?bool
    {
        return $this->banned;
    }

    public function setBanned(?bool $banned): self
    {
        $this->banned = $banned;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setAuthor($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getAuthor() === $this) {
                $like->setAuthor(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isFollowedBy($userID)
    {
        if (!empty($this->followers)) {
            return in_array($userID, $this->followers);
        } return false;
    }

    /**
     * @return Collection|Bookmark[]
     */
    public function getBookmarks(): Collection
    {
        return $this->bookmarks;
    }

    public function addBookmark(Bookmark $bookmark): self
    {
        if (!$this->bookmarks->contains($bookmark)) {
            $this->bookmarks[] = $bookmark;
            $bookmark->setUser($this);
        }

        return $this;
    }

    public function removeBookmark(Bookmark $bookmark): self
    {
        if ($this->bookmarks->contains($bookmark)) {
            $this->bookmarks->removeElement($bookmark);
            // set the owning side to null (unless already changed)
            if ($bookmark->getUser() === $this) {
                $bookmark->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IdentificationPost[]
     */
    public function getIdentificationPosts(): Collection
    {
        return $this->identificationPosts;
    }

    public function addIdentificationPost(IdentificationPost $identificationPost): self
    {
        if (!$this->identificationPosts->contains($identificationPost)) {
            $this->identificationPosts[] = $identificationPost;
            $identificationPost->addUser($this);
        }

        return $this;
    }

    public function removeIdentificationPost(IdentificationPost $identificationPost): self
    {
        if ($this->identificationPosts->contains($identificationPost)) {
            $this->identificationPosts->removeElement($identificationPost);
            $identificationPost->removeUser($this);
        }

        return $this;
    }

    public function getFollowers(): ?array
    {
        return $this->followers;
    }

    public function setFollowers(?array $followers): self
    {
        $this->followers = $followers;

        return $this;
    }

    public function getFollowing(): ?array
    {
        return $this->following;
    }

    public function setFollowing(?array $following): self
    {
        $this->following = $following;

        return $this;
    }

    public function getAwaitFollow(): ?array
    {
        return $this->await_follow;
    }

    public function setAwaitFollow(?array $await_follow): self
    {
        $this->await_follow = $await_follow;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setDest($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getDest() === $this) {
                $notification->setDest(null);
            }
        }

        return $this;
    }

    /**
     * Permet de connaître le nombre de notifications non lues
     * 
     */
    public function countNotificationsNotRead(): int
    {
        $count = 0;
        foreach ($this->notifications as $notification) {
            if ($notification->getOpened() == false) $count++;
        }
        return $count;
    }
}
