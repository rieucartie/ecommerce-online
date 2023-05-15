<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $datecommande;

    /**
     * @ORM\Column(type="boolean", nullable=true,options = {"default" = "0"})
     */
    private int $valider = 0;

    /**
     * @ORM\Column(type="string")
     */
    private string $state = "pending";
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $canceledAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $updateAt = null;

    /**
     * @var Collection<int, Line>
     * @ORM\OneToMany(targetEntity=Line::class, mappedBy="order", cascade={"persist"})
     */
    private Collection $lines;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     */
    private  $user;

    public function __construct()
    {
        $this->datecommande = new DateTimeImmutable();
        $this->lines = new ArrayCollection();
        $this->valider=0;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdateAt(): ?DateTimeImmutable
    {
        return $this->updateAt;
    }

    /**
     * @param DateTimeImmutable|null $updateAt
     */
    public function setUpdateAt(?DateTimeImmutable $updateAt): void
    {
        $this->updateAt = $updateAt;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCanceledAt(): ?DateTimeImmutable
    {
        return $this->canceledAt;
    }

    /**
     * @param DateTimeImmutable|null $canceledAt
     */
    public function setCanceledAt(?DateTimeImmutable $canceledAt): void
    {
        $this->canceledAt = $canceledAt;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateCommande(): DateTimeImmutable
    {
        return $this->datecommande;
    }



    /**
     * @return int
     */
    public function getValider(): int
    {
        return $this->valider;
    }

    /**
     * @param int $valider
     */
    public function setValider(int $valider): void
    {
        $this->valider = $valider;
    }


    /**
     * @return Collection
     */
    public function getLines(): ArrayCollection|Collection
    {
        return $this->lines;
    }

    /**
     * @param Collection $lines
     */
    public function setLines(ArrayCollection|Collection $lines): void
    {
        $this->lines = $lines;
    }


    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }



}
