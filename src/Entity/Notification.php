<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dest;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $nature;

    /**
     * @ORM\Column(type="datetime")
     */
    private $moment;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class)
     */
    private $attach;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $opened;

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

    public function getDest(): ?User
    {
        return $this->dest;
    }

    public function setDest(?User $dest): self
    {
        $this->dest = $dest;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): self
    {
        $this->nature = $nature;

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

    public function getAttach(): ?Post
    {
        return $this->attach;
    }

    public function setAttach(?Post $attach): self
    {
        $this->attach = $attach;

        return $this;
    }

    public function getOpened(): ?bool
    {
        return $this->opened;
    }

    public function setOpened(?bool $opened): self
    {
        $this->opened = $opened;

        return $this;
    }

    /**
     * Permet de connaître le nombre de notifications non lues
     * 
     * @param \App\Entity\User $user
     * @return integer
     */
    public function countNotificationsNotRead(): integer
    {
        $count = 0;
        foreach ($this->notifications as $notification) {
            if ($notification->getOpened() === true) $count++;
        }
        return $count;
    }
}
