<?php

namespace App\Entity;

use App\Repository\SideImgRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SideImgRepository::class)
 */
class SideImg
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "La position doit contenir au moins {{ limit }} caractères.",
     *      allowEmptyString = false
     * )
     * @Assert\NotBlank
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Le nom de l'anime doit contenir au moins {{ limit }} caractères.",
     *      allowEmptyString = false
     * )
     * @Assert\NotBlank
     */
    private $anime;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\Length(
     *      min = 30,
     *      minMessage = "Le lien doit contenir au moins {{ limit }} caractères.",
     *      allowEmptyString = false
     * )
     * @Assert\NotBlank
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getAnime(): ?string
    {
        return $this->anime;
    }

    public function setAnime(string $anime): self
    {
        $this->anime = $anime;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }
}
