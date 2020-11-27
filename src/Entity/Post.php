<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="json")
     */
    private $tags = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pass;

    /**
     * @ORM\ManyToOne(targetEntity=Filters::class, inversedBy="posts")
     */
    private $filters;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="post", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $like_nb;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comment_nb;

    /**
     * @ORM\OneToMany(targetEntity=Bookmark::class, mappedBy="post")
     */
    private $bookmarks;

    /**
     * @ORM\OneToMany(targetEntity=IdentificationPost::class, mappedBy="post")
     */
    private $identificationPosts;

    /**
     * @ORM\Column(type="datetime")
     */
    private $moment;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->identificationPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
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

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getFilters(): ?Filters
    {
        return $this->filters;
    }

    public function setFilters(?Filters $filters): self
    {
        $this->filters = $filters;

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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * Permet de savoir si l'article est commenté par l'utilisateur connecté
     * 
     * @param \App\Entity\User $user
     * @return boolean
     */
    public function isCommentedByUser(User $user): bool
    {
        foreach ($this->comments as $comment) {
            if ($comment->getAuthor() === $user) return true;
        }
        return false;
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
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * Permet de savoir si l'article est liké par l'utilisateur connecté
     * 
     * @param \App\Entity\User $user
     * @return boolean
     */
    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likes as $like) {
            if ($like->getAuthor() === $user) return true;
        }
        return false;
    }

    /**
     * Permet de savoir si l'article est bookmarké par l'utilisateur connecté
     * 
     * @param \App\Entity\User $user
     * @return boolean
     */
    public function isBookmarkedByUser(User $user): bool
    {
        foreach ($this->bookmarks as $bookmark) {
            if ($bookmark->getUser() === $user) return true;
        }
        return false;
    }

    public function getLikeNb(): ?int
    {
        return $this->like_nb;
    }

    public function setLikeNb(?int $like_nb): self
    {
        $this->like_nb = $like_nb;

        return $this;
    }

    public function getCommentNb(): ?int
    {
        return $this->comment_nb;
    }

    public function setCommentNb(?int $comment_nb): self
    {
        $this->comment_nb = $comment_nb;

        return $this;
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
            $bookmark->setPost($this);
        }

        return $this;
    }

    public function removeBookmark(Bookmark $bookmark): self
    {
        if ($this->bookmarks->contains($bookmark)) {
            $this->bookmarks->removeElement($bookmark);
            // set the owning side to null (unless already changed)
            if ($bookmark->getPost() === $this) {
                $bookmark->setPost(null);
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
            $identificationPost->setPost($this);
        }

        return $this;
    }

    public function removeIdentificationPost(IdentificationPost $identificationPost): self
    {
        if ($this->identificationPosts->contains($identificationPost)) {
            $this->identificationPosts->removeElement($identificationPost);
            // set the owning side to null (unless already changed)
            if ($identificationPost->getPost() === $this) {
                $identificationPost->setPost(null);
            }
        }

        return $this;
    }

    public function getMoment(): ?\DateTimeInterface
    {
        return $this->moment;
    }

    public function setMoment(\DateTimeInterface $moment): self
    {
        $this->moment = $moment;

        return $this;
    }
}
