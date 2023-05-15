<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Commande;
use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LineRepository::class)
 * @ORM\Table(name="order_line")
 */
class Line
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="lines")
     * @ORM\JoinColumn(nullable=false)
     */
    private Commande $order;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Product $product;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private int $amount;


    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private int $quantity = 0;

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): Commande
    {
        return $this->order;
    }

    public function setOrder(Commande $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
       // $this->amount = $products->getAmount();
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }


    public function getQuantity(): int
    {
        return $this->quantity;
    }


}
